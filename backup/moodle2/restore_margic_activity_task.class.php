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
 * The task that provides a complete restore of mod_margic is defined here.
 *
 * @package     mod_margic
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/margic/backup/moodle2/restore_margic_stepslib.php');

/**
 * Restore task for mod_margic.
 */
class restore_margic_activity_task extends restore_activity_task {

    /**
     * Defines particular settings that this activity can have.
     */
    protected function define_my_settings() {
        return;
    }

    /**
     * Defines particular steps that this activity can have.
     *
     * @return base_step.
     */
    protected function define_my_steps() {
        $this->add_step(new restore_margic_activity_structure_step('margic_structure', 'margic.xml'));
    }

    /**
     * Defines the contents in the activity that must be processed by the link decoder.
     *
     * @return array.
     */
    public static function define_decode_contents() {
        $contents = array();

        // Define the contents (files in textareas).
        $contents[] = new restore_decode_content('margic', array('intro'), 'margic');
        $contents[] = new restore_decode_content('margic_entries', array('text', 'entrycomment'), 'margic_entry');

        return $contents;
    }

    /**
     * Defines the decoding rules for links belonging to the activity to be executed by the link decoder.
     *
     * @return array.
     */
    public static function define_decode_rules() {
        $rules = array();

        // Define the rules.

        $rules[] = new restore_decode_rule('MARGICINDEX', '/mod/margic/index.php?id=$1', 'course');
        $rules[] = new restore_decode_rule('MARGICVIEWBYID', '/mod/margic/view.php?id=$1&userid=$2', array('course_module', 'userid'));
        $rules[] = new restore_decode_rule('MARGICEDITVIEW', '/mod/margic/edit.php?id=$1', array('course_module'));
        $rules[] = new restore_decode_rule('MARGICANNOTATIONSUMMARY', '/mod/margic/annotations_summary.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('MARGICERRORTYPES', '/mod/margic/errortypes.php?id=$1', array('course_module'));

        return $rules;
    }

    /**
     * Defines the restore log rules that will be applied by the
     * restore_logs_processor when restoring mod_margic logs. It
     * must return one array of restore_log_rule objects.
     *
     * @return array.
     */
    public static function define_restore_log_rules() {
        $rules = array();

        // Define the rules.
        $rules[] = new restore_log_rule('margic', 'view', 'view.php?id={course_module}', '{margic}');
        $rules[] = new restore_log_rule('margic', 'view responses', 'view.php?id={course_module}', '{margic}');
        $rules[] = new restore_log_rule('margic', 'add entry', 'edit.php?id={course_module}', '{margic}');
        $rules[] = new restore_log_rule('margic', 'update entry', 'edit.php?id={course_module}', '{margic}');
        $rules[] = new restore_log_rule('margic', 'update feedback', 'view.php?id={course_module}', '{margic}');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the restore_logs_processor when restoring
     * course logs. It must return one array
     * of restore_log_rule objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     */
    public static function define_restore_log_rules_for_course() {
        $rules = array();

        $rules[] = new restore_log_rule('margic', 'view all', 'index.php?id={course}', null);

        return $rules;
    }
}
