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
 * @copyright AL Rachels (drachels@drachels.com)
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
 * @copyright AL Rachels (drachels@drachels.com)
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
     * Prints the currently selected margic entry of student identified as $user, on the report page.
     *
     * @param integer $course
     * @param integer $margic
     * @param integer $user
     * @param integer $entry
     * @param integer $teachers
     * @param integer $grades
     */
    public static function margic_print_user_entry($course, $margic, $user, $entry, $teachers, $grades) {
        global $USER, $OUTPUT, $DB, $CFG;
        $id = required_param('id', PARAM_INT); // Course module.

        // 20210605 Changed to this format.
        require_once(__DIR__ .'/../../../../lib/gradelib.php');

        $dcolor3 = get_config('mod_margic', 'entrybgc');
        $dcolor4 = get_config('mod_margic', 'entrytextbgc');

        // Create a table for the current users entry with area for teacher feedback.

        // Move id.
        // echo '<table class="margicuserentry" id="entry-'.$user->id.'">';
        echo '<table class="margicuserentry">';

        if ($entry) {
            // Add an entry label followed by the date of the entry.
            echo '<tr>';
            echo '<td style="width:35px;">'.get_string('entry', 'margic').':</td><td>';
            echo userdate($entry->timecreated);
            // 20201202 Added link to all entries for a single user.
            echo '  <a href="reportsingle.php?id='.$id
            .'&user='.$user->id
            .'&action=allentries">'.get_string('reportsingle', 'margic')
            .'</a></td><td></td>';
            echo '</tr>';
        }

        // Add first of two rows, this one containing details showing the user, timecreated, and time last edited.
        echo '<tr>';
        echo '<td class="userpix" rowspan="2">';
        echo $OUTPUT->user_picture($user, array(
            'courseid' => $course->id,
            'alttext' => true
        ));
        echo '</td>';
        echo '<td class="userfullname">'.fullname($user).'<br>';
        if ($entry) {
            // 20210606 Added word/character counts.
            $rawwordcount = count_words($entry->text);
            $rawwordcharcount = strlen($entry->text);
            $rawwordspacecount = substr_count($entry->text, ' ');
            $plaintxt = htmlspecialchars(trim(strip_tags($entry->text)));
            $clnwordcount = count_words($plaintxt);
            $clnwordspacecount = substr_count($plaintxt, ' ');
            $clnwordcharcount = ((strlen($plaintxt)) - $clnwordspacecount);
            $stdwordcount = (strlen($plaintxt)) / 5;
            $stdwordcharcount = strlen($plaintxt);
            $stdwordspacecount = substr_count($plaintxt, ' ');
            // 20210604 Added for Details in each report entry.
            echo '<div class="lastedit">'
                .get_string('details', 'margic').' '
                .get_string('numwordsraw', 'margic', ['wordscount' => $rawwordcount,
                                                     'charscount' => $rawwordcharcount,
                                                     'spacescount' => $rawwordspacecount]).'<br>'
                .get_string('numwordscln', 'margic', ['one' => $clnwordcount,
                                                     'two' => $clnwordcharcount,
                                                     'three' => $clnwordspacecount]).'<br>'
                .get_string('numwordsstd', 'margic', ['one' => $stdwordcount,
                                                     'two' => $stdwordcharcount,
                                                     'three' => $stdwordspacecount]).'<br>'
                .get_string("timecreated", 'margic').':  '
                .userdate($entry->timecreated).' '
                .get_string("lastedited").': '
                .userdate($entry->timemodified).' </div>';
        }

        echo '</td><td style="width:55px;"></td>';
        echo '</tr>';

        // Add the second of two rows, this one containing the users text for this entry.
        echo '<tr><td>';

        // [margic]
        if (isset($entry->id)) {
            echo '<div id="entry-'.$entry->id.'" class="entry originaltext" style="background: '.$dcolor4.';">';
        } else {
            echo '<div class="entry" style="background: '.$dcolor4.';">';
        }

        // If there is a user entry, format it and show it.
        if ($entry) {
            echo self::margic_format_entry_text($entry, $course);
        } else {
            print_string("noentry", "margic");
        }
        echo '</div></td><td style="width:55px;"></td></tr>';

        // If there is a user entry, add a teacher feedback area for grade
        // and comments. Add previous grades and comments, if available.
        if ($entry) {
            echo '<tr>';
            echo '<td class="userpix">';
            if (! $entry->teacher) {
                $entry->teacher = $USER->id;
            }
            if (empty($teachers[$entry->teacher])) {
                $teachers[$entry->teacher] = $DB->get_record('user', array(
                    'id' => $entry->teacher
                ));
            }

            // 20200816 Get the current rating for this user!
            if ($margic->assessed != 0) {
                $gradinginfo = grade_get_grades($course->id, 'mod', 'margic', $margic->id, $user->id);
                $gradeitemgrademax = $gradinginfo->items[0]->grademax;
                $userfinalgrade = $gradinginfo->items[0]->grades[$user->id];
                $currentuserrating = $userfinalgrade->str_long_grade;
            } else {
                $currentuserrating = '';
            }
            $aggregatestr = self::get_margic_aggregation($margic->assessed);

            echo $OUTPUT->user_picture($teachers[$entry->teacher], array(
                'courseid' => $course->id,
                'alttext' => true
            ));
            echo '</td>';
            echo '<td>'.get_string('rating', 'margic').':  ';

            $attrs = array();
            $hiddengradestr = '';
            $gradebookgradestr = '';
            $feedbackdisabledstr = '';
            $feedbacktext = $entry->entrycomment;

            // If the grade was modified from the gradebook disable edition also skip if margic is not graded.
            $gradinginfo = grade_get_grades($course->id, 'mod', 'margic', $entry->margic, array(
                $user->id
            ));

            if (! empty($gradinginfo->items[0]->grades[$entry->userid]->str_long_grade)) {
                if ($gradingdisabled = $gradinginfo->items[0]->grades[$user->id]->locked
                    || $gradinginfo->items[0]->grades[$user->id]->overridden) {

                    $attrs['disabled'] = 'disabled';
                    $hiddengradestr = '<input type="hidden" name="r'.$entry->id.'" value="'.$entry->rating.'"/>';
                    $gradebooklink = '<a href="'.$CFG->wwwroot.'/grade/report/grader/index.php?id='.$course->id.'">';
                    $gradebooklink .= $gradinginfo->items[0]->grades[$user->id]->str_long_grade.'</a>';
                    $gradebookgradestr = '<br/>'.get_string("gradeingradebook", "margic").':&nbsp;'.$gradebooklink;

                    $feedbackdisabledstr = 'disabled="disabled"';
                    $feedbacktext = $gradinginfo->items[0]->grades[$user->id]->str_feedback;
                }
            }

            // 20210510 Modified Grade selector to check for Moodle version.
            $attrs['id'] = 'r'.$entry->id;
            if ($CFG->branch < 311) {
                echo html_writer::label(fullname($user)." ".get_string('grade'),
                    'r'.$entry->id, true, array('class' => 'accesshide'));
            } else {
                echo html_writer::label(fullname($user)." ".get_string('gradenoun'),
                    'r'.$entry->id, true, array('class' => 'accesshide'));
            }

            if ($margic->assessed > 0) {
                echo html_writer::select($grades, 'r'.$entry->id, $entry->rating, get_string("nograde").'...', $attrs);
            }
            echo $hiddengradestr;

            // Rewrote next three lines to show entry needs to be regraded due to resubmission.
            if (! empty($entry->timemarked) && $entry->timemodified > $entry->timemarked) {
                echo ' <span class="needsedit">'.get_string("needsregrading", "margic").' </span>';
            } else if ($entry->timemarked) {
                echo ' <span class="lastedit">'.userdate($entry->timemarked).' </span>';
            }
            echo $gradebookgradestr;

            // 20200816 Added overall rating type and rating.
            echo '<br>'.$aggregatestr.' '.$currentuserrating;

            // Feedback text.
            echo html_writer::label(fullname($user)." ".get_string('feedback'), 'c'.$entry->id, true, array(
                'class' => 'accesshide'
            ));
            echo '<p><textarea id="c'.$entry->id.'" name="c'.$entry->id.'" rows="6" cols="60" $feedbackdisabledstr>';
            echo p($feedbacktext);
            echo '</textarea></p>';

            if ($feedbackdisabledstr != '') {
                echo '<input type="hidden" name="c'.$entry->id.'" value="'.$feedbacktext.'"/>';
            }
            echo '</td></tr>';
        }
        echo '</table>';
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
     * Get the latest entry in mdl_margic_entries for the current user.
     *
     * Used in lib.php.
     *
     * @param int $margic ID of the current margic activity.
     * @param int $user ID of the current user.
     * @param int $timecreated Unix time when margic entry was created.
     * @param int $timemodified Unix time when margic entry was last changed.
     */
    /* public static function get_grade_entry($margic, $user, $timecreated, $timemodified) {
        global $USER, $DB, $CFG;
        $sql = "SELECT * FROM ".$CFG->prefix."margic_entries"
                     ." WHERE margic = ".$margic
                        ."AND userid = ".$user
                        ."AND timecreated = ".$timecreated
                        ."AND timemodified = ".$timemodified
                        ."ORDER BY timecreated";

        if ($rec = $DB->get_record_sql($sql, array())) {
            return $rec;
        } else {
            return null;
        }
    } */

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
     * @param object $context
     * @param integer $course
     * @param integer $margic
     * @param object $entry
     * @param integer $grades
     * @param bool $canmanageentries
     */
    public static function margic_return_comment_and_grade_form_for_entry($context, $course, $margic, $entry, $grades, $canmanageentries) {

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
