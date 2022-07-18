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
        global $COURSE;

        $mform = &$this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('margicname', 'margic'), array(
            'size' => '64'
        ));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $this->standard_intro_elements(get_string('margicdescription', 'margic'));

        // Add the header for availability.
        $mform->addElement('header', 'availibilityhdr', get_string('availability'));

        // 20200915 Moved check so daysavailable is hidden unless using weekly format.
        if ($COURSE->format == 'weeks') {
            $options = array();
            $options[0] = get_string('alwaysopen', 'margic');
            for ($i = 1; $i <= 13; $i ++) {
                $options[$i] = get_string('numdays', '', $i);
            }
            for ($i = 2; $i <= 16; $i ++) {
                $days = $i * 7;
                $options[$days] = get_string('numweeks', '', $i);
            }
            $options[365] = get_string('numweeks', '', 52);
            $mform->addElement('select', 'days', get_string('daysavailable', 'margic'), $options);
            $mform->addHelpButton('days', 'daysavailable', 'margic');

            $mform->setDefault('days', '7');
        } else {
            $mform->setDefault('days', '0');
        }

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
