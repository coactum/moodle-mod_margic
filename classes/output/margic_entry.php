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
 * Class containing data for a margic entry
 *
 * @package     mod_margic
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_margic\output;

use mod_margic\annotation_form;

use renderable;
use renderer_base;
use templatable;
use stdClass;

/**
 * Class containing data for a margic entry
 *
 * @package     mod_margic
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class margic_entry implements renderable, templatable {

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
    protected $entry;
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
    /** @var int */
    protected $course;
    /** @var int */
    protected $singleuser;
    /** @var bool */
    protected $annotationmode;
    /** @var bool */
    protected $canmakeannotations;
    /** @var object */
    protected $errortypes;
    /** @var bool */
    protected $readonly;
    /** @var object */
    protected $grades;
    /** @var object */
    protected $currentgroups;
    /** @var object */
    protected $allowedusers;
    /** @var object */
    protected $strmanager;
    /** @var string */
    protected $gradingstr;
    /** @var string */
    protected $regradingstr;
    /** @var string */
    protected $sesskey;
    /**
     * Construct this renderable.
     * @param object $margic The margic obj
     * @param object $cm The course module
     * @param object $context The context
     * @param array $moduleinstance The moduleinstance for creating grading form
     * @param object $entry The entry
     * @param int $annotationareawidth Width of the annotation area
     * @param bool $caneditentries If own entries can be edited
     * @param int $edittimestarts Time when entries can be edited
     * @param bool $edittimenotstarted If edit time has not started
     * @param int $edittimeends Time when entries cant be edited anymore
     * @param bool $edittimehasended If edit time has ended
     * @param bool $canmanageentries If entries can be managed
     * @param int $course The course id for getting the user pictures
     * @param int $singleuser If only entries of one user are displayed
     * @param bool $annotationmode If annotation mode is set
     * @param bool $canmakeannotations If user can make annotations
     * @param array $errortypes Array with annotation types for form
     * @param bool $readonly If entry and annotations should only be readable
     * @param object $grades The grades
     * @param object $currentgroups The current groups
     * @param object $allowedusers The allowed users
     * @param object $strmanager The strmanager
     * @param string $gradingstr The gradingstr
     * @param string $regradingstr The regradingstr
     * @param string $sesskey The session key
     */
    public function __construct($margic, $cm, $context, $moduleinstance, $entry, $annotationareawidth,
        $caneditentries, $edittimestarts, $edittimenotstarted, $edittimeends, $edittimehasended, $canmanageentries,
        $course, $singleuser, $annotationmode, $canmakeannotations, $errortypes, $readonly, $grades, $currentgroups, $allowedusers,
        $strmanager, $gradingstr, $regradingstr, $sesskey) {

        $this->margic = $margic;
        $this->cm = $cm;
        $this->cmid = $this->cm->id;
        $this->context = $context;
        $this->moduleinstance = $moduleinstance;
        $this->entry = $entry;
        $this->annotationareawidth = $annotationareawidth;
        $this->entryareawidth = 100 - $annotationareawidth;
        $this->caneditentries = $caneditentries;
        $this->edittimestarts = $edittimestarts;
        $this->edittimenotstarted = $edittimenotstarted;
        $this->edittimeends = $edittimeends;
        $this->edittimehasended = $edittimehasended;
        $this->canmanageentries = $canmanageentries;
        $this->course = $course;
        $this->singleuser = $singleuser;
        $this->annotationmode = $annotationmode;
        $this->canmakeannotations = $canmakeannotations;
        $this->errortypes = $errortypes;
        $this->readonly = $readonly;
        $this->grades = $grades;
        $this->currentgroups = $currentgroups;
        $this->allowedusers = $allowedusers;
        $this->strmanager = $strmanager;
        $this->gradingstr = $gradingstr;
        $this->regradingstr = $regradingstr;
        $this->sesskey = $sesskey;
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

        $data->entry = $this->margic->prepare_entry($this->entry, $this->strmanager, $this->currentgroups, $this->allowedusers,
            $this->gradingstr, $this->regradingstr, $this->readonly, $this->grades, $this->canmanageentries, $this->annotationmode);

        $data->entryareawidth = $this->entryareawidth;
        $data->annotationareawidth = $this->annotationareawidth;
        $data->caneditentries = $this->caneditentries;
        $data->edittimestarts = $this->edittimestarts;
        $data->edittimenotstarted = $this->edittimenotstarted;
        $data->edittimeends = $this->edittimeends;
        $data->edittimehasended = $this->edittimehasended;
        $data->canmanageentries = $this->canmanageentries;
        $data->singleuser = $this->singleuser;
        $data->annotationmode = $this->annotationmode;
        $data->canmakeannotations = $this->canmakeannotations;
        $data->entrybgc = get_config('margic', 'entrybgc');
        $data->textbgc = get_config('margic', 'textbgc');
        $data->errortypes = $this->errortypes;
        $data->readonly = $this->readonly;
        $data->sesskey = $this->sesskey;
        return $data;
    }
}
