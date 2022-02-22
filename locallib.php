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

    /** @var string sortmode of the margic instance */
    private $sortmode;

    /** @var int pagecount of the margic instance */
    private $pagecount;

    /** @var int Active page of the margic instance */
    private $page;

    /** @var array Array with all accessible entries of the margic instance */
    private $entries = array();

    /** @var array Array with all annotations to entries of the margic instance */
    private $annotations = array();

    /** @var array Array with all types of annotations */
    private $annotationtypes = array();

    /** @var array Array of error messages encountered during the execution of margic related operations. */
    private $errors = array();

    /**
     * Constructor for the base margic class.
     *
     * @param int $id int The course module id of the margic
     * @param int $m int The instance id of the margic
     * @param int $userid int The id of the user for that entries should be shown
     * @param int $action string The action that should be executed
     * @param int $pagecount int The pagecount that should be set
     * @param int $page int The current page number
     */
    public function __construct($id, $m, $userid, $action, $pagecount, $page) {

        global $DB, $USER;

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

        $select = "defaulttype = 1";
        $select .= " OR userid = " . $USER->id;
        $annotationtypes = $DB->get_records_select('margic_annotation_types', $select);

        $this->annotationtypes = $annotationtypes;

        $this->annotations = $DB->get_records('margic_annotations', array('margic' => $this->get_course_module()->instance));

        $select = "defaulttype = 1";
        $select .= " OR userid = " . $USER->id;
        $annotationtypes = array();
        $annotationtypes = $this->get_all_annotationtypes();

        foreach ($this->annotations as $key => $annotation) {
            $this->annotations[$key]->color = $annotationtypes[$annotation->type]->color;
        }

        if ($canmanageentries = has_capability('mod/margic:manageentries', $context)) {
            $this->mode = 'allentries';
        } else {
            $this->mode = 'ownentries';
        }

        $sortoptions = '';

        if (has_capability('mod/margic:addentries', $context)) {
            switch ($action) {
                case 'currenttooldest':
                    set_user_preference('sortoption['.$id.']', 1);
                    break;
                case 'oldesttocurrent':
                    set_user_preference('sortoption['.$id.']', 2);
                    break;
                case 'lowestgradetohighest':
                    set_user_preference('sortoption['.$id.']', 3);
                    break;
                case 'highestgradetolowest':
                    set_user_preference('sortoption['.$id.']', 4);
                    break;
                case 'latestmodified':
                    set_user_preference('sortoption['.$id.']', 5);
                    break;
                default:
                    if (!get_user_preferences('sortoption['.$id.']')) {
                        set_user_preference('sortoption['.$id.']', 1);
                    }
            }

            switch (get_user_preferences('sortoption['.$id.']')) {
                case 1:
                    $this->sortmode = get_string('currententry', 'mod_margic');
                    $sortoptions = 'timecreated DESC';
                    break;
                case 2:
                    $this->sortmode = get_string('oldestentry', 'mod_margic');
                    $sortoptions = 'timecreated ASC';
                    break;
                case 3:
                    $this->sortmode = get_string('lowestgradeentry', 'mod_margic');
                    $sortoptions = 'rating ASC, timemodified DESC';
                    break;
                case 4:
                    $this->sortmode = get_string('highestgradeentry', 'mod_margic');
                    $sortoptions = 'rating DESC, timemodified DESC';
                    break;
                case 5:
                    $this->sortmode = get_string('latestmodifiedentry', 'mod_margic');
                    $sortoptions = 'timemodified DESC, timecreated DESC';
                    break;
                default:
                    $this->sortmode = get_string('currententry', 'mod_margic');
                    $sortoptions = 'timecreated DESC';
            }

        }

        // Page selector.
        if ($pagecount !== 0) {

            if ($pagecount < 2) {
                $pagecount = 2;
            }

            $oldpagecount = get_user_preferences('margic_pagecount_'.$id);

            if ($pagecount != $oldpagecount) {
                set_user_preference('margic_pagecount_'.$id, $pagecount);
            }

            $this->pagecount = $pagecount;
        } else if ($oldpagecount = get_user_preferences('margic_pagecount_'.$id)) {
            $this->pagecount = $oldpagecount;
        } else {
            $this->pagecount = 5;
        }

        // Active page.
        if ($page) {

            if ($page < 1) {
                $page = 1;
            }

            $oldpage = get_user_preferences('margic_activepage_'.$id);

            if ($page != $oldpage) {
                set_user_preference('margic_activepage_'.$id, $page);
            }

            $this->page = $page;
        }

        // Handling groups.
        $currentgroups = groups_get_activity_group($this->cm, true);    // Get a list of the currently allowed groups for this course.

        if ($currentgroups) {
            $allowedusers = get_users_by_capability($this->context, 'mod/margic:addentries', '', $sort = 'lastname ASC, firstname ASC', '', '', $currentgroups);
        } else {
            $allowedusers = true;
        }

        if ($this->mode == 'allentries') {

            if ($userid && $userid != 0) {
                $this->entries = $DB->get_records('margic_entries', array('margic' => $this->instance->id, 'userid' => $userid), $sortoptions);
            } else {
                $this->entries = $DB->get_records('margic_entries', array('margic' => $this->instance->id), $sortoptions);
            }

        } else if ($this->mode == 'ownentries') {
            $this->entries = $DB->get_records('margic_entries', array('margic' => $this->instance->id, 'userid' => $USER->id), $sortoptions);
        }

        $gradingstr = get_string('needsgrading', 'margic');
        $regradingstr = get_string('needsregrading', 'margic');

        $viewinguserid = $USER->id;

        foreach ($this->entries as $i => $entry) {
            $this->entries[$i]->user = $DB->get_record('user', array('id' => $entry->userid));

            if (!$currentgroups || ($allowedusers && in_array($this->entries[$i]->user, $allowedusers))) {
                $this->entries[$i]->stats = entrystats::get_entry_stats($entry->text, $entry->timecreated);

                if (!empty($entry->timecreated) && !empty($entry->timemodified) && empty($entry->timemarked)) {
                    $this->entries[$i]->needsgrading = $gradingstr;
                } else if (!empty($entry->timemodified) && !empty($entry->timemarked) && $entry->timemodified > $entry->timemarked) {
                    $this->entries[$i]->needsregrading = $regradingstr;
                } else {
                    $this->entries[$i]->needsregrading = false;
                }

                $grades = make_grades_menu($this->instance->scale);

                $this->entries[$i]->gradingform = results::margic_return_comment_and_grade_form_for_entry($this->cm->id, $this->context, $this->course, $this->instance,
                    $entry, $grades, $canmanageentries);

                if ($viewinguserid == $entry->userid) {
                    $this->entries[$i]->entrycanbeedited = true;
                } else {
                    $this->entries[$i]->entrycanbeedited = false;
                }

                $this->entries[$i]->annotations = array_values($DB->get_records('margic_annotations', array('margic' => $this->cm->instance, 'entry' => $entry->id)));

                $annotationtypes = $this->get_all_annotationtypes();
                foreach ($this->entries[$i]->annotations as $key => $annotation) {
                    $this->entries[$i]->annotations[$key]->color = $annotationtypes[$annotation->type]->color;
                }

            } else {
                unset($this->entries[$i]);
            }

        }
    }

    /**
     * Singleton getter for margic instance.
     *
     * @param int $id int the course module id of margic
     * @param int $m int the instance id of the margic
     * @param int $userid int The id of the user for that entries should be shown
     * @param int $action string The sortmode of the entries
     * @param int $pagecount int The pagecount that should be set
     * @param int $page int The current page number
     * @return string action
     */
    public static function get_margic_instance($id, $m = null, $userid, $action, $pagecount, $page) {

        static $inst = null;
        if ($inst === null) {
            $inst = new margic($id, $m, $userid, $action, $pagecount, $page);
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
     * @return array action
     */
    public function get_entries() {
        return array_values($this->entries);
    }

    /**
     * Returns the entries for the margic instance with intact keys.
     *
     * @return array action
     */
    public function get_entries_with_keys() {
        return $this->entries;
    }

    /**
     * Returns all annotations for the margic instance.
     *
     * @return array action
     */
    public function get_annotations() {
        global $DB, $USER;

        return $this->annotations;
    }

    /**
     * Returns all annotationtypes.
     *
     * @return array action
     */
    public function get_all_annotationtypes() {
        return $this->annotationtypes;
    }

    /**
     * Returns annotationtype array for select form.
     *
     * @return array action
     */
    public function get_annotationtypes_for_form() {
        $types = array();
        $strmanager = get_string_manager();
        foreach ($this->annotationtypes as $key => $type) {
            if ($type->defaulttype == 1 && $strmanager->string_exists($type->name, 'mod_margic')) {
                $types[$key] = get_string($type->name, 'mod_margic');
            } else {
                $types[$key] = $type->name;
            }
        }

        return $types;
    }

    /**
     * Returns the entries for the margic instance grouped after pagecount.
     *
     * @return array action
     */
    public function get_entries_grouped_by_pagecount() {
        // Group entries by pagecount.
        if ($this->pagecount != 0) {

            $groupedentries = array_chunk($this->entries, $this->pagecount, true);
            array_unshift($groupedentries, "temp");
            unset($groupedentries[0]);

            if (isset($groupedentries[$this->page])) {
                return array_values($groupedentries[$this->page]);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Returns the pagebar for the margic instance.
     *
     * @return array action
     */
    public function get_pagebar() {
        if ($this->pagecount != 0) {
            $groupedentries = array_chunk($this->entries, $this->pagecount, true);
            array_unshift($groupedentries, "temp");
            unset($groupedentries[0]);

            if (isset($groupedentries[2])) {
                $pagebar = array();
                foreach ($groupedentries as $pagenr => $page) {
                    $obj = new stdClass();
                    if ($pagenr == $this->page) {
                        $obj->nr = $pagenr;
                        $obj->display = '<strong>' . $pagenr . '</strong>';
                    } else {
                        $obj->nr = $pagenr;
                        $obj->display = $pagenr;
                    }

                    array_push($pagebar, $obj);
                }

                return $pagebar;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Returns the pagecount options for the margic instance.
     *
     * @return array action
     */
    public function get_pagecountoptions() {

        $pagecountoptions = array(2, 3, 4, 5, 6, 7, 8, 9, 10,
            15, 20, 30, 40, 50, 100, 200, 300, 400, 500,
            1000);

        foreach ($pagecountoptions as $key => $number) {
            $obj = new stdClass();

            if ($number == $this->pagecount) {
                $obj->option = 'value='.$number.' selected';
                $obj->text = $number;

                $match = true;
            } else {
                $obj->option = 'value='.$number;
                $obj->text = $number;
            }

            $pagecountoptions[$key] = $obj;
        }

        return $pagecountoptions;
    }

    /**
     * Returns the current sort mode for the instance.
     *
     * @return string action
     */
    public function get_sortmode() {
        return $this->sortmode;
    }
}
