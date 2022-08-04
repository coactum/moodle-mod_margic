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
 * The page for the grading of entries in mod_margic.
 *
 * @package     mod_margic
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\output\notification;
use mod_margic\local\results;

require_once("../../config.php");
require_once($CFG->dirroot . '/mod/margic/grading_form.php');
require_once($CFG->dirroot . '/mod/margic/locallib.php');

global $DB, $USER;

// Course Module ID.
$id = required_param('id', PARAM_INT);

// Module instance ID as alternative.
$m  = optional_param('d', null, PARAM_INT);

// ID of the entry to be graded.
$entryid = required_param('entryid', PARAM_INT);

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

$PAGE->set_url('/mod/margic/grade_entry.php', array('id' => $id));

$entry = $DB->get_record('margic_entries', array('id' => $entryid, 'margic' => $cm->instance));
$grades = make_grades_menu($moduleinstance->scale);

// Prepare editor for files.
$data = new stdClass();
$data->id = $id;
$data->entry = $entry->id;
$data->timecreated = $entry->timecreated;
$data->{'feedback_' . $entry->id} = $entry->entrycomment;
$data->{'feedback_' . $entry->id . 'format'} = $entry->formatcomment;

list ($editoroptions, $attachmentoptions) = results::margic_get_editor_and_attachment_options($course, $context, $moduleinstance);

$data = file_prepare_standard_editor($data, 'feedback_' . $entry->id, $editoroptions, $context, 'mod_margic', 'feedback', $data->entry);
$data = file_prepare_standard_filemanager($data, 'attachment', $attachmentoptions, $context, 'mod_margic', 'attachment', $data->entry);

$data->{'rating_' . $entry->id} = $entry->rating;

// Instantiate gradingform and save submitted data if it exists.
$mform = new \mod_margic_grading_form(null,
    array('courseid' => $course->id, 'margic' => $moduleinstance, 'entry' => $entry, 'grades' => $grades, 'teacherimg' => '', 'editoroptions' => $editoroptions));

$mform->set_data($data);

