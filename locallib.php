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
 * Plugin internal classes, functions and constants are defined here.
 *
 * @package     mod_margic
 * @copyright   2021 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use mod_margic\local\entrystats;
use mod_margic\local\results;

/**
 * Base class for mod_margic.
 *
 * @package   mod_margic
 * @copyright 2021 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class margic {

    /** @var context the context of the course module for this margic instance */
    private $context;

    /** @var stdClass the course this margic instance belongs to */
    private $course;

    /** @var cm_info the course module for this margic instance */
    private $cm;

    /** @var stdClass the margic record that contains the global settings for this margic instance */
    private $instance;

    /** @var string modulename prevents excessive calls to get_string */
    private $modulename;

    /** @var string mode of the margic instance */
    private $mode;

    /** @var array Array with all accessible entries of the margic instance */
    private $entries = array();

    /** @var array Array of error messages encountered during the execution of margic related operations. */
    private $errors = array();

    /**
     * Constructor for the base margic class.
     *
     * @param int $id int the course module id of the margic
     * @param int $d int the instance id of the margic
     */
    public function __construct($id, $m, $userid) {

        global $DB;

        if (isset($id)) {
            list ($course, $cm) = get_course_and_cm_from_cmid($id, 'margic');
            $context = context_module::instance($cm->id);
        } else if (isset($d)) {
            list ($course, $cm) = get_course_and_cm_from_instance($m, 'margic');
            $context = context_module::instance($cm->id);
        } else {
            throw new moodle_exception('missingparameter');
        }

        $this->context = $context;

        $this->course = $course;

        $this->cm = cm_info::create($cm);

        $this->instance = $DB->get_record('margic', array('id' => $this->cm->instance));

        $this->modulename = get_string('modulename', 'mod_margic');

        if (has_capability('mod/margic:manageentries', $context)) {
            $this->mode = 'allentries';
        } else {
            $this->mode = 'ownentries';
        }

        if ($this->mode == 'allentries') {

            if ($userid && $userid != 0) {
                $this->entries = $DB->get_records('margic_entries', array('margic' => $this->instance->id, 'userid' => $userid));;
            } else {
                $this->entries = $DB->get_records('margic_entries', array('margic' => $this->instance->id));;
            }

        } else if ($this->mode == 'ownentries'){
            global $USER;
            $this->entries = $DB->get_records('margic_entries', array('margic' => $this->instance->id, 'userid' => $USER->id));;
        }

        $gradingstr = get_string('needsgrading', 'margic');
        $regradingstr = get_string('needsregrading', 'margic');

        foreach ($this->entries as $i => $entry) {
            $this->entries[$i]->stats = entrystats::get_entry_stats($entry->text, $entry->timecreated);
            $this->entries[$i]->user = $DB->get_record('user', array('id' => $entry->userid));

            if (!empty($entry->timecreated) && !empty($entry->timemodified) && empty($entry->timemarked)) {
                $this->entries[$i]->needsgrading = $gradingstr;
            } else if (!empty($entry->timemodified) && !empty($entry->timemarked) && $entry->timemodified > $entry->timemarked) {
                $this->entries[$i]->needsregrading = $regradingstr;
            } else {
                $this->entries[$i]->needsregrading = false;
            }

            $grades = make_grades_menu($this->instance->scale);

            $this->entries[$i]->gradingform = results::margic_return_comment_and_grade_form_for_entry($this->context, $this->course, $this->instance, $entry, $grades);

        }

    }

    /**
     * Singleton getter for margic instance.
     *
     * @param int $id int the course module id of margic
     * @param int $d int the instance id of the margic
     * @return string action
     */
    public static function get_margic_instance($id, $m = null, $userid) {

        static $inst = null;
        if ($inst === null) {
            $inst = new margic($id, $m, $userid);
        }
        return $inst;
    }

    /**
     * Returns the context of the margic.
     *
     * @return string action
     */
    public function get_context() {
        return $this->context;
    }

    /**
     * Returns the course of the margic.
     *
     * @return string action
     */
    public function get_course() {
        return $this->course;
    }

    /**
     * Returns the course module of the margic.
     *
     * @return string action
     */
    public function get_course_module() {
        return $this->cm;
    }

    /**
     * Returns the module instance record from the table margic.
     *
     * @return string action
     */
    public function get_module_instance() {
        return $this->instance;
    }

    /**
     * Returns the entries for the margic instance from the table margic_entries.
     *
     * @return string action
     */
    public function get_entries() {
        return array_values($this->entries);
    }

    /**
     * Returns the entries for the margic instance from the table margic_entries.
     *
     * @return string action
     */
    public function get_entries_with_keys() {
        return $this->entries;
    }
}
