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
 * This page opens the current lib instance of mod margic.
 *
 * @package   mod_margic
 * @copyright 2022 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_margic\local\helper;

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $margic the margic data.
 * @return int margic ID.
 */
function margic_add_instance($margic) {
    global $DB;

    if (empty($margic->assessed)) {
        $margic->assessed = 0;
    }

    if (empty($margic->ratingtime) || empty($margic->assessed)) {
        $margic->assesstimestart = 0;
        $margic->assesstimefinish = 0;
    }
    $margic->timemodified = time();
    $margic->id = $DB->insert_record('margic', $margic);

    // Add calendar dates.
    helper::margic_update_calendar($margic, $margic->coursemodule);

    // Add expected completion date.
    if (! empty($margic->completionexpected)) {
        \core_completion\api::update_completion_date_event($margic->coursemodule,
            'margic', $margic->id, $margic->completionexpected);
    }

    margic_grade_item_update($margic);

    if (isset($margic->errortypes) && !empty($margic->errortypes)) {
        // Add errortypes for margic.
        $priority = 1;
        foreach ($margic->errortypes as $id => $checked) {
            if ($checked) {
                $type = $DB->get_record('margic_errortype_templates', array('id' => $id));
                $type->margic = $margic->id;
                $type->priority = $priority;

                $priority += 1;

                $DB->insert_record('margic_errortypes', $type);
            }
        }
    }

    return $margic->id;
}

/**
 *
 * Given an object containing all the necessary margic data,
 * will update an existing instance with new margic data.
 *
 * @param object $margic the margic data.
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
    // for count and sum aggregation types the grade we check to make sure they do not exceed the scale (i.e. max score)
    // when calculating the grade.
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
    helper::margic_update_calendar($margic, $margic->coursemodule);

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

    // Delete error types for margic.
    $DB->delete_records("margic_errortypes", array("margic" => $margic->id));

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
 * @uses FEATURE_RATE
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE__BACKUP_MOODLE2
 * @param string $feature Constant for requested feature.
 * @return mixed True if module supports feature, null if it doesn't.
 */
