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
 * Prints an instance of mod_margic.
 *
 * @package     mod_margic
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_margic\output\margic_view;
use mod_margic\local\results;
use core\output\notification;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once($CFG->dirroot . '/mod/margic/locallib.php');

// Course_module ID.
$id = required_param('id', PARAM_INT);

// Module instance ID as alternative.
$m  = optional_param('m', null, PARAM_INT);

// Param containing user id if only entries for one user should be displayed.
$userid = optional_param('userid',  0, PARAM_INT); // User id.

// Param containing the requested action.
$action = optional_param('action',  'currententry', PARAM_ALPHANUMEXT);

// Param containing the page count.
$pagecount = optional_param('pagecount', 0, PARAM_INT);

// Param containing the active page.
$page = optional_param('page', 1, PARAM_INT);

// Param if annotation mode is activated.
$annotationmode = optional_param('annotationmode',  0, PARAM_BOOL); // Annotation mode.

$margic = margic::get_margic_instance($id, $m, $userid, $action, $pagecount, $page);

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

if (!$moduleinstance) {
    throw new moodle_exception(get_string('incorrectmodule', 'margic'));
}

if (! $coursesections = $DB->get_record("course_sections", array(
    "id" => $cm->section
))) {
    throw new moodle_exception(get_string('incorrectmodule', 'margic'));
}

require_login($course, true, $cm);

$canmanageentries = has_capability('mod/margic:manageentries', $context);
$canaddentries = has_capability('mod/margic:addentries', $context);

if (!$canaddentries) {
    throw new moodle_exception(get_string('accessdenied', 'margic'));
}

