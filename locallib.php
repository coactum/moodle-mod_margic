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
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_margic\local\entrystats;
use mod_margic\local\helper;

/**
 * Base class for mod_margic.
 *
 * @package   mod_margic
 * @copyright 2022 coactum GmbH
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
    private $errortypes = array();

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

        /**
         * Custom sort function for sorting annotations by absolute start position.
         *
         * @param int $a First annotation
         * @param int $b Second annotation
         * @return bool Sort result
         */
        function sortannotation($a, $b) {
            if ($a->annotationstart === $b->annotationstart) {
                return $a->annotationend > $b->annotationend;
            }

            return $a->annotationstart > $b->annotationstart;
        }

        global $DB, $USER;

        if (isset($id) && $id != 0) {
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

        $this->annotations = $DB->get_records('margic_annotations', array('margic' => $this->get_course_module()->instance));

        $select = "margic = " . $this->instance->id;
        $this->errortypes = (array) $DB->get_records_select('margic_errortypes', $select, null, 'priority ASC');

        foreach ($this->annotations as $key => $annotation) {

            if (!array_key_exists($annotation->type, $this->errortypes) &&
                $DB->record_exists('margic_errortypes', array('id' => $annotation->type))) {

                $this->errortypes[$annotation->type] = $DB->get_record('margic_errortypes', array('id' => $annotation->type));
            }

            if (isset($this->errortypes[$annotation->type])) {
                $this->annotations[$key]->color = $this->errortypes[$annotation->type]->color;
            }

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
                    require_sesskey();
                    set_user_preference('margic_sortoption', 1);
                    break;
                case 'oldesttocurrent':
                    require_sesskey();
                    set_user_preference('margic_sortoption', 2);
                    break;
                case 'lowestgradetohighest':
                    require_sesskey();
                    set_user_preference('margic_sortoption', 3);
                    break;
                case 'highestgradetolowest':
                    require_sesskey();
                    set_user_preference('margic_sortoption', 4);
                    break;
                default:
                    if (!get_user_preferences('margic_sortoption')) {
                        set_user_preference('margic_sortoption', 1);
                    }
            }

            switch (get_user_preferences('margic_sortoption')) {
                case 1:
                    $this->sortmode = get_string('currententry', 'mod_margic');
                    $sortoptions = 'timemodified DESC';
                    break;
                case 2:
                    $this->sortmode = get_string('oldestentry', 'mod_margic');
                    $sortoptions = 'timemodified ASC';
                    break;
                case 3:
                    $this->sortmode = get_string('lowestgradeentry', 'mod_margic');
                    $sortoptions = 'rating ASC, timemodified DESC';
                    break;
                case 4:
                    $this->sortmode = get_string('highestgradeentry', 'mod_margic');
                    $sortoptions = 'rating DESC, timemodified DESC';
                    break;
                default:
                    $this->sortmode = get_string('currententry', 'mod_margic');
                    $sortoptions = 'timemodified DESC';
            }

        }

        // Page selector.
        if ($pagecount !== 0) {

            require_sesskey();

            if ($pagecount < 2) {
                $pagecount = 2;
            }

            $oldpagecount = get_user_preferences('margic_pagecount');

            if ($pagecount != $oldpagecount) {
                set_user_preference('margic_pagecount', $pagecount);
            }

            $this->pagecount = $pagecount;
        } else if ($oldpagecount = get_user_preferences('margic_pagecount')) {
            $this->pagecount = $oldpagecount;
        } else {
            $this->pagecount = 5;
        }

        // Active page.
        if ($page) {

            if ($page < 1) {
                $page = 1;
            }

            $oldpage = get_user_preferences('margic_activepage');

            if ($page != $oldpage) {
                set_user_preference('margic_activepage', $page);
            }

            $this->page = $page;
        } else if ($oldpage = get_user_preferences('margic_activepage')) {
            $this->page = $oldpage;
        } else {
            $this->page = 1;

        }

        // Get entries.
        if ($this->mode == 'allentries') {

            if ($userid && $userid != 0) {
                $this->entries = $DB->get_records('margic_entries', array('margic' => $this->instance->id, 'userid' => $userid,
                    'baseentry' => null), $sortoptions);
            } else {
                $this->entries = $DB->get_records('margic_entries', array('margic' => $this->instance->id,
                    'baseentry' => null), $sortoptions);
            }

        } else if ($this->mode == 'ownentries') {
            $this->entries = $DB->get_records('margic_entries', array('margic' => $this->instance->id, 'userid' => $USER->id,
                'baseentry' => null), $sortoptions);
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
     * Returns all annotations for the margic instance.
     *
     * @return array action
     */
    public function get_annotations() {
        return $this->annotations;
    }

    /**
     * Returns the width of the annotation area.
     *
     * @return int annotationareawidth
     */
    public function get_annotationarea_width() {
        if (isset($this->instance->annotationareawidth)) {
            $annotationareawidth = $this->instance->annotationareawidth;
        } else {
            $annotationareawidth = get_config('margic', 'annotationareawidth');
        }

        return $annotationareawidth;
    }

    /**
     * Returns all errortypes.
     *
     * @return array action
     */
    public function get_margic_errortypes() {
        return $this->errortypes;
    }

    /**
     * Returns errortype array for select form.
     *
     * @return array action
     */
    public function get_errortypes_for_form() {
        $types = array();
        $strmanager = get_string_manager();
        foreach ($this->errortypes as $key => $type) {
            if ($strmanager->string_exists($type->name, 'mod_margic')) {
                $types[$key] = get_string($type->name, 'mod_margic');
            } else {
                $types[$key] = $type->name;
            }
        }

        return $types;
    }

    /**
     * Returns all errortype templates.
     *
     * @return array action
     */
    public function get_all_errortype_templates() {
        global $USER, $DB;

        $select = "defaulttype = 1";
        $select .= " OR userid = " . $USER->id;

        $errortypetemplates = (array) $DB->get_records_select('margic_errortype_templates', $select);

        return $errortypetemplates;
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
            } else if (isset($groupedentries[1])) { // In case the active page stored is not existent in this margic.
                set_user_preference('margic_activepage', 1);
                return array_values($groupedentries[1]);
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

    /**
     * Prepare the entry for the template.
     *
     * @param object $entry The entry to be processed.
     * @param object $strmanager The moodle strmanager object needed to check error types.
     * @param object $currentgroups The currentgroups for checking access.
     * @param object $allowedusers The allowedusers for checking access.
     * @param object $gradingstr The gradingstr for the template.
     * @param object $regradingstr The regradingstr for the template.
     * @param object $readonly If entry is in readonly mode.
     * @param object $grades The grades for the gradingform.
     * @param object $canmanageentries If user can manage entries.
     * @param object $annotationmode If annotationmode is activated.
     * @return object The entry or false if user is not allowed to see entry.
     */
    public function prepare_entry($entry, $strmanager, $currentgroups, $allowedusers, $gradingstr, $regradingstr, $readonly,
        $grades, $canmanageentries, $annotationmode) {

        global $DB, $USER, $CFG, $OUTPUT;

        $entry->user = $DB->get_record('user', array('id' => $entry->userid));

        if (!$currentgroups || ($allowedusers && in_array($entry->user, $allowedusers))) {
            // Get child entries for entry.
            $entry->childentries = $DB->get_records('margic_entries',
                array('margic' => $this->instance->id, 'baseentry' => $entry->id), 'timecreated DESC');

            $revisionnr = count($entry->childentries);
            foreach ($entry->childentries as $ci => $childentry) {
                $entry->childentries[$ci] = $this->prepare_entry_annotations($childentry, $strmanager, $annotationmode, $readonly);
                $entry->childentries[$ci]->stats = entrystats::get_entry_stats($childentry->text, $childentry->timecreated);
                $entry->childentries[$ci]->revision = $revisionnr;

                if ($ci == array_key_first($entry->childentries)) {
                    $entry->childentries[$ci]->newestentry = true;
                    if ($USER->id == $childentry->userid && !$readonly) {
                        $entry->childentries[$ci]->entrycanbeedited = true;
                    } else {
                        $entry->childentries[$ci]->entrycanbeedited = false;
                    }
                } else {
                    $entry->childentries[$ci]->entrycanbeedited = false;
                    $entry->childentries[$ci]->newestentry = false;
                }

                if ($USER->id == $entry->userid && empty($entry->childentries) && !$readonly) {
                    $entry->entrycanbeedited = true;
                } else {
                    $entry->entrycanbeedited = false;
                }

                $revisionnr -= 1;
            }

            $entry->childentries = array_values($entry->childentries);

            if (empty($entry->childentries)) {
                $entry->haschildren = false;
            } else {
                $entry->haschildren = true;
            }

            // Get entry stats.
            $entry->stats = entrystats::get_entry_stats($entry->text, $entry->timecreated);

            // Check entry grading.
            if (!empty($entry->timecreated) && empty($entry->timemarked)) {
                $entry->needsgrading = $gradingstr;
            } else if (!empty($entry->timemodified) && !empty($entry->timemarked) && $entry->timemodified > $entry->timemarked) {
                $entry->needsregrading = $regradingstr;
            } else {
                $entry->needsregrading = false;
            }

            // Check if entry can be edited.
            if ($USER->id == $entry->userid && empty($entry->childentries)) {
                $entry->entrycanbeedited = true;
            } else {
                $entry->entrycanbeedited = false;
            }

            require_once($CFG->dirroot . '/mod/margic/annotation_form.php');
            require_once($CFG->dirroot . '/mod/margic/classes/local/helper.php');

            $entry->user->userpicture = $OUTPUT->user_picture($entry->user,
            array('courseid' => $this->course->id, 'link' => true, 'includefullname' => true, 'size' => 25));

            // Add feedback area to entry.
            $entry->gradingform = helper::margic_return_feedback_area_for_entry($this->cm->id, $this->context,
            $this->course, $this->instance, $entry, $grades, $canmanageentries);

            $entry = $this->prepare_entry_annotations($entry, $strmanager, $annotationmode, $readonly);

            return $entry;
        } else {
            return false;
        }
    }

    /**
     * Prepare the annotations for the entry.
     *
     * @param object $entry The entry to be processed.
     * @param object $strmanager The moodle strmanager object needed to check error types in the annotation form.
     * @param object $annotationmode If annotationmode is activated.
     * @param object $readonly If entry is in readonly mode.
     * @return object The entry with its annotations.
     */
    private function prepare_entry_annotations($entry, $strmanager, $annotationmode = false, $readonly = false) {
        global $DB, $USER, $CFG, $OUTPUT;

        // Get annotations for entry.
        $entry->annotations = array_values($DB->get_records('margic_annotations',
            array('margic' => $this->cm->instance, 'entry' => $entry->id)));

        foreach ($entry->annotations as $key => $annotation) {

            if (!$DB->record_exists('margic_errortypes', array('id' => $annotation->type))) { // If annotation type does not exist.
                $entry->annotations[$key]->color = 'FFFF00';
                $entry->annotations[$key]->type = get_string('deletederrortype', 'mod_margic');
            } else {
                $entry->annotations[$key]->color = $this->errortypes[$annotation->type]->color;

                if ($strmanager->string_exists($this->errortypes[$annotation->type]->name, 'mod_margic')) {
                    $entry->annotations[$key]->type = get_string($this->errortypes[$annotation->type]->name, 'mod_margic');
                } else {
                    $entry->annotations[$key]->type = $this->errortypes[$annotation->type]->name;
                }
            }

            if (has_capability('mod/margic:makeannotations', $this->context) && $annotation->userid == $USER->id) {
                $entry->annotations[$key]->canbeedited = true;
            } else {
                $entry->annotations[$key]->canbeedited = false;
            }

            if ($annotationmode) {
                // Add annotater images to annotations.
                $annotater = $DB->get_record('user', array('id' => $annotation->userid));
                $annotaterimage = $OUTPUT->user_picture($annotater,
                    array('courseid' => $this->course->id, 'link' => true, 'includefullname' => true, 'size' => 20));
                $entry->annotations[$key]->userpicturestr = $annotaterimage;

            } else {
                $entry->annotationform = false;
            }
        }

        // Sort annotations and find its position.
        usort($entry->annotations, "sortannotation");
        $pos = 1;
        foreach ($entry->annotations as $key => $annotation) {
            $entry->annotations[$key]->position = $pos;
            $pos += 1;
        }

        if ($annotationmode) {
            // Add annotation form.
            if (!$readonly) {
                require_once($CFG->dirroot . '/mod/margic/annotation_form.php');
                $mform = new mod_margic_annotation_form(new moodle_url('/mod/margic/annotations.php', array('id' => $this->cm->id)),
                    array('types' => $this->get_errortypes_for_form()));
                // Set default data.
                $mform->set_data(array('id' => $this->cm->id, 'entry' => $entry->id));

                $entry->annotationform = $mform->render();
            }
        }

        return $entry;
    }
}
