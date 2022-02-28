<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * The page for the edit entry form in mod_margic.
 *
 * @package     mod_margic
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_margic\local\results;
use \mod_margic\event\invalid_access_attempt;
use core\output\notification;

require_once("../../config.php");
require_once('./edit_form.php');
require_once($CFG->dirroot . '/mod/margic/locallib.php');

global $DB;

// Course Module ID.
$id = required_param('id', PARAM_INT);

// Module instance ID as alternative.
$m  = optional_param('d', null, PARAM_INT);

// ID of the entry to be edited (if existing).
$entryid = optional_param('entryid', '', PARAM_INT);

$margic = margic::get_margic_instance($id, $m, false, 'currententry', 0, 1);

$moduleinstance = $margic->get_module_instance();
$course = $margic->get_course();
$context = $margic->get_context();
$cm = $margic->get_course_module();

if (! $cm) {
    throw new moodle_exception(get_string('incorrectmodule', 'margic'));
}

if (! $course) {
    throw new moodle_exception(get_string('incorrectcourseid', 'margic'));
}

if (! $coursesections = $DB->get_record("course_sections", array(
    "id" => $cm->section
))) {
    throw new moodle_exception(get_string('incorrectmodule', 'margic'));
}

require_login($course, true, $cm);

require_capability('mod/margic:addentries', $context);

// Prevent creating and editing of entries when activity is closed.
$timenow = time();
if ($course->format == 'weeks' and $moduleinstance->days) {
    $timestart = $course->startdate + (($coursesections->section - 1) * 604800);
    if ($moduleinstance->days) {
        $timefinish = $timestart + (3600 * 24 * $moduleinstance->days);
    } else {
        $timefinish = $course->enddate;
    }
} else if (! ((($moduleinstance->timeopen == 0 || time() >= $moduleinstance->timeopen)
    && ($moduleinstance->timeclose == 0 || time() < $moduleinstance->timeclose)))) { // If margic is not available?
    // If used, set calendar availability time limits on the margics.
    $timestart = $moduleinstance->timeopen;
    $timefinish = $moduleinstance->timeclose;
    $moduleinstance->days = 0;
} else {
    // Have no time limits on the margics.
    $timestart = false;
    $timefinish = false;
}

if (!$moduleinstance->editall && $timefinish && (time() > $timefinish)) {
    // Trigger invalid_access_attempt with redirect to the view page.
    $params = array(
        'objectid' => $id,
        'context' => $context,
        'other' => array(
            'file' => 'edit.php'
        )
    );
    $event = invalid_access_attempt::create($params);
    $event->trigger();
    redirect('view.php?id='.$id, get_string('editentrynotpossible', 'margic'), null, notification::NOTIFY_ERROR);
}

// Header.
$PAGE->set_url('/mod/margic/edit.php', array('id' => $id));
$PAGE->navbar->add(get_string('startoreditentry', 'mod_margic'));
$PAGE->set_title(format_string($moduleinstance->name) . ' - ' . get_string('startoreditentry', 'mod_margic'));
$PAGE->set_heading($course->fullname);

$data = new stdClass();
$data->id = $cm->id;

// Get the single record specified by firstkey.
if (isset($margic->get_entries_with_keys()[$entryid])) {
    $entry = $margic->get_entries_with_keys()[$entryid];

    // Prevent editing of entries not started by this user.
    if ($entry->userid != $USER->id) {
        // Trigger invalid_access_attempt with redirect to the view page.
        $params = array(
            'objectid' => $id,
            'context' => $context,
            'other' => array(
                'file' => 'edit.php'
            )
        );
        $event = invalid_access_attempt::create($params);
        $event->trigger();
        redirect('view.php?id='.$id, get_string('editentrynotpossible', 'margic'), null, notification::NOTIFY_ERROR);
    }

    $data->entryid = $entry->id;
    $data->timecreated = $entry->timecreated;
    $data->text = $entry->text;
    $data->textformat = $entry->format;
} else {
    $entry = false;

    $data->entryid = null;
    $data->timecreated = time();
    $data->text = '';
    $data->textformat = FORMAT_HTML;
}

list ($editoroptions, $attachmentoptions) = results::margic_get_editor_and_attachment_options($course, $context, $moduleinstance);

$data = file_prepare_standard_editor($data, 'text', $editoroptions, $context, 'mod_margic', 'entry', $data->entryid);
$data = file_prepare_standard_filemanager($data, 'attachment', $attachmentoptions, $context, 'mod_margic', 'attachment', $data->entryid);

// Create form.
$form = new mod_margic_entry_form(null, array('margic' => $moduleinstance->editdates, 'editoroptions' => $editoroptions, 'attachmentoptions' => $attachmentoptions));

// Set existing data for this entry.
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($CFG->wwwroot . '/mod/margic/view.php?id=' . $cm->id);
} else if ($fromform = $form->get_data()) {
    $timenow = time();

    // Prevent creation dates in the future.
    if ($moduleinstance->editdates && $fromform->timecreated > $timenow) {
        redirect('view.php?id='.$id, get_string('entrydateinfuture', 'margic'), null, notification::NOTIFY_ERROR);
    }

    // Relink using the proper entryid because draft area didn't have an itemid associated when creating new entry.
    $newentry = new stdClass();
    $newentry->margic = $moduleinstance->id;
    $newentry->userid = $USER->id;

    $newentry->timecreated = $fromform->timecreated;

    $newentry->timemodified = 0;

    $newentry->text = '';
    $newentry->format = 1;
    if ($fromform->entryid != 0 && $entry != false) {

        $newentry->id = $fromform->entryid;

        $newentry->entrycomment = $entry->entrycomment;
        $newentry->teacher = $entry->teacher;
        $newentry->timemodified = $timenow;
        $newentry->timemarked = $entry->timemarked;
        $newentry->timecreated = $entry->timecreated;
    } else {
        if (! $newentry->id = $DB->insert_record("margic_entries", $newentry)) {
            throw new moodle_exception(get_string('generalerrorinsert', 'margic'));
        }
    }


    $fromform = file_postupdate_standard_editor($fromform, 'text', $editoroptions, $editoroptions['context'], 'mod_margic', 'entry', $newentry->id);

    $entrytext = file_rewrite_pluginfile_urls($fromform->text, 'pluginfile.php', $context->id, 'mod_margic', 'entry', $newentry->id);

    $newentry->text = format_text($entrytext, $fromform->textformat, array('para' => false));
    $newentry->format = $fromform->textformat;

    $DB->update_record('margic_entries', $newentry);

    if ($entry && $fromform->entryid) {
        // Trigger module entry updated event.
        $event = \mod_margic\event\entry_updated::create(array(
            'objectid' => $moduleinstance->id,
            'context' => $context
        ));
    } else {
        // Trigger module entry created event.
        $event = \mod_margic\event\entry_created::create(array(
            'objectid' => $moduleinstance->id,
            'context' => $context
        ));
    }

    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('margic', $moduleinstance);
    $event->trigger();

    redirect(new moodle_url('/mod/margic/view.php?id=' . $cm->id));
}

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($moduleinstance->name));

$intro = format_module_intro('margic', $moduleinstance, $cm->id);
echo $OUTPUT->box($intro);

echo $OUTPUT->heading(get_string('startoreditentry', 'mod_margic'), 3);

// Otherwise fill and print the form.
$form->display();

echo $OUTPUT->footer();
