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
 * This page opens the current report instance of margic.
 *
 * @package   mod_margic
 * @copyright 2020 AL Rachels (drachels@drachels.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use mod_margic\local\results;

use core\output\notification; // [margic]

require_once("../../config.php");
require_once("lib.php");
require_once($CFG->dirroot.'/rating/lib.php');

$id = required_param('id', PARAM_INT); // Course module.
$action = optional_param('action', 'currententry', PARAM_ACTION); // Action(default to current entry).
$user = required_param('user', PARAM_INT); // Course module.

// [margic] Param if annotation mode is activated
$annotationmode = optional_param('annotationmode',  1, PARAM_BOOL); // Annotation mode.
// [margic] Param if annotation should be deleted
$deleteannotation = optional_param('deleteannotation',  0, PARAM_INT); // Annotation to be deleted.

if (! $cm = get_coursemodule_from_id('margic', $id)) {
    throw new moodle_exception(get_string('incorrectmodule', 'margic'));
}

if (! $course = $DB->get_record("course", array(
    "id" => $cm->course
))) {
    throw new moodle_exception(get_string('incorrectcourseid', 'margic'));
}

require_login($course, false, $cm);

$context = context_module::instance($cm->id);

require_capability('mod/margic:manageentries', $context);

if (! $margic = $DB->get_record("margic", array(
    "id" => $cm->instance
))) {
    throw new moodle_exception(get_string('invalidid', 'margic'));
}

// [margic] Delete annotation
if (has_capability('mod/margic:makeannotations', $context) && $deleteannotation !== 0) {
    $DB->delete_records('margic_annotations', array('id' => $deleteannotation, 'margic' => $margic->id, 'userid' => $USER->id));

    redirect(new moodle_url('/mod/margic/reportsingle.php', array('id' => $id, 'user' => $user, 'action' => $action,
        'annotationmode' => 1)), get_string('annotationdeleted', 'mod_margic'), null, notification::NOTIFY_SUCCESS);
}

// 20201016 Get the name for this margic activity.
$margicname = format_string($margic->name, true, array(
    'context' => $context
));

// 20201014 Set a default sorting order for entry retrieval.
if ($sortoption = get_user_preferences('sortoption')) {
    $sortoption = get_user_preferences('sortoption');
} else {
    set_user_preference('sortoption', 'u.lastname ASC, u.firstname ASC');
    $sortoption = get_user_preferences('sortoption');
}

if (has_capability('mod/margic:manageentries', $context)) {
    $stringlable = 'reportsingleallentries';
    // Get ALL margic entries from this margic, for this user, from newest to oldest.
    $eee = $DB->get_records("margic_entries", array(
        "margic" => $margic->id,
        "userid" => $user
        ), $sort = 'timecreated DESC');
}

// [margic] Add javascript and navbar element if annotationmode is activated and user has capability.
if ($annotationmode === 1 && has_capability('mod/margic:viewannotations', $context)) {

    $PAGE->set_url('/mod/margic/reportsingle.php', array(
        'id' => $cm->id,
        'annotationmode' => 1,
        'user' => $user,
        'action' => $action
    ));

    $redirecturl = new moodle_url('/mod/margic/reportsingle.php', array('id' => $cm->id, 'annotationmode' => 1, 'user' => $user, 'action' => $action, ));
    $userid = $user;

    $PAGE->navbar->add(get_string("entries", "margic") . ' ' . get_string("rate", "margic"));

    $PAGE->set_title($margicname);
    $PAGE->set_heading($course->fullname);

    echo $OUTPUT->header();
    echo $OUTPUT->heading($margicname);

    $PAGE->requires->js_call_amd('mod_margic/annotations', 'init',
        array('annotations' => $DB->get_records('margic_annotations', array('margic' => $cm->instance)),
            'canmakeannotations' => has_capability('mod/margic:makeannotations', $context)));
} else {
    // Header.
    $PAGE->set_url('/mod/margic/reportsingle.php', array(
            'id' => $id
        ));
    $PAGE->navbar->add(get_string("entries", "margic") . ' ' . get_string("rate", "margic"));
    $PAGE->set_title($margicname);
    $PAGE->set_heading($course->fullname);

    echo $OUTPUT->header();
    echo $OUTPUT->heading($margicname);
}

// 20201016 Added missing header label. 20210511 Changed to remove hard coded <h5>'s.
echo '<div>'.(get_string('sortorder', "margic"));
echo (get_string($stringlable, "margic"));

// 20200827 Added link to index.php page.
echo '<span style="float: right;"><a href="index.php?id='.$course->id.'">'
    .get_string('viewalldiaries', 'margic').'</a></span></div>';

// Save our current user id and also get his details. CHECK - might not need this.
$users = $user;
$user = $DB->get_record("user", array("id" => $user));

if ($eee) {
    // Now, filter down to get entry by any user who has made at least one entry.
    foreach ($eee as $ee) {
        $entrybyuser[$ee->userid] = $ee;
        $entrybyentry[$ee->id] = $ee;
        $entrybyuserentry[$ee->userid][$ee->id] = $ee;
    }
} else {
    $entrybyuser = array();
    $entrybyentry = array();
}

// Process incoming data if there is any.
if ($data = data_submitted()) {
    confirm_sesskey();
    $feedback = array();
    $data = (array) $data;
    // My single data entry contains id, sesskey, and three other items, entry, feedback, and ???
    // Peel out all the data from variable names.
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
        $entry = $entrybyentry[$num];
        // Only update entries where feedback has actually changed.
        $ratingchanged = false;
        if ($margic->assessed != RATING_AGGREGATE_NONE) {
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
            $entrybyuser[$entry->userid]->rating = $studentrating;
            $entrybyuser[$entry->userid]->entrycomment = $studentcomment;
            $entrybyuser[$entry->userid]->teacher = $USER->id;
            $entrybyuser[$entry->userid]->timemarked = $timenow;

            $records[$entry->id] = $entrybyuser[$entry->userid];

            // Compare to database view.php line 465.
            if ($margic->assessed != RATING_AGGREGATE_NONE) {
                // 20200812 Added rating code and got it working.
                $ratingoptions = new stdClass();
                $ratingoptions->contextid = $context->id;
                $ratingoptions->component = 'mod_margic';
                $ratingoptions->ratingarea = 'entry';
                $ratingoptions->itemid = $entry->id;
                $ratingoptions->aggregate = $margic->assessed; // The aggregation method.
                $ratingoptions->scaleid = $margic->scale;
                $ratingoptions->rating = $studentrating;
                $ratingoptions->userid = $entry->userid;
                $ratingoptions->timecreated = $entry->timecreated;
                $ratingoptions->timemodified = $entry->timemodified;
                $ratingoptions->returnurl = $CFG->wwwroot.'/mod/margic/reportsingle.php?id'.$id;
                $ratingoptions->assesstimestart = $margic->assesstimestart;
                $ratingoptions->assesstimefinish = $margic->assesstimefinish;
                // 20200813 Check if there is already a rating, and if so, just update it.
                if ($rec = results::check_rating_entry($ratingoptions)) {
                    $ratingoptions->id = $rec->id;
                    $DB->update_record('rating', $ratingoptions, false);
                } else {
                    $DB->insert_record('rating', $ratingoptions, false);
                }
            }

            $margic = $DB->get_record("margic", array(
                "id" => $entrybyuser[$entry->userid]->margic
            ));
            $margic->cmidnumber = $cm->idnumber;

            margic_update_grades($margic, $entry->userid);
        }
    }

    // Trigger module feedback updated event.
    $event = \mod_margic\event\feedback_updated::create(array(
        'objectid' => $margic->id,
        'context' => $context
    ));
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('margic', $margic);
    $event->trigger();

    // Report how many entries were updated when the, Save all my feedback button was pressed.
    echo $OUTPUT->notification(get_string("feedbackupdated", "margic", "$count"), "notifysuccess");
} else {

    // Trigger module viewed event.
    $event = \mod_margic\event\entries_viewed::create(array(
        'objectid' => $margic->id,
        'context' => $context
    ));
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('margic', $margic);
    $event->trigger();
}

