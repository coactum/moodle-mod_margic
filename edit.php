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
 * This page opens the current instance of a margic entry for editing.
 *
 * @package   mod_margic
 * @copyright 2019 AL Rachels (drachels@drachels.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use mod_margic\local\results;
use \mod_margic\event\invalid_access_attempt;

require_once("../../config.php");
require_once('lib.php'); // May not need this.
require_once('./edit_form.php');
global $DB;
$id = required_param('id', PARAM_INT); // Course Module ID.
$action = optional_param('action', 'currententry', PARAM_ACTION); // Action(default to current entry).
$firstkey = optional_param('firstkey', '', PARAM_INT); // Which margic_entries id to edit.

if (! $cm = get_coursemodule_from_id('margic', $id)) {
    throw new moodle_exception(get_string('incorrectmodule', 'margic'));
}

if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
    throw new moodle_exception(get_string('incorrectcourseid', 'margic'));
}

$context = context_module::instance($cm->id);

require_login($course, false, $cm);

require_capability('mod/margic:addentries', $context);

if (! $margic = $DB->get_record("margic", array("id" => $cm->instance))) {
    throw new moodle_exception(get_string('incorrectcourseid', 'margic'));
}

// 20210613 Added check to prevent direct access to create new entry when activity is closed.
if (($margic->timeclose) && (time() > $margic->timeclose)) {
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
    redirect('view.php?id='.$id, get_string('invalidaccessexp', 'margic'));
}

// Header.
$PAGE->set_url('/mod/margic/edit.php', array('id' => $id));
$PAGE->navbar->add(get_string('edit'));
$PAGE->set_title(format_string($margic->name));
$PAGE->set_heading($course->fullname);

$data = new stdClass();

$parameters = array(
    'userid' => $USER->id,
    'margic' => $margic->id,
    'action' => $action,
    'firstkey' => $firstkey
);

// Get the single record specified by firstkey.
$entry = $DB->get_record("margic_entries", array(
    "userid" => $USER->id,
    'id' => $firstkey
));

if ($action == 'currententry' && $entry) {
    $data->entryid = $entry->id;
    $data->timecreated = $entry->timecreated;
    $data->text = $entry->text;
    $data->textformat = $entry->format;

    // Check the timecreated of the current entry to see if now is a new calendar day .
    // 20210425 If can edit dates, just start a new entry.
    if ((strtotime('today midnight') > $entry->timecreated) || ($action == 'currententry' && $margic->editdates)) {
        $entry = '';
        $data->entryid = null;
        $data->timecreated = time();
        $data->text = '';
        $data->textformat = FORMAT_HTML;
    }
} else if ($action == 'editentry' && $entry) {
    $data->entryid = $entry->id;
    $data->timecreated = $entry->timecreated;
    $data->text = $entry->text;
    $data->textformat = $entry->format;
    // Think I might need to add a check for currententry && !entry to justify starting a new entry, else error.
} else if ($action == 'currententry' && ! $entry) {
    // There are no entries for this user, so start the first one.
    $data->entryid = null;
    $data->timecreated = time();
    $data->text = '';
    $data->textformat = FORMAT_HTML;
} else {
    throw new moodle_exception(get_string('generalerror', 'margic'));
}

$data->id = $cm->id;

list ($editoroptions, $attachmentoptions) = results::margic_get_editor_and_attachment_options($course,
                                                                                             $context,
                                                                                             $margic,
                                                                                             $entry,
                                                                                             $action,
                                                                                             $firstkey);

$data = file_prepare_standard_editor($data,
                                     'text',
                                     $editoroptions,
                                     $context,
                                     'mod_margic',
                                     'entry',
                                     $data->entryid);
$data = file_prepare_standard_filemanager($data,
                                          'attachment',
                                          $attachmentoptions,
                                          $context,
                                          'mod_margic',
                                          'attachment',
                                          $data->entryid);

// 20201119 Added $margic->editdates setting.
$form = new mod_margic_entry_form(null, array(
    'current' => $data,
    'cm' => $cm,
    'margic' => $margic->editdates,
    'editoroptions' => $editoroptions,
    'attachmentoptions' => $attachmentoptions
));