if ($fromform = $mform->get_data()) { // If grading form is submitted.
    // In this case you process validated data.

    if ($fromform->entry !== $entryid) {
        redirect(new moodle_url('/mod/margic/view.php', array('id' => $id)), get_string('errfeedbacknotupdated', 'mod_margic'), null, notification::NOTIFY_ERROR);
    }

    if (!$fromform->{'feedback_' . $entry->id . '_editor'}) {
        redirect(new moodle_url('/mod/margic/view.php', array('id' => $id)), get_string('errnofeedbackorratingdisabled', 'mod_margic'), null, notification::NOTIFY_ERROR);
    }

    $fromform = file_postupdate_standard_editor($fromform, 'feedback_' . $entry->id, $editoroptions, $editoroptions['context'], 'mod_margic', 'feedback', $entry->id);

    $newfeedback = file_rewrite_pluginfile_urls($fromform->{'feedback_' . $entry->id}, 'pluginfile.php', $context->id, 'mod_margic', 'feedback', $entry->id);

    $newfeedback = format_text($newfeedback, $fromform->{'feedback_' . $entry->id . '_editor'}['format'], array('para' => false));

    if (isset($fromform->{'rating_' . $entry->id})) {
        $newrating = $fromform->{'rating_' . $entry->id};
    }

    if (isset($newrating) && $newrating != $entry->rating) {
        $ratingchanged = true;
    } else {
        $ratingchanged = false;
    }

    if ($newfeedback != $entry->entrycomment && !($newfeedback == '' && $entry->entrycomment == null)) {
        $feedbackchanged = true;
    } else {
        $feedbackchanged = false;
    }

    if ($ratingchanged || $feedbackchanged) { // Only update entry if feedback has actually changed.
        $timenow = time();

        if (isset($newrating)) {
            $entry->rating = $newrating;
        }

        $entry->entrycomment = $newfeedback;
        $entry->formatcomment = $fromform->{'feedback_' . $entry->id . '_editor'}['format'];
        $entry->teacher = $USER->id;
        $entry->timemarked = $timenow;

        if (!$DB->update_record("margic_entries", $entry)) {
            redirect(new moodle_url('/mod/margic/view.php', array('id' => $id)), get_string('errfeedbacknotupdated', 'mod_margic'), null, notification::NOTIFY_ERROR);
        }

        if ($moduleinstance->assessed != 0) {
            $ratingoptions = new stdClass();
            $ratingoptions->contextid = $context->id;
            $ratingoptions->component = 'mod_margic';
            $ratingoptions->ratingarea = 'entry';
            $ratingoptions->itemid = $entry->id;
            $ratingoptions->aggregate = $moduleinstance->assessed; // The aggregation method.
            $ratingoptions->scaleid = $moduleinstance->scale;
            $ratingoptions->rating = $newrating;
            $ratingoptions->userid = $entry->userid;
            $ratingoptions->timecreated = $entry->timecreated;
            $ratingoptions->timemodified = $timenow;
            $ratingoptions->returnurl = $CFG->wwwroot . '/mod/margic/view.php?id' . $id;

            $ratingoptions->assesstimestart = $moduleinstance->assesstimestart;
            $ratingoptions->assesstimefinish = $moduleinstance->assesstimefinish;

            // Check if there is already a rating, and if so, just update it.
            if ($rec = results::check_rating_entry($ratingoptions)) {
                $ratingoptions->id = $rec->id;
                $DB->update_record('rating', $ratingoptions, false);
            } else {
                $DB->insert_record('rating', $ratingoptions, false);
            }
        }

        $record = $moduleinstance;
        $record->cmidnumber = $cm->idnumber;

        margic_update_grades($record, $entry->userid);

        // Trigger module feedback updated event.
        $event = \mod_margic\event\feedback_updated::create(array(
            'objectid' => $moduleinstance->id,
            'context' => $context
        ));
        $event->add_record_snapshot('course_modules', $cm);
        $event->add_record_snapshot('course', $course);
        $event->add_record_snapshot('margic', $moduleinstance);
        $event->trigger();

        if ($fromform->sendgradingmessage) {
            global $CFG;

            $url = (new \moodle_url("/mod/margic/view.php?id=$id"))->out(false);

            $obj = new stdClass();
            $obj->user = fullname(core_user::get_user($entry->userid));
            $obj->teacher = fullname(core_user::get_user($entry->teacher));
            $obj->margic = format_string($moduleinstance->name, true);
            $obj->url = $url;

            // Send grading message.
            $message = new \core\message\message();
            $message->component = 'mod_margic';
            $message->name = 'gradingmessages';
            $message->userfrom = core_user::get_noreply_user(); // If the message is 'from' a specific user you can set them here
            $message->userto = $entry->userid;
            $message->subject = get_string('gradingmailsubject', 'mod_margic');
            $message->fullmessage = get_string('gradingmailfullmessage', 'mod_margic', $obj);
            $message->fullmessageformat = FORMAT_MARKDOWN;
            $message->fullmessagehtml = '<p>' . get_string('gradingmailfullmessagehtml', 'mod_margic', $obj) . '</p>';
            $message->smallmessage = get_string('gradingmailfullmessage', 'mod_margic', $obj);
            $message->notification = 1; // Because this is a notification generated from Moodle, not a user-to-user message
            $message->contexturl = $url; // A relevant URL for the notification
            $message->contexturlname = get_string('modulename', 'mod_margic')
                . ' - ' . get_string('overview', 'mod_margic'); // Link title explaining where users get to for the contexturl

            $header = '';
            $urllink = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
            $footer = '<br><br> --------------------------------------------------------------------- <br> ' . get_string('mailfooter', 'mod_margic',
                ['systemname' => get_config('shortname'), 'coursename' => $course->fullname, 'name' => $moduleinstance->name, 'url' => $url]);
            $content = array('*' => array('header' => $header, 'footer' => $footer)); // Extra content for specific processor
            $message->set_additional_content('email', $content);

            // Actually send the message
            $messageid = message_send($message);
        }

        // Redirect after updated from feedback and grades.
        redirect(new moodle_url('/mod/margic/view.php', array('id' => $id)), get_string('feedbackupdated', 'mod_margic'), null, notification::NOTIFY_SUCCESS);
    } else {
        redirect(new moodle_url('/mod/margic/view.php', array('id' => $id)), get_string('errfeedbacknotupdated', 'mod_margic'), null, notification::NOTIFY_ERROR);
    }
} else {
    redirect(new moodle_url('/mod/margic/view.php', array('id' => $id)), get_string('errfeedbacknotupdated', 'mod_margic'), null, notification::NOTIFY_ERROR);
}
