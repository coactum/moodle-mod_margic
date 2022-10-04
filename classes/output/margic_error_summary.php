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
 * Class containing data for margic error summary
 *
 * @package     mod_margic
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_margic\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;

/**
 * Class containing data for margic error summary
 *
 * @package     mod_margic
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class margic_error_summary implements renderable, templatable {

    /** @var int */
    protected $cmid;
    /** @var object */
    protected $participants;
    /** @var object */
    protected $margicerrortypes;
    /** @var object */
    protected $errortypetemplates;
    /** @var string */
    protected $sesskey;
    /** @var bool */
    protected $canmanageerrortypes;
    /** @var bool */
    protected $defaulterrortypetemplateseditable;
    /**
     * Construct this renderable.
     * @param int $cmid The course module id
     * @param array $participants The participants of the margic instance
     * @param array $margicerrortypes The errortypes used in the margic instance
     * @param array $errortypetemplates The errortype templates available for the current user
     * @param string $sesskey The session key
     * @param bool $canmanageerrortypes If user can manage errortypes
     * @param bool $defaulterrortypetemplateseditable If eligible users can edit default error type templates
     */
    public function __construct($cmid, $participants, $margicerrortypes, $errortypetemplates, $sesskey, $canmanageerrortypes, $defaulterrortypetemplateseditable) {

        $this->cmid = $cmid;
        $this->participants = $participants;
        $this->margicerrortypes = $margicerrortypes;
        $this->errortypetemplates = $errortypetemplates;
        $this->sesskey = $sesskey;
        $this->canmanageerrortypes = $canmanageerrortypes;
        $this->defaulterrortypetemplateseditable = $defaulterrortypetemplateseditable;
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
        $data->participants = $this->participants;
        $data->margicerrortypes = $this->margicerrortypes;
        $data->errortypetemplates = $this->errortypetemplates;
        $data->sesskey = $this->sesskey;
        $data->canmanageerrortypes = $this->canmanageerrortypes;
        $data->defaulterrortypetemplateseditable = $this->defaulterrortypetemplateseditable;

        return $data;
    }
}
