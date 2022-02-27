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
 * Prints the annotation summary for the margic instance.
 *
 * @package     mod_margic
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\output\notification;
use mod_margic\output\margic_annotations_summary;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once($CFG->dirroot . '/mod/margic/locallib.php');

// Course_module ID.
$id = required_param('id', PARAM_INT);

// Module instance ID as alternative.
$m  = optional_param('m', null, PARAM_INT);

$margic = margic::get_margic_instance($id, $m, false, 'currententry', 0, 1);

$moduleinstance = $margic->get_module_instance();
$course = $margic->get_course();
$context = $margic->get_context();
$cm = $margic->get_course_module();

if (! $cm) {
    throw new moodle_exception(get_string('incorrectmodule', 'margic'));
}

if (! $course) {
    throw new moodle_exception(get_string('incorrectcourseid', 'margic'));
}

if (!$moduleinstance) {
    throw new moodle_exception(get_string('incorrectmodule', 'margic'));
}

if (! $coursesections = $DB->get_record("course_sections", array(
    "id" => $cm->section
))) {
    throw new moodle_exception(get_string('incorrectmodule', 'margic'));
}

require_login($course, true, $cm);

require_capability('mod/margic:makeannotations', $context);

// Get the name for this margic activity.
$margicname = format_string($moduleinstance->name, true, array(
    'context' => $context
));

$PAGE->set_url('/mod/margic/annotations_summary.php', array('id' => $cm->id));
$PAGE->navbar->add(get_string('annotationssummary', 'mod_margic'));

$PAGE->set_title(get_string('modulename', 'mod_margic').': ' . $margicname);
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->force_settings_menu();

echo $OUTPUT->header();
echo $OUTPUT->heading($margicname);

if ($moduleinstance->intro) {
    echo $OUTPUT->box(format_module_intro('margic', $moduleinstance, $cm->id), 'generalbox mod_introbox', 'newmoduleintro');
}

$participants = array_values(get_enrolled_users($context, 'mod/margic:addentries'));
$annotationtypes = $margic->get_annotationtypes_for_form();

foreach ($participants as $key => $participant) {
    $participants[$key]->errors = array();

    foreach ($annotationtypes as $i => $type) {
        $sql = "SELECT COUNT(*)
            FROM {margic_annotations} a
            JOIN {margic_entries} e ON e.id = a.entry
            WHERE e.margic = :margic AND
                e.userid = :userid AND
                a.type = :atype";
        $params = array('margic' => $moduleinstance->id, 'userid' => $participant->id, 'atype' => $i);
        $count = $DB->count_records_sql($sql, $params);

        $participants[$key]->errors[$i] = $count;
    }

    $participants[$key]->errors = array_values($participants[$key]->errors);
}

$annotationtypes = array_values($annotationtypes);

// Output page.
$page = new margic_annotations_summary($cm->id, $participants, $annotationtypes);

echo $OUTPUT->render($page);

echo $OUTPUT->footer();
