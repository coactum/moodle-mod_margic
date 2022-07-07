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

    /**
     * Print the teacher feedback.
     * This renders the teacher feedback for margic_user_complete (not used at the moment?).
     *
     * @param object $course
     * @param object $entry
     * @param object $grades
     */
    public function margic_print_feedback($course, $entry, $grades) {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/lib/gradelib.php');

        if (! $teacher = $DB->get_record('user', array(
            'id' => $entry->teacher
        ))) {
            throw new moodle_exception(get_string('errnograder', 'margic'));
        }

        echo '<table class="feedbackbox">';

        echo '<tr>';
        echo '<td class="left picture">';
        echo $this->output->user_picture($teacher, array(
            'courseid' => $course->id,
            'alttext' => true
        ));
        echo '</td>';
        echo '<td class="entryheader">';
        echo '<span class="author">' . fullname($teacher) . '</span>';
        echo '&nbsp;&nbsp;<span class="time">' . userdate($entry->timemarked) . '</span>';
        echo '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td class="left side">&nbsp;</td>';
        echo '<td class="entrycontent">';

        echo '<div class="grade">';

        // Gradebook preference.
        $gradinginfo = grade_get_grades($course->id, 'mod', 'margic', $entry->margic, array(
            $entry->userid
        ));

        // 20210609 Added branch check for string compatibility.
        if (! empty($grades)) {
            if ($CFG->branch > 310) {
                echo get_string('gradenoun') . ': ';
            } else {
                echo get_string('grade') . ': ';
            }
            echo $grades . '/' . number_format($gradinginfo->items[0]->grademax, 2);
        } else {
            print_string('nograde');
        }
        echo '</div>';

        // Feedback text.
        echo format_text($entry->entrycomment, FORMAT_PLAIN);
        echo '</td></tr></table>';
    }
}