function margic_supports($feature) {

    // Adding support for FEATURE_MOD_PURPOSE (MDL-71457) and providing backward compatibility (pre-v4.0).
    if (defined('FEATURE_MOD_PURPOSE') && $feature === FEATURE_MOD_PURPOSE) {
        return MOD_PURPOSE_COLLABORATION;
    }

    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return true;
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
 * Returns a summary of data activity of this user.
 *
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $margic
 * @return object|null
 */
function margic_user_outline($course, $user, $mod, $margic) {
    global $DB;

    if ($count = $DB->count_records("margic_entries", array("userid" => $user->id, "margic" => $margic->id))) {
        $result = new stdClass();
        $result->info = $count . ' ' .  get_string("entries");
        return $result;
    }
    return null;
}

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

    $params = array(
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

    $sql = "SELECT e.id, e.timecreated, cm.id AS cmid, $namefields
              FROM {margic_entries} e
              JOIN {margic} d ON d.id = e.margic
              JOIN {course_modules} cm ON cm.instance = d.id
              JOIN {modules} md ON md.id = cm.module
              JOIN {user} u ON u.id = e.userid
             WHERE e.timecreated > ? AND d.course = ? AND md.name = ?
          ORDER BY timecreated DESC
    ";

    $newentries = $DB->get_records_sql($sql, $params);

    $modinfo = get_fast_modinfo($course);

    $show = array();

    foreach ($newentries as $entry) {
        if (! array_key_exists($entry->cmid, $modinfo->get_cms())) {
            continue;
        }
        $cm = $modinfo->get_cm($entry->cmid);

        if (! $cm->uservisible) {
            continue;
        }
        if ($entry->userid == $USER->id) {
            $show[] = $entry;
            continue;
        }
        $context = context_module::instance($entry->cmid);

        $teacher = has_capability('mod/margic:manageentries', $context);

        // Only teachers can see other students entries.
        if (!$teacher) {
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
            $usersgroups = groups_get_all_groups($course->id, $entry->userid, $cm->groupingid);
            if (is_array($usersgroups)) {
                $usersgroups = array_keys($usersgroups);
                $intersect = array_intersect($usersgroups, $modinfo->get_groups($cm->groupingid));
                if (empty($intersect)) {
                    continue;
                }
            }
        }
        $show[] = $entry;
    }

    if (empty($show)) {
        return false;
    }

    echo $OUTPUT->heading(get_string('newmargicentries', 'margic') . ':', 6);

    foreach ($show as $entry) {
        $cm = $modinfo->get_cm($entry->cmid);
        $context = context_module::instance($entry->cmid);
        $link = $CFG->wwwroot . '/mod/margic/view.php?id=' . $cm->id;
        print_recent_activity_note($entry->timecreated, $entry, $cm->name, $link, false, $viewfullnames);
        echo '<br>';
    }

    return true;
}

/**
 * Returns all margics since a given time.
 *
 * @param array $activities The activity information is returned in this array
 * @param int $index The current index in the activities array
 * @param int $timestart The earliest activity to show
 * @param int $courseid Limit the search to this course
 * @param int $cmid The course module id
 * @param int $userid Optional user id
 * @param int $groupid Optional group id
 * @return void
 */
function margic_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid,
    $cmid, $userid=0, $groupid=0) {

    global $CFG, $COURSE, $USER, $DB;

    if ($COURSE->id == $courseid) {
        $course = $COURSE;
    } else {
        $course = $DB->get_record('course', array('id' => $courseid));
    }

    $modinfo = get_fast_modinfo($course);

    $cm = $modinfo->get_cm($cmid);
    $params = array();
    if ($userid) {
        $userselect = 'AND u.id = :userid';
        $params['userid'] = $userid;
    } else {
        $userselect = '';
    }

    if ($groupid) {
        $groupselect = 'AND gm.groupid = :groupid';
        $groupjoin   = 'JOIN {groups_members} gm ON  gm.userid=u.id';
        $params['groupid'] = $groupid;
    } else {
        $groupselect = '';
        $groupjoin   = '';
    }

    $params['cminstance'] = $cm->instance;
    $params['timestart'] = $timestart;
    $params['submitted'] = 1;

    if ($CFG->branch < 311) {
        $userfields = user_picture::fields('u', null, 'userid');
    } else {
        $userfieldsapi = \core_user\fields::for_userpic();
        $userfields = $userfieldsapi->get_sql('u', false, '', 'userid', false)->selects;
    }

    $entries = $DB->get_records_sql(
        'SELECT e.id, e.timecreated, ' . $userfields .
        '  FROM {margic_entries} e
        JOIN {margic} m ON m.id = e.margic
        JOIN {user} u ON u.id = e.userid ' . $groupjoin .
        '  WHERE e.timecreated > :timestart AND
            m.id = :cminstance
            ' . $userselect . ' ' . $groupselect .
            ' ORDER BY e.timecreated DESC', $params);

    if (!$entries) {
         return;
    }

    $groupmode       = groups_get_activity_groupmode($cm, $course);
    $cmcontext       = context_module::instance($cm->id);
    $grader          = has_capability('moodle/grade:viewall', $cmcontext);
    $accessallgroups = has_capability('moodle/site:accessallgroups', $cmcontext);
    $viewfullnames   = has_capability('moodle/site:viewfullnames', $cmcontext);
    $teacher = has_capability('mod/margic:manageentries', $cmcontext);

    $show = array();
    foreach ($entries as $entry) {
        if ($entry->userid == $USER->id) {
            $show[] = $entry;
            continue;
        }

        // Only teachers can see other students entries.
        if (!$teacher) {
            continue;
        }

        if ($groupmode == SEPARATEGROUPS && !$accessallgroups) {
            if (isguestuser()) {
                // Shortcut - guest user does not belong into any group.
                continue;
            }

            // This will be slow - show only users that share group with me in this cm.
            if (!$modinfo->get_groups($cm->groupingid)) {
                continue;
            }
            $usersgroups = groups_get_all_groups($course->id, $entry->userid, $cm->groupingid);
            if (is_array($usersgroups)) {
                $usersgroups = array_keys($usersgroups);
                $intersect = array_intersect($usersgroups, $modinfo->get_groups($cm->groupingid));
                if (empty($intersect)) {
                    continue;
                }
            }
        }
        $show[] = $entry;
    }

    if (empty($show)) {
        return;
    }

    if ($grader) {
        require_once($CFG->libdir.'/gradelib.php');
        $userids = array();
        foreach ($show as $id => $entry) {
            $userids[] = $entry->userid;
        }
        $grades = grade_get_grades($courseid, 'mod', 'margic', $cm->instance, $userids);
    }

    $aname = format_string($cm->name, true);
    foreach ($show as $entry) {
        $activity = new stdClass();

        $activity->type         = 'margic';
        $activity->cmid         = $cm->id;
        $activity->name         = $aname;
        $activity->sectionnum   = $cm->sectionnum;
        $activity->timestamp    = $entry->timecreated;
        $activity->user         = new stdClass();
        if ($grader) {
            $activity->grade = $grades->items[0]->grades[$entry->userid]->str_long_grade;
        }

        if ($CFG->branch < 311) {
            $userfields = explode(',', user_picture::fields());
        } else {
            $userfields = explode(',', implode(',', \core_user\fields::get_picture_fields()));
        }

        foreach ($userfields as $userfield) {
            if ($userfield == 'id') {
                // Aliased in SQL above.
                $activity->user->{$userfield} = $entry->userid;
            } else {
                $activity->user->{$userfield} = $entry->{$userfield};
            }
        }
        $activity->user->fullname = fullname($entry, $viewfullnames);

        $activities[$index++] = $activity;
    }

    return;
}