// Set existing data loaded from the database for this entry.
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($CFG->wwwroot . '/mod/margic/view.php?id=' . $cm->id);
} else if ($fromform = $form->get_data()) {
    // If data submitted, then process and store, contains text, format, and itemid.
    // Prevent CSFR.
    confirm_sesskey();
    $timenow = time();

    // This will be overwritten after we have the entryid.
    $newentry = new stdClass();
    $newentry->timecreated = $fromform->timecreated;
    $newentry->timemodified = $timenow;
    $newentry->text = $fromform->text_editor['text'];
    $newentry->format = $fromform->text_editor['format'];

    if (! $margic->editdates) {
        // If editdates is NOT enabled do attempted cheat testing here.
        // 20210619 Before we update, see if there is an entry in database with the same entryid.
        $entry = $DB->get_record("margic_entries", array(
            "userid" => $USER->id,
            'id' => $fromform->entryid
        ));
    }

    // 20210619 If user tries to change timecreated, prevent it.
    // TODO: Need to move new code to up to just after getting $entry, to make a nested if.
    // Currently not taking effect on the overall user grade unless the teacher rates it.
    if ($fromform->entryid) {
        $newentry->id = $fromform->entryid;
        if (($entry) && (!($entry->timecreated == $newentry->timecreated))) {
            // 20210620 New code to prevent attempts to change timecreated.
            $newentry->entrycomment = get_string('invalidtimechange', 'margic');
            $newentry->entrycomment .= get_string('invalidtimechangeoriginal', 'margic', ['one' => userdate($entry->timecreated)]);
            $newentry->entrycomment .= get_string('invalidtimechangenewtime', 'margic', ['one' => userdate($newentry->timecreated)]);
            // Probably do not want to just arbitraily set a rating.
            // Should leave it up to the teacher, otherwise will need to acertain rating settings for the activity.
            // @codingStandardsIgnoreLine
            // $newentry->rating = 1;
            $newentry->teacher = 2;
            $newentry->timemodified = time();
            $newentry->timemarked = time();
            $newentry->timecreated = $entry->timecreated;
            $fromform->timecreated = $entry->timecreated;
            $newentry->entrycomment .= get_string('invalidtimeresettime', 'margic', ['one' => userdate($newentry->timecreated)]);
            $DB->update_record("margic_entries", $newentry);
            // Trigger module entry updated event.
            $event = \mod_margic\event\invalid_entry_attempt::create(array(
                'objectid' => $margic->id,
                'context' => $context
            ));
            $event->add_record_snapshot('course_modules', $cm);
            $event->add_record_snapshot('course', $course);
            $event->add_record_snapshot('margic', $margic);
            $event->trigger();

            redirect(new moodle_url('/mod/margic/view.php?id=' . $cm->id));
            die();
        }
        if (! $DB->update_record("margic_entries", $newentry)) {
            throw new moodle_exception(get_string('generalerrorupdate', 'margic'));
        }
    } else {
        $newentry->userid = $USER->id;
        $newentry->margic = $margic->id;
        if (! $newentry->id = $DB->insert_record("margic_entries", $newentry)) {
            throw new moodle_exception(get_string('generalerrorinsert', 'margic'));
        }
    }

    // Relink using the proper entryid.
    // We need to do this as draft area didn't have an itemid associated when creating the entry.
    $fromform = file_postupdate_standard_editor($fromform,
                                                'text',
                                                $editoroptions,
                                                $editoroptions['context'],
                                                'mod_margic',
                                                'entry',
                                                $newentry->id);
    $newentry->text = $fromform->text;
    $newentry->format = $fromform->textformat;
    $newentry->timecreated = $fromform->timecreated;

    $DB->update_record('margic_entries', $newentry);

    if ($entry) {
        // Trigger module entry updated event.
        $event = \mod_margic\event\entry_updated::create(array(
            'objectid' => $margic->id,
            'context' => $context
        ));
    } else {
        // Trigger module entry created event.
        $event = \mod_margic\event\entry_created::create(array(
            'objectid' => $margic->id,
            'context' => $context
        ));
    }
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('margic', $margic);
    $event->trigger();

    redirect(new moodle_url('/mod/margic/view.php?id=' . $cm->id));
    die();
}

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($margic->name));

$intro = format_module_intro('margic', $margic, $cm->id);
echo $OUTPUT->box($intro);

// Otherwise fill and print the form.
$form->display();

echo $OUTPUT->footer();
