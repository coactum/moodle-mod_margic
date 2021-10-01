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
 * File containing the class definition for the annotate form for the annotated diary.
 *
 * @package     mod_annotateddiary
 * @copyright   2021 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");

/**
 * Form for submissions.
 *
 * @package   mod_annotateddiary
 * @copyright 2021 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL Juv3 or later
 */
class annotation_form extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {

        global $OUTPUT;

        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'annotationmode', 1);
        $mform->setType('annotationmode', PARAM_INT);

        $mform->addElement('hidden', 'startcontainer[' . $this->_customdata['entry'] . ']', -1);
        $mform->setType('startcontainer[' . $this->_customdata['entry'] . ']', PARAM_RAW);

        $mform->addElement('hidden', 'endcontainer[' . $this->_customdata['entry'] . ']', -1);
        $mform->setType('endcontainer[' . $this->_customdata['entry'] . ']', PARAM_RAW);

        $mform->addElement('hidden', 'startposition[' . $this->_customdata['entry'] . ']', -1);
        $mform->setType('startposition[' . $this->_customdata['entry'] . ']', PARAM_INT);

        $mform->addElement('hidden', 'endposition[' . $this->_customdata['entry'] . ']', -1);
        $mform->setType('endposition[' . $this->_customdata['entry'] . ']', PARAM_INT);

        $mform->addElement('hidden', 'annotationid[' . $this->_customdata['entry'] . ']', null);
        $mform->setType('annotationid[' . $this->_customdata['entry'] . ']', PARAM_INT);

        $mform->addElement('textarea', 'text[' . $this->_customdata['entry'] . ']');
        $mform->setType('text[' . $this->_customdata['entry'] . ']', PARAM_RAW);
        // $mform->addRule('annotation[' . $this->_customdata['entry'] . ']', get_string('errfilloutfield', 'mod_discourse'), 'required', 'client');

        $this->add_action_buttons();

        $mform->disable_form_change_checker();
    }

    /**
     * Custom validation should be added here
     * @param array $data Array with all the form data
     * @param array $files Array with files submitted with form
     * @return array Array with errors
     */
    public function validation($data, $files) {
        return array();
    }
}
