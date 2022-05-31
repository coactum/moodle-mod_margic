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
 * The mod_margic invalid entry attempt event.
 *
 * @package   mod_margic
 * @copyright 2022 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_margic\event;

/**
 * The mod_margic invalid entry created class.
 *
 * @package   mod_margic
 * @since     Moodle 3.1
 * @copyright 2022 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class invalid_entry_attempt extends \core\event\base {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'margic';
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventinvalidentryattempt', 'mod_margic');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' has attempted to make an invalid entry for the margic activity with " .
            "the course module id '$this->contextinstanceid'.";
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/margic/edit.php', array(
            'id' => $this->contextinstanceid
        ));
    }

    /**
     * replace add_to_log() statement.
     *
     * @return array of parameters to be passed to legacy add_to_log() function.
     */
    protected function get_legacy_logdata() {
        $url = new \moodle_url('edit.php', array(
            'id' => $this->contextinstanceid
        ));
        return array(
            $this->courseid,
            'margic',
            'add entry',
            $url->out(),
            $this->objectid,
            $this->contextinstanceid
        );
    }
}
