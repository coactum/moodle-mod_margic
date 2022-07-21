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
 * File containing the class definition for the errortypes form for the margic.
 *
 * @package     mod_margic
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");

/**
 * Form for annotation types.
 *
 * @package   mod_margic
 * @copyright 2022 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL Juv3 or later
 */
class errortypes_form extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {

        global $OUTPUT;

        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'typeid', null);
        $mform->setType('typeid', PARAM_INT);

        $mform->addElement('text', 'typename', get_string('nameoferrortype', 'mod_margic'));
        $mform->setType('typename', PARAM_TEXT);
        $mform->addRule('typename', null, 'required', null, 'client');

        if ($this->_customdata['editdefaulttype']) {
            $mform->addHelpButton('typename', 'explanationtypename', 'mod_margic');
        }

        $mform->addElement('text', 'color', get_string('annotationcolor', 'mod_margic'));
        $mform->setType('color', PARAM_ALPHANUM);
        $mform->addRule('color', null, 'required', null, 'client');
        $mform->addHelpButton('color', 'explanationhexcolor', 'mod_margic');

        if ($this->_customdata['editdefaulttype']) {
            $mform->addElement('advcheckbox', 'defaulttype', get_string('defaulttype', 'mod_margic'), get_string('explanationdefaulttype', 'mod_margic'));
        } else {
            $mform->addElement('hidden', 'defaulttype', 0);
        }

        $mform->setType('defaulttype', PARAM_INT);

        $this->add_action_buttons();
    }

    /**
     * Custom validation should be added here
     * @param array $data Array with all the form data
     * @param array $files Array with files submitted with form
     * @return array Array with errors
     */
    public function validation($data, $files) {
        $errors = array();

        if (strlen($data['color']) !== 6 || preg_match("/[^a-fA-F0-9]/", $data['color'])) {
            $errors['color'] = get_string('errnohexcolor', 'mod_margic');
        }

        return $errors;
    }
}
