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
 * This page opens the current lib instance of margic.
 *
 * @package   mod_margic
 * @copyright 2022 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_margic\local\results;

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod.html) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $margic
 *            Object containing required margic properties.
 * @return int margic ID.
 */
function margic_add_instance($margic) {
    global $DB;

    if (empty($margic->assessed)) {
        $margic->assessed = 0;
    }
    // 20190917 First one always true as ratingtime does not exist.
    if (empty($margic->ratingtime) || empty($margic->assessed)) {
        $margic->assesstimestart = 0;
        $margic->assesstimefinish = 0;
    }
    $margic->timemodified = time();
    $margic->id = $DB->insert_record('margic', $margic);

    // 20200903 Added calendar dates.
    results::margic_update_calendar($margic, $margic->coursemodule);

    // 20200901 Added expected completion date.
    if (! empty($margic->completionexpected)) {
        \core_completion\api::update_completion_date_event($margic->coursemodule, 'margic', $margic->id, $margic->completionexpected);
    }

    margic_grade_item_update($margic);

    return $margic->id;
}

/**
 *
 * Given an object containing all the necessary margic data,
 * will update an existing instance with new margic data.
 *
 * @param object $margic
 *            Object containing required margic properties.
 * @return boolean True if successful.
 */
function margic_update_instance($margic) {
    global $DB;

    $margic->timemodified = time();
    $margic->id = $margic->instance;

    if (empty($margic->assessed)) {
        $margic->assessed = 0;
    }

    if (empty($margic->ratingtime) || empty($margic->assessed)) {
        $margic->assesstimestart = 0;
        $margic->assesstimefinish = 0;
    }

    if (empty($margic->notification)) {
        $margic->notification = 0;
    }

    // If the aggregation type or scale (i.e. max grade) changes then recalculate the grades for the entire margic
    // if scale changes - do we need to recheck the ratings, if ratings higher than scale how do we want to respond?
    // for count and sum aggregation types the grade we check to make sure they do not exceed the scale (i.e. max score) when calculating the grade
    $oldmargic = $DB->get_record('margic', array('id' => $margic->id));

    $updategrades = false;

    if ($oldmargic->assessed <> $margic->assessed) {
        // Whether this margic is rated.
        $updategrades = true;
    }

    if ($oldmargic->scale <> $margic->scale) {
        // The scale currently in use.
        $updategrades = true;
    }

    if ($updategrades) {
        margic_update_grades($margic); // Recalculate grades for the margic.
    }

    $DB->update_record('margic', $margic);

    // Update calendar.
    results::margic_update_calendar($margic, $margic->coursemodule);

    // Update completion date.
    $completionexpected = (! empty($margic->completionexpected)) ? $margic->completionexpected : null;
    \core_completion\api::update_completion_date_event($margic->coursemodule, 'margic', $margic->id, $completionexpected);

    // Update grade.
    margic_grade_item_update($margic);

    return true;
}

/**
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id margic id.
 * @return boolean True if successful.
 */
function margic_delete_instance($id) {
    global $DB;

    if (!$margic = $DB->get_record("margic", array("id" => $id))) {
        return false;
    }
    if (!$cm = get_coursemodule_from_instance('margic', $margic->id)) {
        return false;
    }
    if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
        return false;
    }

    $context = context_module::instance($cm->id);

    // Delete files.
    $fs = get_file_storage();
    $fs->delete_area_files($context->id);

    // Update completion for calendar events.
    \core_completion\api::update_completion_date_event($cm->id, 'margic', $margic->id, null);

    // Delete grades.
    margic_grade_item_delete($margic);

    // Delete entries.
    $DB->delete_records("margic_entries", array("margic" => $margic->id));

    // Delete annotations.
    $DB->delete_records("margic_annotations", array("margic" => $margic->id));

    // Delete margic, else return false.
    if (!$DB->delete_records("margic", array("id" => $margic->id))) {
        return false;
    }

    return true;
}

/**
 * Indicates API features that the margic supports.
 *
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_SHOW_DESCRIPTION
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @uses FEATURE_RATE
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE__BACKUP_MOODLE2
 * @param string $feature Constant for requested feature.
 * @return mixed True if module supports feature, null if it doesn't.
 */
function margic_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_GRADE_OUTCOMES:
            return false;
        case FEATURE_RATE:
            return true;
        case FEATURE_GROUPS:
            return true;
        case FEATURE_GROUPINGS:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        default:
            return null;
    }
}

