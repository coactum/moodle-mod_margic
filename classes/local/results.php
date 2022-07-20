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
 * Utility class for mod_margic.
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
     * Returns if margic is available and entries are editable.
     *
     * @param var $margic
     */
    public static function margic_available($margic) {
        $timeopen = $margic->timeopen;
        $timeclose = $margic->timeclose;

        return (($timeopen == 0 || time() >= $timeopen) && ($timeclose == 0 || time() < $timeclose));
    }

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
                if ($d->timemodified == '1970-01-01 00:00:00') {
                    $d->timemodified = '';
                }

                $output = array(
                    $d->firstname,
                    $d->lastname,
                    $d->margic,
                    $d->userid,
                    $d->timecreated,
                    $d->timemodified,
                    $d->format,
                    $d->rating,
                    $d->entrycomment,
                    $d->teacher,
                    $d->timemarked,
                    $d->mailed,
                    format_text($d->text, $d->format, array('para' => false))
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
        $maxbytes = $course->maxbytes;

        // For the editor.
        $editoroptions = array(
            'trusttext' => true,
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'maxbytes' => $maxbytes,
            'context' => $context,
            'subdirs' => false,

            'editdates' => $margic->editdates, // Custom data (not really for editor).
        );

        // If maxfiles would be set to an int and more files are given the editor saves them all but saves the overcouting incorrect so that white box is diaplayed.

        // For a file attachments field (not really needed here?).
        $attachmentoptions = array(
            'subdirs' => false,
            'maxfiles' => 1,
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
     * Returns the feedback area for a specific margic entry.
     *
     * @param integer $cmid
     * @param object $context
     * @param object $course
     * @param object $margic
     * @param object $entry
     * @param array $grades
     * @param bool $canmanageentries
     */
    public static function margic_return_feedback_area_for_entry($cmid, $context, $course, $margic, $entry, $grades, $canmanageentries) {

        $grade = false;

        // If there is a user entry, add a teacher feedback area for grade
        // and comments. Add previous grades and comments, if available.
        if ($entry) {
            global $USER, $DB, $CFG, $OUTPUT;

            require_once(__DIR__ .'/../../../../lib/gradelib.php');

            if ($entry->teacher) {
                $teacher = $DB->get_record('user', array('id' => $entry->teacher));
                $teacherimage = $OUTPUT->user_picture($teacher, array('courseid' => $course->id, 'link' => true, 'includefullname' => true));
            } else {
                $teacherimage = false;
            }

            $feedbackarea = '';

            $feedbacktext = format_text($entry->entrycomment, $entry->formatcomment, array('para' => false));

            if ($canmanageentries) { // If user is teacher.
                if (! $entry->teacher) {
                    $entry->teacher = $USER->id;
                }

                $feedbackarea .= '<h3>' . get_string('feedback') . '</h3>';

                require_once($CFG->dirroot . '/mod/margic/grading_form.php');

                // Prepare editor for files.
                $data = new stdClass();
                $data->id = $cmid;
                $data->entry = $entry->id;
                $data->timecreated = $entry->timecreated;
                $data->{'feedback_' . $entry->id} = $entry->entrycomment;
                $data->{'feedback_' . $entry->id . 'format'} = $entry->formatcomment;

                list ($editoroptions, $attachmentoptions) = self::margic_get_editor_and_attachment_options($course, $context, $margic);

                $editoroptions['autosave'] = false;

                $data = file_prepare_standard_editor($data, 'feedback_' . $entry->id, $editoroptions, $context, 'mod_margic', 'feedback', $data->entry);
                // $data = file_prepare_standard_filemanager($data, 'attachment', $attachmentoptions, $context, 'mod_margic', 'attachment', $data->entry);

                $data->{'rating_' . $entry->id} = $entry->rating;

                $mform = new \mod_margic_grading_form(new \moodle_url('/mod/margic/grade_entry.php', array('id' => $cmid, 'entryid' => $entry->id)), array('courseid' => $course->id, 'margic' => $margic, 'entry' => $entry, 'grades' => $grades, 'teacherimg' => $teacherimage, 'editoroptions' => $editoroptions));

                // Set default data.
                $mform->set_data($data);

                $feedbackarea .= $mform->render();
            } else if ($feedbacktext || ! empty($entry->rating)) {  // If user is student and has rating or feedback text.
                $feedbackarea .= '<div class="ratingform" style="background-color: ' . get_config('mod_margic', 'entrytextbgc') . '"><h3>' . get_string('feedback') . '</h3>';

                $feedbackarea .= '<div class="entryheader">';
                $feedbackarea .= '<span class="teacherpicture">' . $teacherimage . '</span>';
                $feedbackarea .= ' - <span class="time">' . userdate($entry->timemarked) . '</span>';

                $feedbackarea .= '<span class="pull-right"><strong>';

                if ($margic->assessed > 0) {
                    // Gradebook preference.
                    $gradinginfo = grade_get_grades($course->id, 'mod', 'margic', $entry->margic, array(
                        $entry->userid
                    ));

                    // Branch check for string compatibility.
                    if (! empty($grades)) {
                        if ($CFG->branch > 310) {
                            $feedbackarea .= get_string('gradenoun') . ': ';
                        } else {
                            $feedbackarea .= get_string('grade') . ': ';
                        }
                        $feedbackarea .= $entry->rating . '/' . number_format($gradinginfo->items[0]->grademax, 2);
                    } else {
                        $feedbackarea .= get_string('nograde');
                    }
                }

                $feedbackarea .= '</strong></span>';
                $feedbackarea .= '</div>';

                // Feedback text.
                if ($feedbacktext) {
                    $feedbackarea .= '<hr>' . $feedbacktext;
                }

                $feedbackarea .= '</div>';
            } else {
                $feedbackarea = false;
            }
        }
        return $feedbackarea;
    }
}
