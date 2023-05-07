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
 * The form for editing existing or creating new entries in mod_margic.
 *
 * @package     mod_margic
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");

/**
 * The form for editing existing or creating new entries in mod_margic.
 *
 * @package     mod_margic
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_margic_entry_form extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {

        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'editentrydates');
        $mform->setType('editentrydates', PARAM_INT);
        $mform->setDefault('editentrydates', $this->_customdata['editentrydates']);

        $mform->addElement('hidden', 'entryid');
        $mform->setType('entryid', PARAM_INT);

        $editentrydates = $this->_customdata['editoroptions']['editentrydates'];

        if ($editentrydates) {
            // Add date selector if entry dates can be edited.
            $mform->addElement('date_time_selector', 'timecreated', get_string('margicentrydate', 'margic'));
            $mform->setType('timecreated', PARAM_INT);
            $mform->hideIf('timecreated', 'editentrydates', 'neq', '1');
            $mform->disabledIf('timecreated', 'editentrydates', 'neq', '1');
        } else {
            $mform->addElement('hidden', 'timecreated');
            $mform->setType('timecreated', PARAM_INT);
        }

        $mform->addElement('editor', 'text_editor', get_string('entry', 'mod_margic'), null, $this->_customdata['editoroptions']);
        $mform->setType('text_editor', PARAM_RAW);
        $mform->addRule('text_editor', null, 'required', null, 'client');

        $this->add_action_buttons();
    }
}