/**
 * Print recent activity from all margics in a given course
 *
 * This is used by course/recent.php
 * @param stdClass $activity
 * @param int $courseid
 * @param bool $detail
 * @param array $modnames
 */
function margic_print_recent_mod_activity($activity, $courseid, $detail, $modnames) {
    global $CFG, $OUTPUT;

    echo '<table border="0" cellpadding="3" cellspacing="0" class="margic-recent">';

    echo '<tr><td class="userpicture" valign="top">';
    echo $OUTPUT->user_picture($activity->user);
    echo '</td><td>';

    if ($detail) {
        $modname = $modnames[$activity->type];
        echo '<div class="title">';
        echo $OUTPUT->image_icon('icon', $modname, 'margic');
        echo '<a href="' . $CFG->wwwroot . '/mod/margic/view.php?id=' . $activity->cmid . '">';
        echo $activity->name;
        echo '</a>';
        echo '</div>';
    }

    echo '<div class="grade"><strong>';
    echo '<a href="' . $CFG->wwwroot . '/mod/margic/view.php?id=' . $activity->cmid . '">'
        . get_string('entryadded', 'mod_margic') . '</a>';
    echo '</strong></div>';

    echo '<div class="user">';
    echo "<a href=\"$CFG->wwwroot/user/view.php?id={$activity->user->id}&amp;course=$courseid\">";
    echo "{$activity->user->fullname}</a> - " . userdate($activity->timestamp);
    echo '</div>';

    echo '</td></tr></table>';
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

    $mform->addElement('checkbox', 'reset_margic_errortypes', get_string('deleteerrortypes', 'margic'));
}

/**
 * Course reset form defaults.
 *
 * @param object $course
 * @return array
 */
function margic_reset_course_form_defaults($course) {
    return array('reset_margic_all' => 1, 'reset_margic_errortypes' => 1);
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

    // Delete entries and their annotations, files and ratings.
    if (!empty($data->reset_margic_all)) {

        $fs = get_file_storage();

        // Get ratings manager.
        $rm = new rating_manager();
        $ratingdeloptions = new stdClass;
        $ratingdeloptions->component = 'mod_margic';
        $ratingdeloptions->ratingarea = 'entry';

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

    // Delete errortypes.
    if (!empty($data->reset_margic_errortypes) ) {
        $DB->delete_records_select('margic_errortypes', "margic IN ($sql)", $params);

        $status[] = array('component' => $modulename, 'item' => get_string('errortypesdeleted', 'margic'), 'error' => false);

    }

    // Updating dates - shift may be negative too.
    if ($data->timeshift) {
        // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
        // See MDL-9367.
        shift_course_mod_dates('margic', array('assesstimestart', 'assesstimefinish', 'timeopen', 'timeclose'),
            $data->timeshift, $data->courseid);
        $status[] = array('component' => $modulename, 'item' => get_string('datechanged'), 'error' => false);
    }

    return $status;
}

/**
 * Removes all grades in the margic gradebook
 *
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

    if (! $margic->assessed || $margic->scale == 0) {
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
 * Serves the margic files.
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
        if ($index == 1 && $cm->modname == "margic" && $cm->uservisible && $cm->available) {
            $url = new moodle_url("/mod/" . $cm->modname . "/index.php",
                array("id" => $course->id)); // Set url for the link in the navigation node.
            $node = navigation_node::create(get_string('viewallmargics', 'margic'), $url,
                navigation_node::TYPE_CUSTOM, null , null , null);
            $margicnode->add_node($node);
            $index++;
        }
    }
}
