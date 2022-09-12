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
 * This file contains a renderer for various parts of the margic module.
 *
 * @package   mod_margic
 * @copyright 2022 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * A custom renderer class that extends the plugin_renderer_base and is used by the margic module.
 *
 * @package mod_margic
 * @copyright 2022 coactum GmbH
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_margic_renderer extends plugin_renderer_base {

    /**
     * Defer to template.
     *
     * @param margic_view $page
     *
     * @return string html for the page
     */
    public function render_margic_view(margic_view $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('mod_margic/margic_view', $data);
    }

    /**
     * Rendering margic files.
     *
     * @var int $margic
     */
    private $margic;

    /**
     * Initialize internal objects.
     *
     * @param int $cm
     */
    public function init($cm) {
        $this->cm = $cm;
    }
}
