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
 * Class containing data for margic main page
 *
 * @package     mod_margic
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_margic\output;

use mod_margic\mod_margic_annotation_form;

use renderable;
use renderer_base;
use templatable;
use stdClass;

/**
 * Class containing data for margic_view
 *
 * @package     mod_margic
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class margic_view implements renderable, templatable {

    /** @var object */
    protected $margic;
    /** @var object */
    protected $cm;
    /** @var int */
    protected $cmid;
    /** @var object */
    protected $context;
    /** @var object */
    protected $moduleinstance;
    /** @var object */
    protected $entries;
    /** @var string */
    protected $sortmode;
    /** @var string */
    protected $entrybgc;
    /** @var string */
    protected $textbgc;
    /** @var int */
    protected $entryareawidth;
    /** @var int */
    protected $annotationareawidth;
    /** @var bool */
    protected $caneditentries;
    /** @var int */
    protected $edittimestarts;
    /** @var bool */
    protected $edittimenotstarted;
    /** @var int */
    protected $edittimeends;
    /** @var bool */
    protected $edittimehasended;
    /** @var bool */
    protected $canmanageentries;
    /** @var string */
    protected $sesskey;
    /** @var string */
    protected $currentuserrating;
    /** @var int */
    protected $ratingaggregationmode;
    /** @var int */
    protected $course;
    /** @var int */
    protected $singleuser;
    /** @var int */
    protected $pagecountoptions;
    /** @var array */
    protected $pagebar;
    /** @var int */
    protected $entriescount;
    /** @var bool */
    protected $annotationmode;
    /** @var bool */
    protected $canmakeannotations;
    /** @var object */
    protected $errortypes;
    /**
     * Construct this renderable.
     * @param object $margic The margic obj
     * @param object $cm The course module
     * @param object $context The context
     * @param array $moduleinstance The moduleinstance for creating grading form
     * @param array $entries The accessible entries for the margic instance
     * @param string $sortmode Sort mode for the margic instance
     * @param string $entrybgc Background color of the entries
     * @param string $textbgc Background color of the texts in the entries
     * @param int $annotationareawidth Width of the annotation area
     * @param bool $caneditentries If own entries can be edited
     * @param int $edittimestarts Time when entries can be edited
     * @param bool $edittimenotstarted If edit time has not started
     * @param int $edittimeends Time when entries cant be edited anymore
     * @param bool $edittimehasended If edit time has ended
     * @param bool $canmanageentries If entries can be managed
     * @param string $sesskey The session key
     * @param string $currentuserrating The rating of the current user viewing the page
     * @param string $ratingaggregationmode The mode of the aggregated grades
     * @param int $course The course id for getting the user pictures
     * @param int $singleuser If only entries of one user are displayed
     * @param array $pagecountoptions Options for the pagecount select
     * @param array $pagebar Array with the bpages for the pagebar
     * @param int $entriescount The amount of all entries
     * @param bool $annotationmode If annotation mode is set
     * @param bool $canmakeannotations If user can make annotations
     * @param array $errortypes Array with annotation types for form
     */
    public function __construct($margic, $cm, $context, $moduleinstance, $entries, $sortmode, $entrybgc, $textbgc,
        $annotationareawidth, $caneditentries, $edittimestarts, $edittimenotstarted, $edittimeends, $edittimehasended,
        $canmanageentries, $sesskey, $currentuserrating, $ratingaggregationmode, $course, $singleuser, $pagecountoptions,
        $pagebar, $entriescount, $annotationmode, $canmakeannotations, $errortypes) {

        $this->margic = $margic;
        $this->cm = $cm;
        $this->cmid = $this->cm->id;
        $this->context = $context;
        $this->moduleinstance = $moduleinstance;
        $this->entries = $entries;
        $this->sortmode = $sortmode;
        $this->entrybgc = $entrybgc;
        $this->textbgc = $textbgc;
        $this->annotationareawidth = $annotationareawidth;
        $this->entryareawidth = 100 - $annotationareawidth;
        $this->caneditentries = $caneditentries;
        $this->edittimestarts = $edittimestarts;
        $this->edittimenotstarted = $edittimenotstarted;
        $this->edittimeends = $edittimeends;
        $this->edittimehasended = $edittimehasended;
        $this->canmanageentries = $canmanageentries;
        $this->sesskey = $sesskey;
        $this->currentuserrating = $currentuserrating;
        $this->ratingaggregationmode = $ratingaggregationmode;
        $this->course = $course;
        $this->singleuser = $singleuser;
        $this->pagecountoptions = $pagecountoptions;
        $this->pagebar = $pagebar;
        $this->entriescount = $entriescount;
        $this->annotationmode = $annotationmode;
        $this->canmakeannotations = $canmakeannotations;
        $this->errortypes = $errortypes;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Renderer base.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->cmid = $this->cmid;

        global $OUTPUT, $DB, $USER, $CFG;

        if ($this->entries) {

            require_once($CFG->dirroot . '/mod/margic/annotation_form.php');

            $grades = make_grades_menu($this->moduleinstance->scale); // For select in grading_form.
            $currentgroups = groups_get_activity_group($this->cm, true);    // Get a list of the currently allowed course groups.
            if ($currentgroups) {
                $allowedusers = get_users_by_capability($this->context, 'mod/margic:addentries', '',
                    $sort = 'lastname ASC, firstname ASC', '', '', $currentgroups);
            } else {
                $allowedusers = true;
            }

            $strmanager = get_string_manager();

            $gradingstr = get_string('needsgrading', 'margic');
            $regradingstr = get_string('needsregrading', 'margic');

            $readonly = false;

            foreach ($this->entries as $key => $entry) {
                if ($entry) { // Set user picture for teachers.
                    $this->entries[$key]->entry = $OUTPUT->render(new margic_entry($this->margic, $this->cm, $this->context,
                        $this->moduleinstance, $entry, $this->annotationareawidth, $this->moduleinstance->editentries,
                        $this->edittimestarts, $this->edittimenotstarted, $this->edittimeends, $this->edittimehasended,
                        $this->canmanageentries, $this->course, $this->singleuser, $this->annotationmode, $this->canmakeannotations,
                        $this->errortypes, $readonly, $grades, $currentgroups, $allowedusers, $strmanager, $gradingstr,
                        $regradingstr, $this->sesskey, false));
                }
            }
        }

        $data->entries = $this->entries;
        $data->sortmode = $this->sortmode;
        $data->entrybgc = $this->entrybgc;
        $data->textbgc = $this->textbgc;
        $data->entryareawidth = $this->entryareawidth;
        $data->annotationareawidth = $this->annotationareawidth;
        $data->caneditentries = $this->caneditentries;
        $data->edittimestarts = $this->edittimestarts;
        $data->edittimenotstarted = $this->edittimenotstarted;
        $data->edittimeends = $this->edittimeends;
        $data->edittimehasended = $this->edittimehasended;
        $data->canmanageentries = $this->canmanageentries;
        $data->sesskey = $this->sesskey;
        $data->currentuserrating = $this->currentuserrating;
        $data->ratingaggregationmode = $this->ratingaggregationmode;
        $data->singleuser = $this->singleuser;
        $data->pagecountoptions = $this->pagecountoptions;
        $data->pagebar = $this->pagebar;
        $data->entriescount = $this->entriescount;
        $data->annotationmode = $this->annotationmode;
        $data->canmakeannotations = $this->canmakeannotations;

        return $data;
    }
}