/**
 * List the actions that correspond to a view of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 * crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 * be considered as view action.
 *
 * @return array
 */
function margic_get_view_actions() {
    return array(
        'view',
        'view all',
        'view responses'
    );
}

/**
 * List the actions that correspond to a post of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 * crud = ('c' || 'u' || 'd') and edulevel = LEVEL_PARTICIPATING
 * will be considered as post action.
 *
 * @return array
 */
function margic_get_post_actions() {
    return array(
        'add entry',
        'update entry',
        'update feedback'
    );
}

/**
 * Returns a summary of data activity of this user.
 *
 * Not used yet, as of 20200718.
 *
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $margic
 * @return object|null
 */
function margic_user_outline($course, $user, $mod, $margic) {
    global $DB;

    if ($entry = $DB->get_record("margic_entries", array(
        "userid" => $user->id,
        "margic" => $margic->id
    ))) {

        $numwords = count(preg_split("/\w\b/", $entry->text)) - 1;

        $result = new stdClass();
        $result->info = get_string("numwords", "", $numwords);
        $result->time = $entry->timemodified;
        return $result;
    }
    return null;
}

/**
 * Prints all the records uploaded by this user.
 *
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $margic
 */
function margic_user_complete($course, $user, $mod, $margic) {
    global $DB, $OUTPUT;

    if ($entry = $DB->get_record("margic_entries", array(
        "userid" => $user->id,
        "margic" => $margic->id
    ))) {

        echo $OUTPUT->box_start();

        if ($entry->timemodified) {
            echo "<p><font size=\"1\">" . get_string("lastedited") . ": " . userdate($entry->timemodified) . "</font></p>";
        }
        if ($entry->text) {
            echo margic_format_entry_text($entry, $course, $mod);
        }
        if ($entry->teacher) {
            $grades = make_grades_menu($margic->grade);
            margic_print_feedback($course, $entry, $grades);
        }

        echo $OUTPUT->box_end();
    } else {
        print_string("noentry", "margic");
    }
}

/**
 * Function to be run periodically according to the moodle cron.
 * Finds all margic notifications that have yet to be mailed out, and mails them.
 *
 * @return boolean True if successful.
 */
