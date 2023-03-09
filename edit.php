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

use mod_margic\local\helper;
use \mod_margic\event\invalid_access_attempt;
use core\output\notification;
use mod_margic\output\margic_entry;

require(__DIR__.'/../../config.php');
require_once('./edit_form.php');
require_once($CFG->dirroot . '/mod/margic/locallib.php');

global $DB;

// Course Module ID.
$id = required_param('id', PARAM_INT);

// Module instance ID as alternative.
$m = optional_param('m', null, PARAM_INT);

// ID of the entry to be edited (if existing).
$entryid = optional_param('entryid', '0', PARAM_INT);

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

// Prevent creating and editing of entries if user is not allowed to edit entry or activity is not available.
if (($entryid && !$moduleinstance->editentries) || !helper::margic_available($moduleinstance)) {
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
if ($entryid) {
    $title = get_string('editentry', 'mod_margic');
} else {
    $title = get_string('addnewentry', 'mod_margic');
}

$data = new stdClass();
$data->id = $cm->id;

// Get the single record specified by firstkey.
if ($DB->record_exists('margic_entries', array('margic' => $moduleinstance->id, "id" => $entryid))) {
    $entry = $DB->get_record('margic_entries', array('margic' => $moduleinstance->id, "id" => $entryid));

    $notnewestentry = false;
    // Prevent editing of entries that are not the newest version of a base entry or a unedited entry.
    if (isset($entry->baseentry)) { // If entry has a base entry check if this entry is the newest childentry.
        $otherchildentries = $DB->get_records('margic_entries',
            array('margic' => $moduleinstance->id, 'baseentry' => $entry->baseentry), 'timecreated DESC');

        if ($entry->timecreated < $otherchildentries[array_key_first($otherchildentries)]->timecreated) {
            $notnewestentry = true;
        }
    } else { // If this entry has no base entry check if it has childentries and cant therefore be edited.
        $childentries = $DB->get_records('margic_entries', array('margic' => $moduleinstance->id, 'baseentry' => $entry->id),
            'timecreated DESC');

        if (!empty($childentries)) {
            $notnewestentry = true;
        }
    }

    // Prevent editing of entries not started by this user or if it is not the newest child entry.
    if ($entry->userid != $USER->id || $notnewestentry) {
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

    $PAGE->requires->js_call_amd('mod_margic/annotations', 'init',
        array('cmid' => $cm->id, 'canmakeannotations' => false, 'myuserid' => $USER->id));
} else {
    $entry = false;

    $data->entryid = null;
    $data->timecreated = time();
    $data->text = '';
    $data->textformat = FORMAT_HTML;
}

list ($editoroptions, $attachmentoptions) = helper::margic_get_editor_and_attachment_options($course, $context, $moduleinstance);

$data = file_prepare_standard_editor($data, 'text', $editoroptions, $context, 'mod_margic', 'entry', $data->entryid);
$data = file_prepare_standard_filemanager($data, 'attachment', $attachmentoptions, $context,
    'mod_margic', 'attachment', $data->entryid);

// Create form.
$form = new mod_margic_entry_form(null, array('margic' => $moduleinstance->editentrydates, 'editoroptions' => $editoroptions));

// Set existing data for this entry.
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($CFG->wwwroot . '/mod/margic/view.php?id=' . $cm->id);
} else if ($fromform = $form->get_data()) {
    $timenow = time();

    // Relink using the proper entryid because draft area didn't have an itemid associated when creating new entry.
    $newentry = new stdClass();
    $newentry->margic = $moduleinstance->id;
    $newentry->userid = $USER->id;

    if ($moduleinstance->editentrydates) {
        $newentry->timecreated = $fromform->timecreated;
        $newentry->timemodified = $fromform->timecreated;
    } else {
        $newentry->timecreated = $timenow;
        $newentry->timemodified = $timenow;
    }


    $newentry->text = '';
    $newentry->format = 1;

    if ($fromform->entryid != 0 && $entry != false) { // If existing entry is edited.
        if (!isset($entry->baseentry)) {
            $newentry->baseentry = $fromform->entryid;
        } else {
            $newentry->baseentry = $entry->baseentry;
        }

        // Check if timecreated is not older then connected entries.
        if ($moduleinstance->editentrydates) {

            $baseentry = $DB->get_record('margic_entries', array('margic' => $moduleinstance->id, "id" => $newentry->baseentry));

            if ($newentry->timecreated < $baseentry->timemodified) {
                redirect(new moodle_url('/mod/margic/view.php?id=' . $cm->id), get_string('timecreatedinvalid', 'mod_margic'),
                    null, notification::NOTIFY_ERROR);
            }

            $connectedentries = $DB->get_records('margic_entries',
                array('margic' => $moduleinstance->id, 'baseentry' => $newentry->baseentry), 'timecreated DESC');

            if ($connectedentries && $newentry->timecreated < $connectedentries[array_key_first($connectedentries)]->timecreated) {
                redirect(new moodle_url('/mod/margic/view.php?id=' . $cm->id), get_string('timecreatedinvalid', 'mod_margic'),
                    null, notification::NOTIFY_ERROR);
            }

        }

        // Update timemodified for base entry.
        $baseentry = $DB->get_record('margic_entries', array('margic' => $moduleinstance->id, "id" => $newentry->baseentry));
        $baseentry->timemodified = $newentry->timecreated;
        $DB->update_record('margic_entries', $baseentry);
    }

    if (! $newentry->id = $DB->insert_record("margic_entries", $newentry)) {
        throw new moodle_exception(get_string('generalerrorinsert', 'margic'));
    }

    $fromform = file_postupdate_standard_editor($fromform, 'text', $editoroptions, $editoroptions['context'],
        'mod_margic', 'entry', $newentry->id);

    $entrytext = file_rewrite_pluginfile_urls($fromform->text, 'pluginfile.php', $context->id,
        'mod_margic', 'entry', $newentry->id);

    $newentry->text = format_text($entrytext, $fromform->textformat, array('para' => false));
    $newentry->format = $fromform->textformat;

    $DB->update_record('margic_entries', $newentry);

    if ($entry && $fromform->entryid) {
        // Trigger module entry updated event.
        $event = \mod_margic\event\entry_updated::create(array(
            'objectid' => $newentry->id,
            'context' => $context
        ));
    } else {
        // Trigger module entry created event.
        $event = \mod_margic\event\entry_created::create(array(
            'objectid' => $newentry->id,
            'context' => $context
        ));
    }

    $event->trigger();

    if ($moduleinstance->editentrydates && $fromform->timecreated > $timenow) {
        redirect(new moodle_url('/mod/margic/view.php?id=' . $cm->id), get_string('entryaddedoredited', 'mod_margic') .
            ' ' . get_string('editdateinfuture', 'mod_margic'), null, notification::NOTIFY_WARNING);
    } else {
        redirect(new moodle_url('/mod/margic/view.php?id=' . $cm->id), get_string('entryaddedoredited', 'mod_margic'),
            null, notification::NOTIFY_SUCCESS);
    }

}

$PAGE->set_url('/mod/margic/edit.php', array('id' => $id));
$PAGE->navbar->add($title);
$PAGE->set_title(format_string($moduleinstance->name) . ' - ' . $title);
$PAGE->set_heading($course->fullname);
if ($CFG->branch < 400) {
    $PAGE->force_settings_menu();
}

echo $OUTPUT->header();

if ($CFG->branch < 400) {
    echo $OUTPUT->heading(format_string($moduleinstance->name));

    if ($moduleinstance->intro) {
        echo $OUTPUT->box(format_module_intro('margic', $moduleinstance, $cm->id), 'generalbox', 'intro');
    }
}

echo $OUTPUT->heading($title, 4);

// If existing entry is edited render entry.
if ($entry) {
    $edittimes = helper::margic_get_edittime_options($moduleinstance);

    $grades = make_grades_menu($moduleinstance->scale); // For select in grading_form.

    $currentgroups = groups_get_activity_group($cm, true);    // Get a list of the currently allowed groups for this course.

    if ($currentgroups) {
        $allowedusers = get_users_by_capability($context, 'mod/margic:addentries', '', $sort = 'lastname ASC, firstname ASC',
            '', '', $currentgroups);
    } else {
        $allowedusers = true;
    }

    $strmanager = get_string_manager();

    $gradingstr = get_string('needsgrading', 'margic');
    $regradingstr = get_string('needsregrading', 'margic');

    if ($entry->baseentry) { // If edited entry is child entry get base entry for rendering.
        $entry = $DB->get_record('margic_entries', array('margic' => $moduleinstance->id, "id" => $entry->baseentry));
    }

    $page = new margic_entry($margic, $cm, $context, $moduleinstance, $entry, $margic->get_annotationarea_width(),
        $moduleinstance->editentries, $edittimes->edittimestarts, $edittimes->edittimenotstarted, $edittimes->edittimeends,
        $edittimes->edittimehasended, has_capability('mod/margic:manageentries', $context), $course, false, true, false,
        false, true, $grades, $currentgroups, $allowedusers, $strmanager, $gradingstr, $regradingstr, sesskey(), true);

    echo $OUTPUT->render($page);
}

// Display the form for editing the entry.
$form->display();

echo $OUTPUT->footer();