if (! $users) {
    echo $OUTPUT->heading(get_string("nousersyet"));
} else {

    // Next line is different from Journal line 171.
    $grades = make_grades_menu($margic->scale);

    if (! $teachers = get_users_by_capability($context, 'mod/margic:manageentries')) {
        throw new moodle_exception(get_string('noentriesmanagers', 'margic'));
    }
    // Start the page area where feedback and grades are added and will need to be saved.
    // Set up to return to report.php upon saving feedback.
    echo '<form action="report.php" method="post">';
    // Create a variable with all the info to save all my feedback, so it can be used multiple places.
    $saveallbutton = '';
    $saveallbutton = "<p class=\"feedbacksave\">";
    $saveallbutton .= "<input type=\"hidden\" name=\"id\" value=\"$cm->id\" />";
    $saveallbutton .= "<input type=\"hidden\" name=\"sesskey\" value=\"".sesskey()."\" />";
    $saveallbutton .= "<input type=\"submit\" class='btn btn-primary' value=\"".get_string("saveallfeedback", "margic")."\" />";

    // 20201222 Added a return to report.php button if you do not want to save feedback.
    $url = $CFG->wwwroot.'/mod/margic/report.php?id='.$id;
    $saveallbutton .= ' <a href="'.$url
                     .'" class="btn btn-primary" role="button">'
                     .get_string('returntoreport', 'margic', $margic->name)
                     .'</a>';

    $saveallbutton .= "</p>";

    // Add save button at the top of the list of users with entries.
    echo $saveallbutton;

    $dcolor3 = get_config('mod_margic', 'entrybgc');
    $dcolor4 = get_config('mod_margic', 'entrytextbgc');

    // [margic] Add divs for annotations menu if annotationmode is activated and user has capability.
    if ($annotationmode === 1 && has_capability('mod/margic:viewannotations', $context)) {
        echo '<div class="container mw-100">';
    }

    foreach ($eee as $ee) {
        // [margic] Add divs for annotations menu if annotationmode is activated and user has capability.
        if ($annotationmode === 1 && has_capability('mod/margic:viewannotations', $context)) {
            echo '<div class="row"><div class="col-sm-8">';
        }

        // 20210511 Changed to using class.
        echo '<div class="entry" style="background: '.$dcolor3.'">';

        // Based on the single selected user, print all their entries on screen.
        echo results::margic_print_user_entry($course,
                                             $margic,
                                             $user,
                                             $ee,
                                             $teachers,
                                             $grades);

        echo '</div>';

        // [margic] Add annotations menu if annotationmode is activated and user has capability.
        if ($annotationmode === 1 && has_capability('mod/margic:viewannotations', $context)) {
            echo '</div>';
            $entryid = $ee->id;
            include(__DIR__ ."/classes/annotations/annotations.php"); // include annotation menu
            echo '</div>';
        }

        // Since the list can be quite long, add a save button after each entry that will save ALL visible changes.
        echo $saveallbutton;
    }

    // [margic] Finisch annotations menu if annotationmode is activated and user has capability.
    if ($annotationmode === 1 && has_capability('mod/margic:viewannotations', $context)) {
        echo '</div>';
    }

    // End the page area where feedback and grades are added and will need to be saved.
    echo "</form>";
}

echo $OUTPUT->footer();