/* function margic_cron() {
    global $CFG, $USER, $DB;

    $cutofftime = time() - $CFG->maxeditingtime;

    if ($entries = margic_get_unmailed_graded($cutofftime)) {
        $timenow = time();

        $usernamefields = get_all_user_name_fields();
        $requireduserfields = 'id, auth, mnethostid, email, mailformat, maildisplay, lang, deleted, suspended, '
            .implode(', ', $usernamefields);

        // To save some db queries.
        $users = array();
        $courses = array();

        foreach ($entries as $entry) {

            echo "Processing margic entry $entry->id\n";

            if (! empty($users[$entry->userid])) {
                $user = $users[$entry->userid];
            } else {
                if (! $user = $DB->get_record("user", array(
                    "id" => $entry->userid
                ), $requireduserfields)) {
                    echo "Could not find user $entry->userid\n";
                    continue;
                }
                $users[$entry->userid] = $user;
            }

            $USER->lang = $user->lang;

            if (! empty($courses[$entry->course])) {
                $course = $courses[$entry->course];
            } else {
                if (! $course = $DB->get_record('course', array(
                    'id' => $entry->course), 'id, shortname')) {
                    echo "Could not find course $entry->course\n";
                    continue;
                }
                $courses[$entry->course] = $course;
            }

            if (! empty($users[$entry->teacher])) {
                $teacher = $users[$entry->teacher];
            } else {
                if (! $teacher = $DB->get_record("user", array(
                    "id" => $entry->teacher), $requireduserfields)) {
                    echo "Could not find teacher $entry->teacher\n";
                    continue;
                }
                $users[$entry->teacher] = $teacher;
            }

            // All cached.
            $coursemargics = get_fast_modinfo($course)->get_instances_of('margic');
            if (empty($coursemargics) || empty($coursemargics[$entry->margic])) {
                echo "Could not find course module for margic id $entry->margic\n";
                continue;
            }
            $mod = $coursemargics[$entry->margic];

            // This is already cached internally.
            $context = context_module::instance($mod->id);
            $canadd = has_capability('mod/margic:addentries', $context, $user);
            $entriesmanager = has_capability('mod/margic:manageentries', $context, $user);

            if (! $canadd and $entriesmanager) {
                continue; // Not an active participant.
            }

            $margicinfo = new stdClass();
            // 20200829 Added users first and last name to message.
            $margicinfo->user = $user->firstname . ' ' . $user->lastname;
            $margicinfo->teacher = fullname($teacher);
            $margicinfo->margic = format_string($entry->name, true);
            $margicinfo->url = "$CFG->wwwroot/mod/margic/view.php?id=$mod->id";
            $modnamepl = get_string('modulenameplural', 'margic');
            $msubject = get_string('mailsubject', 'margic');

            $postsubject = "$course->shortname: $msubject: " . format_string($entry->name, true);
            $posttext = "$course->shortname -> $modnamepl -> " . format_string($entry->name, true) . "\n";
            $posttext .= "---------------------------------------------------------------------\n";
            $posttext .= get_string("margicmail", "margic", $margicinfo) . "\n";
            $posttext .= "---------------------------------------------------------------------\n";
            if ($user->mailformat == 1) { // HTML.
                $posthtml = "<p><font face=\"sans-serif\">"
                    ."<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ->"
                    ."<a href=\"$CFG->wwwroot/mod/margic/index.php?id=$course->id\">margics</a> ->"
                    ."<a href=\"$CFG->wwwroot/mod/margic/view.php?id=$mod->id\">"
                    .format_string($entry->name, true)
                    ."</a></font></p>";
                $posthtml .= "<hr /><font face=\"sans-serif\">";
                $posthtml .= "<p>" . get_string("margicmailhtml", "margic", $margicinfo) . "</p>";
                $posthtml .= "</font><hr />";
            } else {
                $posthtml = "";
            }

            if (! email_to_user($user, $teacher, $postsubject, $posttext, $posthtml)) {
                echo "Error: margic cron: Could not send out mail for id $entry->id to user $user->id ($user->email)\n";
            }
            if (! $DB->set_field("margic_entries", "mailed", "1", array(
                "id" => $entry->id
            ))) {
                echo "Could not update the mailed field for id $entry->id\n";
            }
        }
    }

    return true;
} */

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in margic activities and print it out.
 * Return true if there was output, or false if there was none.
 *
 * @param stdClass $course
 * @param bool $viewfullnames
 * @param int $timestart
 * @return bool
 */
function margic_print_recent_activity($course, $viewfullnames, $timestart) {
    global $CFG, $USER, $DB, $OUTPUT;

    if (! get_config('margic', 'showrecentactivity')) {
        return false;
    }

    $dbparams = array(
        $timestart,
        $course->id,
        'margic'
    );
    // Moodle branch check.
    if ($CFG->branch < 311) {
        $namefields = user_picture::fields('u', null, 'userid');
    } else {
        $userfieldsapi = \core_user\fields::for_userpic();
        $namefields = $userfieldsapi->get_sql('u', false, '', 'userid', false)->selects;;
    }
    $sql = "SELECT de.id, de.timemodified, cm.id AS cmid, $namefields
              FROM {margic_entries} de
              JOIN {margic} d ON d.id = de.margic
              JOIN {course_modules} cm ON cm.instance = d.id
              JOIN {modules} md ON md.id = cm.module
              JOIN {user} u ON u.id = de.userid
             WHERE de.timemodified > ? AND d.course = ? AND md.name = ?
          ORDER BY u.lastname ASC, u.firstname ASC
    ";
    // Changed on 20190622 original line 310: ORDER BY de.timemodified ASC.
    $newentries = $DB->get_records_sql($sql, $dbparams);

    $modinfo = get_fast_modinfo($course);

    $show = array();

    foreach ($newentries as $anentry) {
        if (! array_key_exists($anentry->cmid, $modinfo->get_cms())) {
            continue;
        }
        $cm = $modinfo->get_cm($anentry->cmid);

        if (! $cm->uservisible) {
            continue;
        }
        if ($anentry->userid == $USER->id) {
            $show[] = $anentry;
            continue;
        }
        $context = context_module::instance($anentry->cmid);

        // Only teachers can see other students entries.
        if (! has_capability('mod/margic:manageentries', $context)) {
            continue;
        }

        $groupmode = groups_get_activity_groupmode($cm, $course);

        if ($groupmode == SEPARATEGROUPS && ! has_capability('moodle/site:accessallgroups', $context)) {
            if (isguestuser()) {
                // Shortcut - guest user does not belong into any group.
                continue;
            }

            // This will be slow - show only users that share group with me in this cm.
            if (! $modinfo->get_groups($cm->groupingid)) {
                continue;
            }
            $usersgroups = groups_get_all_groups($course->id, $anentry->userid, $cm->groupingid);
            if (is_array($usersgroups)) {
                $usersgroups = array_keys($usersgroups);
                $intersect = array_intersect($usersgroups, $modinfo->get_groups($cm->groupingid));
                if (empty($intersect)) {
                    continue;
                }
            }
        }
        $show[] = $anentry;
    }

    if (empty($show)) {
        return false;
    }

    echo $OUTPUT->heading(get_string('newmargicentries', 'margic') . ':', 3);

    foreach ($show as $submission) {
        $cm = $modinfo->get_cm($submission->cmid);
        $context = context_module::instance($submission->cmid);
        if (has_capability('mod/margic:manageentries', $context)) {
            $link = $CFG->wwwroot . '/mod/margic/report.php?id=' . $cm->id;
        } else {
            $link = $CFG->wwwroot . '/mod/margic/view.php?id=' . $cm->id;
        }
        print_recent_activity_note($submission->timemodified, $submission, $cm->name, $link, false, $viewfullnames);
    }
    return true;
}

