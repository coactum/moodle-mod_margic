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
 * The form for grading and rating entries in mod_margic.
 *
 * @package     mod_margic
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use mod_margic\local\helper;

global $CFG;

require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot . '/mod/margic/classes/local/helper.php');
require_once(__DIR__ .'/../../lib/gradelib.php');

/**
 * The form for grading and rating entries in mod_margic.
 *
 * @package     mod_margic
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_margic_grading_form extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {

        global $DB;

        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'entry');
        $mform->setType('entry', PARAM_INT);

        $feedbacktext = $this->_customdata['entry']->feedback;

        $user = $DB->get_record('user', array('id' => $this->_customdata['entry']->userid));
        $userfullname = fullname($user);

        $feedbackdisabled = false;
        $attr = array();

        if ($this->_customdata['margic']->assessed != 0) { // Append grading area only when grading is not disabled.

            $gradinginfo = grade_get_grades($this->_customdata['courseid'], 'mod', 'margic', $this->_customdata['margic']->id, $this->_customdata['entry']->userid);

            $userfinalgrade = $gradinginfo->items[0]->grades[$this->_customdata['entry']->userid];
            $currentuserrating = $userfinalgrade->str_long_grade;

            // If margic already graded.
            if (!empty($gradinginfo->items[0]->grades[$this->_customdata['entry']->userid]->str_long_grade)) {
                if ($gradingdisabled = $gradinginfo->items[0]->grades[$this->_customdata['entry']->userid]->locked
                    || $gradinginfo->items[0]->grades[$this->_customdata['entry']->userid]->overridden) { // If the grade was modified from the gradebook disable editing.

                    global $CFG;

                    $feedbackdisabled = true;

                    $gradebooklinkrating = '<a href="' . $CFG->wwwroot . '/grade/report/grader/index.php?id='
                        . $this->_customdata['courseid'] . '">' . $gradinginfo->items[0]->grades[$this->_customdata['entry']->userid]->str_long_grade . '</a>';

                    $gradebooklinkfeedback = '<a href="' . $CFG->wwwroot . '/grade/report/grader/index.php?id='
                        . $this->_customdata['courseid'] . '">' . $gradinginfo->items[0]->grades[$this->_customdata['entry']->userid]->str_feedback . '</a>';

                    $attr = array('disabled' => 'disabled');
                }
            }

            $aggregatestr = helper::get_margic_aggregation($this->_customdata['margic']->assessed) . ' ' . get_string('forallentries', 'margic') . ' '. $userfullname;

            $mform->addElement('static', 'currentuserrating', $aggregatestr.': ', $currentuserrating);

            $mform->addElement('html', '<hr>');

            if ($this->_customdata['entry']->timemarked) {
                $mform->addElement('static', 'currentuserrating',
                    get_string('grader', 'mod_margic'), $this->_customdata['teacherimg'] . ' - ' . userdate($this->_customdata['entry']->timemarked));
                $mform->addElement('static', 'savedrating', get_string('savedrating', 'mod_margic'), $this->_customdata['entry']->rating);
            }

            $select = $mform->addElement('select', 'rating_' . $this->_customdata['entry']->id, get_string('newrating', 'margic') . ': ', $this->_customdata['grades'], $attr);
            $mform->setType('rating_' . $this->_customdata['entry']->id, PARAM_INT);
            $mform->setDefault('rating_' . $this->_customdata['entry']->id, 0);

            if ($feedbackdisabled) { // Disable rating and show rating from gradebook if override is set there.
                $mform->addElement('static', 'gradebookrating', get_string('gradeingradebook', 'margic') . ': ', $gradebooklinkrating);
            }
        }

        // Feedback text.
        if ($feedbackdisabled) { // If override is set in the gradebook show feedback from there and dont show editor.
            $mform->addElement('static', 'gradebookfeedback', get_string('feedbackingradebook', 'margic') . ': ', $gradebooklinkfeedback);

        } else {
            $mform->addElement('editor', 'feedback_' . $this->_customdata['entry']->id . '_editor',
                get_string('feedback', 'mod_margic'), null, $this->_customdata['editoroptions']);
            $mform->setType('feedback_' . $this->_customdata['entry']->id . '_editor', PARAM_RAW);

            $mform->addElement('selectyesno', 'sendgradingmessage', get_string('sendgradingmessage', 'margic'));
            $mform->setDefault('sendgradingmessage', 1);

            $this->add_action_buttons();
        }

    }
}

