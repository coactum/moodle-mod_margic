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
 * Class containing data for margic annotations summary
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
 * Class containing data for margic annotations summary
 *
 * @package     mod_margic
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class margic_annotations_summary implements renderable, templatable {

    /** @var int */
    protected $cmid;
    /** @var object */
    protected $participants;
    /** @var object */
    protected $errortypes;

    /**
     * Construct this renderable.
     * @param int $cmid The course module id
     * @param array $participants The participants of the margic instance
     * @param array $errortypes The annotation types of the margic instance
     */
    public function __construct($cmid, $participants, $errortypes) {

        $this->cmid = $cmid;
        $this->participants = $participants;
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
        $data->participants = $this->participants;
        $data->errortypes = $this->errortypes;

        return $data;
    }
}
