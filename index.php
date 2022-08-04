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
 * This page lists all the instances of margic in a particular course
 *
 * @package     mod_margic
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . "/../../config.php");
require_once("lib.php");

$id = required_param('id', PARAM_INT); // Course.

if (! $course = $DB->get_record('course', array(
    'id' => $id
))) {
    throw new moodle_exception(get_string('incorrectcourseid', 'margic'));
}

require_course_login($course);

// Header.
$strmargics = get_string('modulenameplural', 'margic');
$PAGE->set_pagelayout('incourse');
$PAGE->set_url('/mod/margic/index.php', array(
    'id' => $id
));
$PAGE->navbar->add($strmargics);
$PAGE->set_title($strmargics);
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading($strmargics);

if (! $margics = get_all_instances_in_course('margic', $course)) {
    notice(get_string('thereareno', 'moodle', get_string('modulenameplural', 'margic')), '../../course/view.php?id=$course->id');
    die();
}

// Sections.
$usesections = course_format_uses_sections($course->format);
if ($usesections) {
    $modinfo = get_fast_modinfo($course);
    $sections = $modinfo->get_section_info_all();
}

$timenow = time();

// Table data.
$table = new html_table();

$table->head = array();
$table->align = array();
if ($usesections) {
    // Add column heading based on the course format. e.g. Week, Topic.
    $table->head[] = get_string('sectionname', 'format_' . $course->format);
    $table->align[] = 'left';
}
// Add activity, Name, and activity, Description, headings.
$table->head[] = get_string('name');
$table->align[] = 'left';
$table->head[] = get_string('description');
$table->align[] = 'left';

$currentsection = '';
$i = 0;

foreach ($margics as $margic) {

    $context = context_module::instance($margic->coursemodule);

    // Section.
    $printsection = '';
    if ($margic->section !== $currentsection) {
        if ($margic->section) {
            $printsection = get_section_name($course, $sections[$margic->section]);
        }
        if ($currentsection !== '') {
            $table->data[$i] = 'hr';
            $i ++;
        }
        $currentsection = $margic->section;
    }
    if ($usesections) {
        $table->data[$i][] = $printsection;
    }

    // Link.
    $margicname = format_string($margic->name, true, array(
        'context' => $context
    ));
    if (! $margic->visible) {
        // Show dimmed if the mod is hidden.
        $table->data[$i][] = "<a class=\"dimmed\" href=\"view.php?id=$margic->coursemodule\">" . $margicname . "</a>";
    } else {
        // Show normal if the mod is visible.
        $table->data[$i][] = "<a href=\"view.php?id=$margic->coursemodule\">" . $margicname . "</a>";
    }

    // Description.
    $table->data[$i][] = format_module_intro('margic', $margic, $margic->coursemodule);

    $i ++;
}

echo "<br />";

echo html_writer::table($table);

// Trigger course module instance list event.
$params = array(
    'context' => context_course::instance($course->id)
);
$event = \mod_margic\event\course_module_instance_list_viewed::create($params);
$event->add_record_snapshot('course', $course);
$event->trigger();

echo $OUTPUT->footer();
