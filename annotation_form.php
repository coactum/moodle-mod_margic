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
 * File containing the class definition for the annotate form for the margic.
 *
 * @package     mod_margic
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");

/**
 * Form for annotations.
 *
 * @package   mod_margic
 * @copyright 2022 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL Juv3 or later
 */
class mod_margic_annotation_form extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {

        global $OUTPUT;

        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'user', null);
        $mform->setType('user', PARAM_INT);

        $mform->addElement('hidden', 'entry', null);
        $mform->setType('entry', PARAM_INT);

        $mform->addElement('hidden', 'annotationmode', 1);
        $mform->setType('annotationmode', PARAM_INT);

        $mform->addElement('hidden', 'startcontainer', -1);
        $mform->setType('startcontainer', PARAM_RAW);

        $mform->addElement('hidden', 'endcontainer', -1);
        $mform->setType('endcontainer', PARAM_RAW);

        $mform->addElement('hidden', 'startoffset', -1);
        $mform->setType('startoffset', PARAM_INT);

        $mform->addElement('hidden', 'endoffset', -1);
        $mform->setType('endoffset', PARAM_INT);

        $mform->addElement('hidden', 'annotationstart', -1);
        $mform->setType('annotationstart', PARAM_INT);

        $mform->addElement('hidden', 'annotationend', -1);
        $mform->setType('annotationend', PARAM_INT);

        $mform->addElement('hidden', 'annotationid', null);
        $mform->setType('annotationid', PARAM_INT);

        $mform->addElement('hidden', 'exact', -1);
        $mform->setType('exact', PARAM_RAW);

        $mform->addElement('hidden', 'prefix', -1);
        $mform->setType('prefix', PARAM_RAW);

        $mform->addElement('hidden', 'suffix', -1);
        $mform->setType('suffix', PARAM_RAW);

        $select = $mform->addElement('select', 'type', '', $this->_customdata['types']);
        $mform->setType('type', PARAM_INT);

        $mform->addElement('textarea', 'text');
        $mform->setType('text', PARAM_TEXT);

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