// Process incoming data if there is any.
if ($data = data_submitted()) {
    confirm_sesskey();
    $feedback = array();
    $data = (array) $data;

    if (isset($data["submitbutton"])) {
        $entries = $margic->get_entries_with_keys();

        foreach ($data as $key => $val) {
            if (strpos($key, 'r') === 0 || strpos($key, 'c') === 0) {
                $type = substr($key, 0, 1);
                $num = substr($key, 1);
                $feedback[$num][$type] = $val;
            }
        }

        $timenow = time();
        $count = 0;
        foreach ($feedback as $num => $vals) {
            $entry = $entries[$num];

            // Only update entries where feedback has actually changed.
            $ratingchanged = false;
            if ($moduleinstance->assessed != 0) {
                $studentrating = clean_param($vals['r'], PARAM_INT);
            } else {
                $studentrating = '';
            }
            $studentcomment = clean_text($vals['c'], FORMAT_PLAIN);

            if ($studentrating != $entry->rating && ! ($studentrating == '' && $entry->rating == "0")) {
                $ratingchanged = true;
            }

            if ($ratingchanged || $studentcomment != $entry->entrycomment) {
                $newentry = new StdClass();
                $newentry->rating = $studentrating;
                $newentry->entrycomment = $studentcomment;
                $newentry->teacher = $USER->id;
                $newentry->timemarked = $timenow;
                $newentry->mailed = 0; // Make sure mail goes out (again, even).
                $newentry->id = $num;
                if (! $DB->update_record("margic_entries", $newentry)) {
                    notify("Failed to update the margic feedback for user $entry->userid");
                } else {
                    $count ++;
                }

                if ($moduleinstance->assessed != 0) {
                    $ratingoptions = new stdClass();
                    $ratingoptions->contextid = $context->id;
                    $ratingoptions->component = 'mod_margic';
                    $ratingoptions->ratingarea = 'entry';
                    $ratingoptions->itemid = $entry->id;
                    $ratingoptions->aggregate = $moduleinstance->assessed; // The aggregation method.
                    $ratingoptions->scaleid = $moduleinstance->scale;
                    $ratingoptions->rating = $studentrating;
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
            }
        }

        // Trigger module feedback updated event.
        $event = \mod_margic\event\feedback_updated::create(array(
            'objectid' => $moduleinstance->id,
            'context' => $context
        ));
        $event->add_record_snapshot('course_modules', $cm);
        $event->add_record_snapshot('course', $course);
        $event->add_record_snapshot('margic', $moduleinstance);
        $event->trigger();

        // Redirect and display how many entries were updated with feedback and grades.
        redirect(new moodle_url('/mod/margic/view.php', array('id' => $id)), get_string('feedbackupdated', 'mod_margic', $count), null, notification::NOTIFY_SUCCESS);
    } else {
        // Redirect if pagecount is updated.
        redirect(new moodle_url('/mod/margic/view.php', array('id' => $id)), null, null, null);
    }

} else {

    // Trigger course_module_viewed event.
    $event = \mod_margic\event\course_module_viewed::create(array(
        'objectid' => $moduleinstance->id,
        'context' => $context
    ));
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('margic', $moduleinstance);
    $event->trigger();
}

// Toolbar action handler for download.
if (!empty($action) && $action == 'download' && has_capability('mod/margic:addentries', $context)) {
    // Call download entries function in lib.php.
    results::download_entries($context, $course, $moduleinstance);
}

// Get the name for this margic activity.
$margicname = format_string($moduleinstance->name, true, array(
    'context' => $context
));

$canmakeannotations = has_capability('mod/margic:makeannotations', $context);

// Add javascript and navbar element if annotationmode is activated and user has capability.
if ($annotationmode === 1 && has_capability('mod/margic:viewannotations', $context)) {

    $PAGE->set_url('/mod/margic/view.php', array(
        'id' => $cm->id,
        'annotationmode' => 1,
    ));

    $PAGE->navbar->add(get_string("viewentries", "margic"), new moodle_url('/mod/margic/view.php', array('id' => $cm->id)));
    $PAGE->navbar->add(get_string('viewannotations', 'mod_margic'));

    $PAGE->requires->js_call_amd('mod_margic/annotations', 'init',
        array('annotations' => $margic->get_annotations(),
            'canmakeannotations' => $canmakeannotations));
} else {
    // Header.
    $PAGE->set_url('/mod/margic/view.php', array(
        'id' => $cm->id
    ));
    $PAGE->navbar->add(get_string("viewentries", "margic"));
}

$PAGE->set_title(get_string('modulename', 'mod_margic').': ' . $margicname);
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->force_settings_menu();

echo $OUTPUT->header();
echo $OUTPUT->heading($margicname);

if ($moduleinstance->intro) {
    echo $OUTPUT->box(format_module_intro('margic', $moduleinstance, $cm->id), 'generalbox mod_introbox', 'newmoduleintro');
}

// Set start and finish time. Needs to be reworked/simplified?
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

// Get grading of current user when margic is rated.
if ($moduleinstance->assessed != 0) {
    $ratingaggregationmode = results::get_margic_aggregation($moduleinstance->assessed) . ' ' . get_string('forallmyentries', 'mod_margic');
    $gradinginfo = grade_get_grades($course->id, 'mod', 'margic', $moduleinstance->id, $USER->id);
    $userfinalgrade = $gradinginfo->items[0]->grades[$USER->id];
    $currentuserrating = $userfinalgrade->str_long_grade;
} else {
    $ratingaggregationmode = false;
    $currentuserrating = false;
}

$timenow = time();
if (!$moduleinstance->timeclose) {
    $edittimehasended = false;
    $edittimeends = false;
} else if ($moduleinstance->timeclose && $timenow < $moduleinstance->timeclose) {
    $edittimehasended = false;
    $edittimeends = $moduleinstance->timeclose;
} else if ($moduleinstance->timeclose && $timenow >= $moduleinstance->timeclose) {
    $edittimehasended = true;
    $edittimeends = $moduleinstance->timeclose;
}

// Handle groups.
echo groups_print_activity_menu($cm, $CFG->wwwroot . "/mod/margic/view.php?id=$id");

// Output page.
$page = new margic_view($cm, $margic->get_entries_grouped_by_pagecount(), $margic->get_sortmode(),
    get_config('mod_margic', 'entrybgc'), get_config('mod_margic', 'entrytextbgc'), $moduleinstance->editall,
    $edittimeends, $edittimehasended, $canmanageentries, sesskey(), $currentuserrating, $ratingaggregationmode, $course->id,
    $userid, $margic->get_pagecountoptions(), $margic->get_pagebar(), count($margic->get_entries()), $annotationmode, $canmakeannotations, $margic->get_annotationtypes_for_form());

echo $OUTPUT->render($page);

echo $OUTPUT->footer();
