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
 * This file contains the forms to create and edit an instance of the margic module.
 *
 * @package   mod_margic
 * @copyright 2022 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * margic settings form.
 *
 * @package   mod_margic
 * @copyright 2022 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_margic_mod_form extends moodleform_mod {

    /**
     * Define the margic activity settings form.
     *
     * @return void
     */
    public function definition() {
        global $DB, $USER;

        $mform = &$this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('margicname', 'margic'), array(
            'size' => '64'
        ));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $this->standard_intro_elements(get_string('margicdescription', 'margic'));

        $id = optional_param('update', null, PARAM_INT);

        if (!isset($id) || $id == 0) {
            // Add the header for the error types.
            $mform->addElement('header', 'errortypeshdr', get_string('errortypes', 'margic'));
            $mform->setExpanded('errortypeshdr');

            $select = "defaulttype = 1";
            $select .= " OR userid = " . $USER->id;
            $errortypetemplates = (array) $DB->get_records_select('margic_errortype_templates', $select);

            $strmanager = get_string_manager();

            $this->add_checkbox_controller(1);

            foreach ($errortypetemplates as $id => $type) {
                if ($type->defaulttype == 1) {
                    $name = '<span style="margin-right: 10px; background-color: #' . $type->color . '" title="' . get_string('standardtype', 'mod_margic') .'">(S)</span>';
                } else {
                    $name = '<span style="margin-right: 10px; background-color: #' . $type->color . '" title="' . get_string('manualtype', 'mod_margic') .'">(M)</span>';
                }

                if ($type->defaulttype == 1 && $strmanager->string_exists($type->name, 'mod_margic')) {
                    $name .= '<span>' . get_string($type->name, 'mod_margic') . '</span>';
                } else {
                    $name .= '<span>' . $type->name . '</span>';
                }

                $mform->addElement('advcheckbox', 'errortypes[' . $id . ']', $name, ' ', array('group' => 1), array(0, 1));
            }

        }

        // Add the header for availability.
        $mform->addElement('header', 'availibilityhdr', get_string('availability'));

        $mform->addElement('date_time_selector', 'timeopen', get_string('margicopentime', 'margic'), array(
            'optional' => true,
            'step' => 1
        ));
        $mform->addHelpButton('timeopen', 'margicopentime', 'margic');

        $mform->addElement('date_time_selector', 'timeclose', get_string('margicclosetime', 'margic'), array(
            'optional' => true,
            'step' => 1
        ));
        $mform->addHelpButton('timeclose', 'margicclosetime', 'margic');

        // Edit all setting if user can edit its own entries.
        $mform->addElement('selectyesno', 'editall', get_string('editall', 'margic'));
        $mform->addHelpButton('editall', 'editall', 'margic');

        // Edit dates setting if user can modify entry date.
        $mform->addElement('selectyesno', 'editdates', get_string('editdates', 'margic'));
        $mform->addHelpButton('editdates', 'editdates', 'margic');

        // Add the header for appearance.
        $mform->addElement('header', 'appearancehdr', get_string('appearance'));

        // Width of the annotation area.
        $mform->addElement('text', 'annotationareawidth', get_string('annotationareawidth', 'margic'));
        $mform->setType('annotationareawidth', PARAM_INT);
        $mform->addHelpButton('annotationareawidth', 'annotationareawidth', 'margic');
        $mform->setDefault('annotationareawidth', get_config('mod_margic', 'annotationareawidth'));

        // Add the rest of the common settings.
        $this->standard_grading_coursemodule_elements();
        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $minwidth = 20;
        $maxwidth = 80;

        if (!$data['annotationareawidth'] || $data['annotationareawidth'] < $minwidth || $data['annotationareawidth'] > $maxwidth) {
            $errors['annotationareawidth'] = get_string('errannotationareawidthinvalid', 'margic', array('minwidth' => $minwidth, 'maxwidth' => $maxwidth));
        }

        return $errors;
    }
}
