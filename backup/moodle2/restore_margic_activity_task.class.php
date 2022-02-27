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
 * Define all the backup steps that will be used by the backup_margic_activity_task
 *
 * @package   mod_margic
 * @copyright 2022 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/margic/backup/moodle2/restore_margic_stepslib.php');

/**
 * margic restore task that provides all the settings and steps to perform one complete restore of the activity.
 *
 * @package   mod_margic
 * @copyright 2022 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_margic_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have.
     */
    protected function define_my_settings() {
        // No particular settings for this activity.
    }

    /**
     * Define (add) particular steps this activity can have.
     */
    protected function define_my_steps() {
        $this->add_step(new restore_margic_activity_structure_step('margic_structure', 'margic.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder.
     *
     * @return array
     */
    public static function define_decode_contents() {
        $contents = array();
        $contents[] = new restore_decode_content('margic', array(
            'intro'
        ), 'margic');
        $contents[] = new restore_decode_content('margic_entries', array(
            'text',
            'entrycomment'
        ), 'margic_entry');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder.
     *
     * @return array of restore_decode_rule
     */
    public static function define_decode_rules() {
        $rules = array();
        // List of margic's in the course.
        $rules[] = new restore_decode_rule('margicINDEX', '/mod/margic/index.php?id=$1', 'course');
        // margic views by cm->id.
        $rules[] = new restore_decode_rule('margicVIEWBYID', '/mod/margic/view.php?id=$1', 'course_module');
        // margic reports by cm->id.
        $rules[] = new restore_decode_rule('margicREPORT', '/mod/margic/report.php?id=$1', 'course_module');
        // margic user edits by cm->id.
        $rules[] = new restore_decode_rule('margicEDIT', '/mod/margic/edit.php?id=$1', 'course_module');

        return $rules;
    }

    /**
     * Added fix from https://tracker.moodle.org/browse/MDL-34172
     */

    /**
     * Define the restore log rules that will be applied
     * by the restore_logs_processor when restoring
     * margic logs.
     * It must return one array
     * of restore_log_rule objects.
     *
     * @return array of restore_log_rule
     */
    public static function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('margic', 'view', 'view.php?id={course_module}', '{margic}');
        $rules[] = new restore_log_rule('margic', 'view responses', 'report.php?id={course_module}', '{margic}');
        $rules[] = new restore_log_rule('margic', 'add entry', 'edit.php?id={course_module}', '{margic}');
        $rules[] = new restore_log_rule('margic', 'update entry', 'edit.php?id={course_module}', '{margic}');
        $rules[] = new restore_log_rule('margic', 'update feedback', 'report.php?id={course_module}', '{margic}');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the restore_logs_processor when restoring
     * course logs.
     * It must return one array
     * of restore_log_rule objects.
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0).
     *
     * @return array
     */
    public static function define_restore_log_rules_for_course() {
        $rules = array();

        $rules[] = new restore_log_rule('margic', 'view all', 'index.php?id={course}', null);

        return $rules;
    }
}
