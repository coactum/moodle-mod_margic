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
 * Results utilities for margic.
 *
 * 2020071700 Moved these functions from lib.php to here.
 *
 * @package   mod_margic
 * @copyright 2022 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_margic\local;

define('MARGIC_EVENT_TYPE_OPEN', 'open');
define('MARGIC_EVENT_TYPE_CLOSE', 'close');
use mod_margic\local\results;
use stdClass;
use csv_export_writer;
use html_writer;
use context_module;
use calendar_event;

/**
 * Utility class for margic results.
 *
 * @package   mod_margic
 * @copyright 2022 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class results {

    /**
     * Update the calendar entries for this margic activity.
     *
     * @param stdClass $margic the row from the database table margic.
     * @param int $cmid The coursemodule id
     * @return bool
     */
    public static function margic_update_calendar(stdClass $margic, $cmid) {
        global $DB, $CFG;

        require_once($CFG->dirroot.'/calendar/lib.php');

        // Get CMID if not sent as part of $margic.
        if (! isset($margic->coursemodule)) {
            $cm = get_coursemodule_from_instance('margic', $margic->id, $margic->course);
            $margic->coursemodule = $cm->id;
        }

        // Margic start calendar events.
        $event = new stdClass();
        $event->eventtype = MARGIC_EVENT_TYPE_OPEN;
        // The MOOTYPER_EVENT_TYPE_OPEN event should only be an action event if no close time is specified.
        $event->type = empty($margic->timeclose) ? CALENDAR_EVENT_TYPE_ACTION : CALENDAR_EVENT_TYPE_STANDARD;

        if ($event->id = $DB->get_field('event', 'id', array(
            'modulename' => 'margic',
            'instance' => $margic->id,
            'eventtype' => $event->eventtype
        ))) {

            if ((! empty($margic->timeopen)) && ($margic->timeopen > 0)) {
                // Calendar event exists so update it.
                $event->name = get_string('calendarstart', 'margic', $margic->name);
                $event->description = format_module_intro('margic', $margic, $cmid);
                $event->timestart = $margic->timeopen;
                $event->timesort = $margic->timeopen;
                $event->visible = instance_is_visible('margic', $margic);
                $event->timeduration = 0;

                $calendarevent = calendar_event::load($event->id);
                $calendarevent->update($event, false);
            } else {
                // Calendar event is no longer needed.
                $calendarevent = calendar_event::load($event->id);
                $calendarevent->delete();
            }
        } else {
            // Event doesn't exist so create one.
            if ((! empty($margic->timeopen)) && ($margic->timeopen > 0)) {
                $event->name = get_string('calendarstart', 'margic', $margic->name);
                $event->description = format_module_intro('margic', $margic, $cmid);
                $event->courseid = $margic->course;
                $event->groupid = 0;
                $event->userid = 0;
                $event->modulename = 'margic';
                $event->instance = $margic->id;
                $event->timestart = $margic->timeopen;
                $event->timesort = $margic->timeopen;
                $event->visible = instance_is_visible('margic', $margic);
                $event->timeduration = 0;

                calendar_event::create($event, false);
            }
        }

        // Margic end calendar events.
        $event = new stdClass();
        $event->type = CALENDAR_EVENT_TYPE_ACTION;
        $event->eventtype = MARGIC_EVENT_TYPE_CLOSE;
        if ($event->id = $DB->get_field('event', 'id', array(
            'modulename' => 'margic',
            'instance' => $margic->id,
            'eventtype' => $event->eventtype
        ))) {
            if ((! empty($margic->timeclose)) && ($margic->timeclose > 0)) {
                // Calendar event exists so update it.
                $event->name = get_string('calendarend', 'margic', $margic->name);
                $event->description = format_module_intro('margic', $margic, $cmid);
                $event->timestart = $margic->timeclose;
                $event->timesort = $margic->timeclose;
                $event->visible = instance_is_visible('margic', $margic);
                $event->timeduration = 0;

                $calendarevent = calendar_event::load($event->id);
                $calendarevent->update($event, false);
            } else {
                // Calendar event is on longer needed.
                $calendarevent = calendar_event::load($event->id);
                $calendarevent->delete();
            }
        } else {
            // Event doesn't exist so create one.
            if ((! empty($margic->timeclose)) && ($margic->timeclose > 0)) {
                $event->name = get_string('calendarend', 'margic', $margic->name);
                $event->description = format_module_intro('margic', $margic, $cmid);
                $event->courseid = $margic->course;
                $event->groupid = 0;
                $event->userid = 0;
                $event->modulename = 'margic';
                $event->instance = $margic->id;
                $event->timestart = $margic->timeclose;
                $event->timesort = $margic->timeclose;
                $event->visible = instance_is_visible('margic', $margic);
                $event->timeduration = 0;

                calendar_event::create($event, false);
            }
        }
        return true;
    }

    /**
     * Returns availability status.
     * Added 20200903.
     *
     * @param var $margic
     */
    /* public static function margic_available($margic) {
        $timeopen = $margic->timeopen;
        $timeclose = $margic->timeclose;
        return (($timeopen == 0 || time() >= $timeopen) && ($timeclose == 0 || time() < $timeclose));
    } */

    /**
     * Download entries in this margic activity.
     *
     * @param array $context Context for this download.
     * @param array $course Course for this download.
     * @param array $margic margic to download.
     * @return nothing
     */
    public static function download_entries($context, $course, $margic) {
        global $CFG, $DB, $USER;
        require_once($CFG->libdir.'/csvlib.class.php');
        $data = new stdClass();
        $data->margic = $margic->id;

        // Trigger download_margic_entries event.
        $event = \mod_margic\event\download_margic_entries::create(array(
            'objectid' => $data->margic,
            'context' => $context
        ));
        $event->trigger();

        // Construct sql query and filename based on admin, teacher, or student.
        // Add filename details based on course and margic activity name.
        $csv = new csv_export_writer();
        $whichuser = ''; // Leave blank for an admin or teacher.
        if (is_siteadmin($USER->id)) {
            $whichmargic = ('AND d.margic > 0');
            $csv->filename = clean_filename(get_string('exportfilenameallentries', 'margic'));
        } else if (has_capability('mod/margic:manageentries', $context)) {
            $whichmargic = ('AND d.margic = ');
            $whichmargic .= ($margic->id);
            $csv->filename = clean_filename(get_string('exportfilenamemargicentries', 'margic'));
            $csv->filename .= '_'.clean_filename(($course->shortname).'_');
            $csv->filename .= clean_filename(($margic->name));
        } else if (has_capability('mod/margic:addentries', $context)) {
            $whichmargic = ('AND d.margic = ');
            $whichmargic .= ($margic->id);
            $whichuser = (' AND d.userid = '.$USER->id); // Not an admin or teacher so can only get their OWN entries.
            $csv->filename = clean_filename(get_string('exportfilenamemyentries', 'margic'));
            $csv->filename .= '_'.clean_filename(($course->shortname).'_');
            $csv->filename .= clean_filename(($margic->name));
        }
        $csv->filename .= '_'.clean_filename(gmdate("Ymd_Hi").'GMT.csv');

        $fields = array();
        $fields = array(
            get_string('firstname'),
            get_string('lastname'),
            get_string('pluginname', 'margic'),
            get_string('userid', 'margic'),
            get_string('timecreated', 'margic'),
            get_string('timemodified', 'margic'),
            get_string('format', 'margic'),
            get_string('rating', 'margic'),
            get_string('entrycomment', 'margic'),
            get_string('teacher', 'margic'),
            get_string('timemarked', 'margic'),
            get_string('mailed', 'margic'),
            get_string('text', 'margic')
        );
        // Add the headings to our data array.
        $csv->add_data($fields);
        if ($CFG->dbtype == 'pgsql') {
            $sql = "SELECT d.id AS entry,
                           u.firstname AS firstname,
                           u.lastname AS lastname,
                           d.margic AS margic,
                           d.userid AS userid,
                           to_char(to_timestamp(d.timecreated), 'YYYY-MM-DD HH24:MI:SS') AS timecreated,
                           to_char(to_timestamp(d.timemodified), 'YYYY-MM-DD HH24:MI:SS') AS timemodified,
                           d.text AS text,
                           d.format AS format,
                           d.rating AS rating,
                           d.entrycomment AS entrycomment,
                           d.teacher AS teacher,
                           to_char(to_timestamp(d.timemarked), 'YYYY-MM-DD HH24:MI:SS') AS timemarked,
                           d.mailed AS mailed
                      FROM {margic_entries} d
                      JOIN {user} u ON u.id = d.userid
                     WHERE d.userid > 0 ";
        } else {
            $sql = "SELECT d.id AS entry,
                           u.firstname AS 'firstname',
                           u.lastname AS 'lastname',
                           d.margic AS margic,
                           d.userid AS userid,
                           FROM_UNIXTIME(d.timecreated) AS TIMECREATED,
                           FROM_UNIXTIME(d.timemodified) AS TIMEMODIFIED,
                           d.text AS text,
                           d.format AS format,
                           d.rating AS rating,
                           d.entrycomment AS entrycomment,
                           d.teacher AS teacher,
                           FROM_UNIXTIME(d.timemarked) AS TIMEMARKED,
                           d.mailed AS mailed
                      FROM {margic_entries} d
                      JOIN {user} u ON u.id = d.userid
                     WHERE d.userid > 0 ";
        }

        $sql .= ($whichmargic);
        $sql .= ($whichuser);
        $sql .= "       GROUP BY u.lastname, u.firstname, d.margic, d.id
                  ORDER BY u.lastname ASC, u.firstname ASC, d.margic ASC, d.id ASC";

        // Add the list of users and diaries to our data array.
        if ($ds = $DB->get_records_sql($sql, $fields)) {
            foreach ($ds as $d) {
                $output = array(
                    $d->firstname,
                    $d->lastname,
                    $d->margic,
                    $d->userid,
                    $d->timecreated,
                    $d->timemodified ?: '',
                    $d->format,
                    $d->rating,
                    $d->entrycomment,
                    $d->teacher,
                    $d->timemarked,
                    $d->mailed,
                    $d->text
                );
                $csv->add_data($output);
            }
        }
        // Download the completed array.
        $csv->download_file();
    }

    /**
     * Return formatted text.
     *
     * @param array $entry
     * @param array $course
     * @param array $cm
     * @return string $entrytext Text string containing a user entry.
     * @return int $entry-format Format for user entry.
     * @return array $formatoptions Array of options for a user entry.
     */
    public static function margic_format_entry_text($entry, $course = false, $cm = false) {
        if (! $cm) {
            if ($course) {
                $courseid = $course->id;
            } else {
                $courseid = 0;
            }
            $cm = get_coursemodule_from_instance('margic', $entry->margic, $courseid);
        }

        $context = context_module::instance($cm->id);
        $entrytext = file_rewrite_pluginfile_urls($entry->text, 'pluginfile.php', $context->id, 'mod_margic', 'entry', $entry->id);

        $formatoptions = array(
            'context' => $context,
            'noclean' => false,
            'trusted' => false
        );
        return format_text($entrytext, $entry->format, $formatoptions);
    }

    /**
     * Return the editor and attachment options when editing a margic entry.
     *
     * @param stdClass $course Course object.
     * @param stdClass $context Context object.
     * @param stdClass $margic margic object.
     * @return array $editoroptions Array containing the editor and attachment options.
     * @return array $attachmentoptions Array containing the editor and attachment options.
     */
    public static function margic_get_editor_and_attachment_options($course, $context, $margic) {
        $maxfiles = 5;
        $maxbytes = $course->maxbytes;

        // 20210613 Added more custom data to use in edit_form.php to prevent illegal access.
        $editoroptions = array(
            'timeclose' => $margic->timeclose,
            'editall' => $margic->editall,
            'editdates' => $margic->editdates,
            'trusttext' => true,
            'maxfiles' => $maxfiles,
            'maxbytes' => $maxbytes,
            'context' => $context,
            'subdirs' => false
        );
        $attachmentoptions = array(
            'subdirs' => false,
            'maxfiles' => $maxfiles,
            'maxbytes' => $maxbytes
        );

        return array(
            $editoroptions,
            $attachmentoptions
        );
    }

    /**
     * Check for existing rating entry in mdl_rating for the current user.
     *
     * Used in report.php.
     *
     * @param array $ratingoptions An array of current entry data.
     * @return array $rec An entry was found, so return it for update.
     */
    public static function check_rating_entry($ratingoptions) {
        global $USER, $DB, $CFG;
        $params = array();
        $params['contextid'] = $ratingoptions->contextid;
        $params['component'] = $ratingoptions->component;
        $params['ratingarea'] = $ratingoptions->ratingarea;
        $params['itemid'] = $ratingoptions->itemid;
        $params['userid'] = $ratingoptions->userid;
        $params['timecreated'] = $ratingoptions->timecreated;

        $sql = 'SELECT * FROM '.$CFG->prefix.'rating'
                      .' WHERE contextid =  ?'
                        .' AND component =  ?'
                        .' AND ratingarea =  ?'
                        .' AND itemid =  ?'
                        .' AND userid =  ?'
                        .' AND timecreated = ?';

        if ($rec = $DB->record_exists_sql($sql, $params)) {
            $rec = $DB->get_record_sql($sql, $params);
            return ($rec);
        } else {
            return null;
        }
    }

    /**
     * Check for existing rating entry in mdl_rating for the current user.
     *
     * Used in view.php.
     *
     * @param int $aggregate The margic rating method.
     * @return string $aggregatestr Return the language string for the rating method.
     */
    public static function get_margic_aggregation($aggregate) {
        $aggregatestr = null;
        switch ($aggregate) {
            case 0:
                $aggregatestr = get_string('aggregatenone', 'rating');
                break;
            case 1:
                $aggregatestr = get_string('aggregateavg', 'rating');
                break;
            case 2:
                $aggregatestr = get_string('aggregatecount', 'rating');
                break;
            case 3:
                $aggregatestr = get_string('aggregatemax', 'rating');
                break;
            case 4:
                $aggregatestr = get_string('aggregatemin', 'rating');
                break;
            case 5:
                $aggregatestr = get_string('aggregatesum', 'rating');
                break;
            default:
                $aggregatestr = 'AVG'; // Default to this to avoid real breakage - MDL-22270.
                debugging('Incorrect call to get_aggregation_method(), incorrect aggregate method '.$aggregate, DEBUG_DEVELOPER);
        }
        return $aggregatestr;
    }

    /**
     * Returns the grade for a specific margic entry.
     *
     * @param integer $cmid
     * @param object $context
     * @param integer $course
     * @param integer $margic
     * @param object $entry
     * @param integer $grades
     * @param bool $canmanageentries
     */
    public static function margic_return_comment_and_grade_form_for_entry($cmid, $context, $course, $margic, $entry, $grades, $canmanageentries) {

        $grade = false;

        // If there is a user entry, add a teacher feedback area for grade
        // and comments. Add previous grades and comments, if available.
        if ($entry) {
            global $USER, $DB, $CFG;

            require_once(__DIR__ .'/../../../../lib/gradelib.php');

            $gradingform = '';

            if ($canmanageentries) {
                if (! $entry->teacher) {
                    $entry->teacher = $USER->id;
                }

                $attrs = array();
                $attrs['id'] = 'r'.$entry->id;

                $hiddengradestr = '';
                $gradebookgradestr = '';
                $feedbackdisabledstr = '';
                $feedbacktext = $entry->entrycomment;

                $user = $DB->get_record('user', array('id' => $entry->userid));
                $userfullname = fullname($user);

                $gradingform .= '<h3>' . get_string('feedback') . '</h3>';

                $gradingform .= '<form action="view.php" method="post">';
                $gradingform .= '<input type="hidden" name="id" value="' . $cmid . '">';
                $gradingform .= '<input type="hidden" name="sesskey" value="' . sesskey() . '">';

                // Get the current rating for this user!
                if ($margic->assessed != 0) { // Append grading area only when grading is not disabled.
                    $gradinginfo = grade_get_grades($course->id, 'mod', 'margic', $margic->id, $entry->userid);
                    $userfinalgrade = $gradinginfo->items[0]->grades[$entry->userid];
                    $currentuserrating = $userfinalgrade->str_long_grade;

                    // If the grade was modified from the gradebook disable edition also skip if margic is not graded.
                    if (! empty($gradinginfo->items[0]->grades[$entry->userid]->str_long_grade)) {
                        if ($gradingdisabled = $gradinginfo->items[0]->grades[$entry->userid]->locked
                            || $gradinginfo->items[0]->grades[$entry->userid]->overridden) {

                            $attrs['disabled'] = 'disabled';
                            $hiddengradestr = '<input type="hidden" name="r'.$entry->id.'" value="'.$entry->rating.'"/>';
                            $gradebooklink = '<a href="'.$CFG->wwwroot.'/grade/report/grader/index.php?id='.$course->id.'">';
                            $gradebooklink .= $gradinginfo->items[0]->grades[$entry->userid]->str_long_grade.'</a>';
                            $gradebookgradestr = '<br/>'.get_string("gradeingradebook", "margic").':&nbsp;'.$gradebooklink;

                            $feedbackdisabledstr = 'disabled="disabled"';
                            $feedbacktext = $gradinginfo->items[0]->grades[$entry->userid]->str_feedback;
                        }
                    }

                    $gradingform .= '<div class="row">';
                    $gradingform .= '<div class="col-md-3 col-form-label d-flex pb-0 pr-md-0">';
                    $gradingform .= get_string('rating', 'margic') . ': ';
                    $gradingform .= html_writer::label($userfullname." ".get_string('grade'), 'r'.$entry->id, true, array('class' => 'accesshide'));
                    $gradingform .= '</div><div class="col-md-9 form-inline align-items-start felement">';
                    $gradingform .= html_writer::select($grades, 'r'.$entry->id, $entry->rating, get_string("nograde").'...', $attrs);
                    $gradingform .= $hiddengradestr;

                    if ($entry->timemarked) {
                        $gradingform .= ' <span class="teacherpicture m-l-1"></span><span class="m-1">'.userdate($entry->timemarked).' </span>';
                    }

                    $gradingform .= $gradebookgradestr;
                    $gradingform .= '</div>';
                    $gradingform .= '</div>';

                    $aggregatestr = self::get_margic_aggregation($margic->assessed) . ' ' . get_string('forallentries', 'margic') . ' '. $userfullname;
                    $gradingform .= '<div class="row">';
                    $gradingform .= '<div class="col-md-3 col-form-label d-flex pb-0 pr-md-0">';

                    $gradingform .= $aggregatestr.': ';

                    $gradingform .= '</div>';

                    $gradingform .= '<div class="col-md-9 form-inline align-items-start felement">';
                    $gradingform .= $currentuserrating;
                    $gradingform .= '</div>';
                    $gradingform .= '</div>';
                }

                // Feedback text.
                $gradingform .= html_writer::label($userfullname." ".get_string('feedback'), 'c'.$entry->id, true, array(
                    'class' => 'accesshide'
                ));

                $gradingform .= '<div class="form-group row  fitem">';
                $gradingform .= '<div class="col-md-3 col-form-label d-flex pb-0 pr-md-0">';
                $gradingform .= html_writer::label(get_string('entrycomment', 'margic'). ': ', 'c'.$entry->id, true);
                $gradingform .= '</div>';
                $gradingform .= '<div class="col-md-9 form-inline align-items-start felement">';
                $gradingform .= '<textarea id="c'.$entry->id.'" name="c'.$entry->id.'" rows="6" cols="60"'. $feedbackdisabledstr .'>'.$feedbacktext.'</textarea>';
                $gradingform .= '</div>';
                $gradingform .= '</div>';

                if ($feedbackdisabledstr != '') {
                    $gradingform .= '<input type="hidden" name="c'.$entry->id.'" value="'.$feedbacktext.'"/>';
                }

                $gradingform .= '<div class="row">';
                $gradingform .= '<div class="col-md-3">';
                $gradingform .= '</div>';
                $gradingform .= '<div class="col-md-9 form-inline align-items-start felement">';
                $gradingform .= '<input type="submit" class="btn btn-primary " name="submitbutton" id="id_submitbutton" value="' . get_string("saveallfeedback", "margic") .'">';
                $gradingform .= '</div>';
                $gradingform .= '</div>';
                $gradingform .= '</form>';
            } else if (! empty($entry->entrycomment) || ! empty($entry->rating)) {
                if (! $teacher = $DB->get_record('user', array(
                    'id' => $entry->teacher
                ))) {
                    throw new moodle_exception(get_string('generalerror', 'margic'));
                }

                $gradingform .= '<div class="ratingform"><h3>' . get_string('feedback') . '</h3>';

                $gradingform .= '<div class="entryheader">';
                $gradingform .= '<span class="teacherpicture"></span>';
                $gradingform .= '<span class="author">' . fullname($teacher) . '</span> - ';
                $gradingform .= '<span class="time">' . userdate($entry->timemarked) . '</span>';

                $gradingform .= '<span class="pull-right"><strong>';

                if ($margic->assessed > 0) {
                    // Gradebook preference.
                    $gradinginfo = grade_get_grades($course->id, 'mod', 'margic', $entry->margic, array(
                        $entry->userid
                    ));

                    // Branch check for string compatibility.
                    if (! empty($grades)) {
                        if ($CFG->branch > 310) {
                            $gradingform .= get_string('gradenoun') . ': ';
                        } else {
                            $gradingform .= get_string('grade') . ': ';
                        }
                        $gradingform .= $entry->rating . '/' . number_format($gradinginfo->items[0]->grademax, 2);
                    } else {
                        print_string('nograde');
                    }
                }

                $gradingform .= '</strong></span>';
                $gradingform .= '</div><hr>';

                // Feedback text.
                $gradingform .= $entry->entrycomment;

                $gradingform .= '</div>';
            } else {
                $gradingform = false;
            }
        }
        return $gradingform;
    }
}