/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the margic.
 *
 * @param object $mform Form passed by reference.
 */
function margic_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'margicheader', get_string('modulenameplural', 'margic'));
    $mform->addElement('checkbox', 'reset_margic_all', get_string('deletealluserdata', 'margic'));

    $mform->addElement('checkbox', 'reset_margic_ratings', get_string('deleteallratings', 'margic'));
    $mform->disabledIf('reset_margic_ratings', 'reset_margic_all', 'checked');
    $mform->setAdvanced('reset_margic_ratings');

    $mform->addElement('checkbox', 'reset_margic_tags', get_string('deletealltags', 'margic'));
    $mform->disabledIf('reset_margic_tags', 'reset_margic_all', 'checked');
    $mform->setAdvanced('reset_margic_tags');
}

/**
 * Course reset form defaults.
 *
 * @param object $course
 * @return array
 */
function margic_reset_course_form_defaults($course) {
    return array('reset_margic_all' => 1, 'reset_margic_ratings' => 0, 'reset_margic_tags' => 0);
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * This function will remove all userdata from the specified margic.
 *
 * @param object $data The data submitted from the reset course.
 * @return array $status Status array.
 */
function margic_reset_userdata($data) {
    global $CFG, $DB;

    require_once($CFG->libdir . '/filelib.php');
    require_once($CFG->dirroot . '/rating/lib.php');

    $modulename = get_string('modulenameplural', 'margic');
    $status = array();

    // Get margics in course that should be resetted.
    $sql = "SELECT m.id
                FROM {margic} m
                WHERE m.course = ?";

    $params = array(
        $data->courseid
    );

    $margics = $DB->get_records_sql($sql, $params);

    // Get ratings manager.
    if (!empty($data->reset_margic_all) || !empty($data->reset_margic_ratings)) {
        $rm = new rating_manager();
        $ratingdeloptions = new stdClass;
        $ratingdeloptions->component = 'mod_margic';
        $ratingdeloptions->ratingarea = 'entry';
    }

    // Delete entries and their annotations, files, ratings and tags.
    if (!empty($data->reset_margic_all)) {

        foreach ($margics as $margicid => $unused) {
            if (!$cm = get_coursemodule_from_instance('margic', $margicid)) {
                continue;
            }

            // Remove files.
            $context = context_module::instance($cm->id);
            $fs->delete_area_files($context->id, 'mod_margic', 'entry');
            $fs->delete_area_files($context->id, 'mod_margic', 'feedback');

            // Remove ratings.
            $ratingdeloptions->contextid = $context->id;
            $rm->delete_ratings($ratingdeloptions);

            // Remove tags.
            core_tag_tag::delete_instances('mod_margic', null, $context->id);
        }

        // Remove all grades from gradebook (if that is not already done by the reset_gradebook_grades).
        if (empty($data->reset_gradebook_grades)) {
            margic_reset_gradebook($data->courseid);
        }

        // Delete the annotations of all entries.
        $DB->delete_records_select('margic_annotations', "margic IN ($sql)", $params);

        // Delete all entries.
        $DB->delete_records_select('margic_entries', "margic IN ($sql)", $params);

        $status[] = array(
            'component' => $modulename,
            'item' => get_string('alluserdatadeleted', 'margic'),
            'error' => false
        );
    }

    // Delete ratings only.
    if (!empty($data->reset_margic_ratings) ) {

        if ($margics) {
            foreach ($margics as $margicid => $unused) {
                if (!$cm = get_coursemodule_from_instance('margic', $margicid)) {
                    continue;
                }

                $context = context_module::instance($cm->id);
                $ratingdeloptions->contextid = $context->id;
                $rm->delete_ratings($ratingdeloptions);
            }
        }

        // Remove all grades from gradebook (if that is not already done by the reset_gradebook_grades).
        if (empty($data->reset_gradebook_grades)) {
            margic_reset_gradebook($data->courseid);
        }
    }

    // Delete tags only.
    if (!empty($data->reset_margic_tags) ) {
        if ($margics) {
            foreach ($margics as $margicid => $unused) {
                if (!$cm = get_coursemodule_from_instance('margic', $margicid)) {
                    continue;
                }

                $context = context_module::instance($cm->id);
                core_tag_tag::delete_instances('mod_margic', null, $context->id);
            }
        }

        $status[] = array('component' => $modulename, 'item' => get_string('tagsdeleted', 'margic'), 'error' => false);
    }

    // Updating dates - shift may be negative too.
    if ($data->timeshift) {
        // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
        // See MDL-9367.
        shift_course_mod_dates('margic', array('assesstimestart', 'assesstimefinish', 'timeopen', 'timeclose'), $data->timeshift, $data->courseid);
        $status[] = array('component' => $modulename, 'item' => get_string('datechanged'), 'error' => false);
    }

    return $status;
}

/**
 * Removes all grades in the margic gradebook
 *
 * @global object
 * @param int $courseid
 */
function margic_reset_gradebook($courseid) {
    global $DB;

    $params = array($courseid);

    $sql = "SELECT ma.*, cm.idnumber as cmidnumber, ma.course as courseid
              FROM {margic} ma, {course_modules} cm, {modules} m
             WHERE m.name='margic' AND m.id=cm.module AND cm.instance=ma.id AND ma.course=?";

    if ($margics = $DB->get_records_sql($sql, $params)) {
        foreach ($margics as $margic) {
            margic_grade_item_update($margic, 'reset');
        }
    }
}

/**
 * Get margic grades for a user.
 *
 * @param object $margic If null, all margics
 * @param int $userid If false all users
 * @return object $grades
 */
function margic_get_user_grades($margic, $userid = 0) {
    global $CFG;

    require_once($CFG->dirroot . '/rating/lib.php');

    $ratingoptions = new stdClass();
    $ratingoptions->component = 'mod_margic';
    $ratingoptions->ratingarea = 'entry';
    $ratingoptions->modulename = 'margic';
    $ratingoptions->moduleid = $margic->id;
    $ratingoptions->userid = $userid;
    $ratingoptions->aggregationmethod = $margic->assessed;
    $ratingoptions->scaleid = $margic->scale;
    $ratingoptions->itemtable = 'margic_entries';
    $ratingoptions->itemtableusercolumn = 'userid';

    $rm = new rating_manager();

    return $rm->get_user_grades($ratingoptions);
}

/**
 * Update margic activity grades.
 *
 * @category grade
 * @param object $margic If is null, then all diaries.
 * @param int $userid If is false, then all users.
 * @param boolean $nullifnone Return null if grade does not exist.
 */
function margic_update_grades($margic, $userid = 0, $nullifnone = true) {
    global $CFG;
    require_once($CFG->libdir . '/gradelib.php');

    $cm = get_coursemodule_from_instance('margic', $margic->id);
    $margic->cmidnumber = $cm->idnumber;

    if (!$margic->assessed) {
        margic_grade_item_update($margic);
    } else if ($grades = margic_get_user_grades($margic, $userid)) {
        margic_grade_item_update($margic, $grades);
    } else if ($userid && $nullifnone) {
        $grade = new stdClass();
        $grade->userid = $userid;
        $grade->rawgrade = null;
        margic_grade_item_update($margic, $grade);
    } else {
        margic_grade_item_update($margic);
    }
}

/**
 * Update or create grade item for given margic.
 *
 * @param stdClass $margic Object with extra cmidnumber.
 * @param array $grades optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return int 0 if ok, error code otherwise.
 */
function margic_grade_item_update($margic, $grades = null) {
    global $CFG;
    require_once($CFG->libdir . '/gradelib.php');

    $params = array(
        'itemname' => $margic->name,
        'idnumber' => $margic->cmidnumber
    );

    if (! $margic->assessed or $margic->scale == 0) {
        $params['gradetype'] = GRADE_TYPE_NONE;
    } else if ($margic->scale > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax'] = $margic->scale;
        $params['grademin'] = 0;
    } else if ($margic->scale < 0) {
        $params['gradetype'] = GRADE_TYPE_SCALE;
        $params['scaleid'] = - $margic->scale;
    }

    if ($grades === 'reset') {
        $params['reset'] = true;
        $grades = null;
    }

    return grade_update('mod/margic', $margic->course, 'mod', 'margic', $margic->id, 0, $grades, $params);
}

/**
 * Delete grade item for given margic.
 *
 * @param object $margic
 * @return object grade_item
 */
function margic_grade_item_delete($margic) {
    global $CFG;

    require_once($CFG->libdir . '/gradelib.php');

    return grade_update('mod/margic', $margic->course, 'mod', 'margic', $margic->id, 0, null, array(
        'deleted' => 1
    ));
}

/**
 * Checks if scale is being used by any instance of margic.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param int $scaleid
 * @return boolean True if the scale is used by any dimargicary.
 */
function margic_scale_used_anywhere($scaleid) {
    global $DB;

    if (empty($scaleid)) {
        return false;
    }

    return $DB->record_exists_select('margic', "scale = ? and assessed > 0", [$scaleid * -1]);
}

/**
 * Return only the users that have entries in the specified margic activity.
 * Used by report.php.
 *
 * @param object $margic
 * @param object $currentgroup
 * @param object $sortoption return object $margics
 */
/* function margic_get_users_done($margic, $currentgroup, $sortoption) {
    global $DB;

    $params = array();

    $sql = "SELECT DISTINCT u.* FROM {margic_entries} de
              JOIN {user} u ON de.userid = u.id ";

    // Group users.
    if ($currentgroup != 0) {
        $sql .= "JOIN {groups_members} gm ON gm.userid = u.id AND gm.groupid = ?";
        $params[] = $currentgroup;
    }
    // 20201014 Changed to a sort option preference to sort lastname ascending or descending.
    $sql .= " WHERE de.margic = ? ORDER BY " . $sortoption;

    $params[] = $margic->id;

    $margics = $DB->get_records_sql($sql, $params);

    $cm = margic_get_coursemodule($margic->id);
    if (! $margics || ! $cm) {
        return null;
    }

    // Remove unenrolled participants.
    foreach ($margics as $key => $user) {

        $context = context_module::instance($cm->id);

        $canadd = has_capability('mod/margic:addentries', $context, $user);
        $entriesmanager = has_capability('mod/margic:manageentries', $context, $user);

        if (! $entriesmanager and ! $canadd) {
            unset($margics[$key]);
        }
    }
    return $margics;
} */

/**
 * Counts all the margic entries (optionally in a given group).
 *
 * @param array $margic
 * @param int $groupid
 * @return int count($margics) Count of margic entries.
 */
function margic_count_entries($margic, $groupid = 0) {
    global $DB;

    $cm = margic_get_coursemodule($margic->id);
    $context = context_module::instance($cm->id);

    if ($groupid) { // How many in a particular group?

        $sql = "SELECT DISTINCT u.id FROM {margic_entries} d
                  JOIN {groups_members} g ON g.userid = d.userid
                  JOIN {user} u ON u.id = g.userid
                 WHERE d.margic = ? AND g.groupid = ?";
        $margics = $DB->get_records_sql($sql, array(
            $margic->id,
            $groupid
        ));
    } else { // Count all the entries from the whole course.

        $sql = "SELECT DISTINCT u.id FROM {margic_entries} d
                  JOIN {user} u ON u.id = d.userid
                 WHERE d.margic = ?";
        $margics = $DB->get_records_sql($sql, array(
            $margic->id
        ));
    }

    if (! $margics) {
        return 0;
    }

    $canadd = get_users_by_capability($context, 'mod/margic:addentries', 'u.id');
    $entriesmanager = get_users_by_capability($context, 'mod/margic:manageentries', 'u.id');

    // Remove unenrolled participants.
    foreach ($margics as $userid => $notused) {

        if (! isset($entriesmanager[$userid]) && ! isset($canadd[$userid])) {
            unset($margics[$userid]);
        }
    }

    return count($margics);
}

/**
 * Return entries that have not been emailed.
 *
 * @param int $cutofftime
 * @return object
 */
function margic_get_unmailed_graded($cutofftime) {
    global $DB;

    $sql = "SELECT de.*, d.course, d.name FROM {margic_entries} de
              JOIN {margic} d ON de.margic = d.id
             WHERE de.mailed = '0' AND de.timemarked < ? AND de.timemarked > 0";
    return $DB->get_records_sql($sql, array(
        $cutofftime
    ));
}

/**
 * Return margic log info.
 *
 * @param string $log
 * @return object
 */
function margic_log_info($log) {
    global $DB;

    $sql = "SELECT d.*, u.firstname, u.lastname
              FROM {margic} d
              JOIN {margic_entries} de ON de.margic = d.id
              JOIN {user} u ON u.id = de.userid
             WHERE de.id = ?";
    return $DB->get_record_sql($sql, array(
        $log->info
    ));
}

/**
 * Returns the margic instance course_module id.
 *
 * @param integer $margicid
 * @return object
 */
function margic_get_coursemodule($margicid) {
    global $DB;

    return $DB->get_record_sql("SELECT cm.id FROM {course_modules} cm
                                  JOIN {modules} m ON m.id = cm.module
                                 WHERE cm.instance = ? AND m.name = 'margic'", array(
        $margicid
    ));
}

/**
 * Serves the margic files.
 * THIS FUNCTION MAY BE ORPHANED. APPEARS TO BE SO IN JOURNAL.
 *
 * @param stdClass $course Course object.
 * @param stdClass $cm Course module object.
 * @param stdClass $context Context object.
 * @param string $filearea File area.
 * @param array $args Extra arguments.
 * @param bool $forcedownload Whether or not force download.
 * @param array $options Additional options affecting the file serving.
 * @return bool False if file not found, does not return if found - just send the file.
 */
function margic_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    global $DB, $USER;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_course_login($course, true, $cm);

    if (! $course->visible && ! has_capability('moodle/course:viewhiddencourses', $context)) {
        return false;
    }

    // Args[0] should be the entry id.
    $entryid = intval(array_shift($args));
    $entry = $DB->get_record('margic_entries', array(
        'id' => $entryid
    ), 'id, userid', MUST_EXIST);

    $canmanage = has_capability('mod/margic:manageentries', $context);
    if (! $canmanage && ! has_capability('mod/margic:addentries', $context)) {
        // Even if it is your own entry.
        return false;
    }

    // Students can only see their own entry.
    if (! $canmanage && $USER->id !== $entry->userid) {
        return false;
    }

    if ($filearea !== 'entry' && $filearea !== 'feedback') {
        return false;
    }

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/mod_margic/$filearea/$entryid/$relativepath";
    $file = $fs->get_file_by_hash(sha1($fullpath));

    // Finally send the file.
    send_stored_file($file, null, 0, $forcedownload, $options);
}

/**
 * Extends the global navigation tree by adding mod_margic nodes if there is a relevant content.
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $margicnode An object representing the navigation tree node.
 * @param  stdClass $course Course object
 * @param  context_course $coursecontext Course context
 */
function margic_extend_navigation_course($margicnode, $course, $coursecontext) {
    $modinfo = get_fast_modinfo($course); // Get mod_fast_modinfo from $course.
    $index = 1; // Set index.
    foreach ($modinfo->get_cms() as $cmid => $cm) { // Search existing course modules for this course.
        if ($index == 1 && $cm->modname == "margic" && $cm->uservisible && $cm->available) { // Look if module (in this case margic) exists, is uservisible and available.
            $url = new moodle_url("/mod/" . $cm->modname . "/index.php", array("id" => $course->id)); // Set url for the link in the navigation node.
            $node = navigation_node::create(get_string('viewallmargics', 'margic'), $url, navigation_node::TYPE_CUSTOM, null , null , null);
            $margicnode->add_node($node);
            $index++;
        }
    }
}
