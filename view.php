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
 * Prints an instance of mod_margic.
 *
 * @package     mod_margic
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_margic\output\margic_view;
use mod_margic\local\results;
use core\output\notification;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once($CFG->dirroot . '/mod/margic/locallib.php');

// Course_module ID.
$id = required_param('id', PARAM_INT);

// Module instance ID as alternative.
$m  = optional_param('m', null, PARAM_INT);

// Param containing user id if only entries for one user should be displayed.
$userid = optional_param('userid',  0, PARAM_INT); // User id.

// Param containing the requested action.
$action = optional_param('action',  'currententry', PARAM_ALPHANUMEXT);

// Param containing the page count.
$pagecount = optional_param('pagecount', 0, PARAM_INT);

// Param containing the active page.
$page = optional_param('page', 0, PARAM_INT);

// Param if annotation mode is activated.
$annotationmode = optional_param('annotationmode',  0, PARAM_BOOL); // Annotation mode.

$margic = margic::get_margic_instance($id, $m, $userid, $action, $pagecount, $page);

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

$canmanageentries = has_capability('mod/margic:manageentries', $context);
$canaddentries = has_capability('mod/margic:addentries', $context);

if (!$canaddentries) {
    throw new moodle_exception(get_string('accessdenied', 'margic'));
}

if ($pagecount) {
    // Redirect if pagecount is updated.
    redirect(new moodle_url('/mod/margic/view.php', array('id' => $id)), null, null, null);
}

// Toolbar action handler for download.
if (!empty($action) && $action == 'download' && has_capability('mod/margic:addentries', $context)) {
    // Call download entries function in lib.php.
    results::download_entries($context, $course, $moduleinstance);
}

// Trigger course_module_viewed event.
$event = \mod_margic\event\course_module_viewed::create(array(
    'objectid' => $id,
    'context' => $context
));
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('margic', $moduleinstance);
$event->trigger();

// Get the name for this margic activity.
$margicname = format_string($moduleinstance->name, true, array(
    'context' => $context
));

$canmakeannotations = has_capability('mod/margic:makeannotations', $context);

// Add javascript and navbar element if annotationmode is activated and user has capability.
if ($annotationmode === 1 && has_capability('mod/margic:viewannotations', $context)) {

    $PAGE->set_url('/mod/margic/view.php', array(
        'id' => $cm->id,
        'annotationmode' => 1,
    ));

    $PAGE->navbar->add(get_string("viewentries", "margic"), new moodle_url('/mod/margic/view.php', array('id' => $cm->id)));
    $PAGE->navbar->add(get_string('viewannotations', 'mod_margic'));

    $PAGE->requires->js_call_amd('mod_margic/annotations', 'init',
        array( 'cmid' => $cm->id, 'canmakeannotations' => $canmakeannotations, 'myuserid' => $USER->id));
} else {
    // Header.
    $PAGE->set_url('/mod/margic/view.php', array(
        'id' => $cm->id
    ));
    $PAGE->navbar->add(get_string("viewentries", "margic"));
}

$PAGE->set_title(get_string('modulename', 'mod_margic').': ' . $margicname);
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->force_settings_menu();

echo $OUTPUT->header();
echo $OUTPUT->heading($margicname);

if ($moduleinstance->intro) {
    echo $OUTPUT->box(format_module_intro('margic', $moduleinstance, $cm->id), 'generalbox mod_introbox', 'newmoduleintro');
}

// Get grading of current user when margic is rated.
if ($moduleinstance->assessed != 0) {
    $ratingaggregationmode = results::get_margic_aggregation($moduleinstance->assessed) . ' ' . get_string('forallmyentries', 'mod_margic');
    $gradinginfo = grade_get_grades($course->id, 'mod', 'margic', $moduleinstance->id, $USER->id);
    $userfinalgrade = $gradinginfo->items[0]->grades[$USER->id];
    $currentuserrating = $userfinalgrade->str_long_grade;
} else {
    $ratingaggregationmode = false;
    $currentuserrating = false;
}

// Handle groups.
echo groups_print_activity_menu($cm, $CFG->wwwroot . "/mod/margic/view.php?id=$id");

$edittimes = results::margic_get_edittime_options($moduleinstance);

// Output page.
$page = new margic_view($margic, $cm, $context, $moduleinstance, $margic->get_entries_grouped_by_pagecount(),
    $margic->get_sortmode(), get_config('margic', 'entrybgc'), get_config('margic', 'textbgc'),
    $margic->get_annotationarea_width(), $moduleinstance->editentries, $edittimes->edittimestarts,
    $edittimes->edittimenotstarted, $edittimes->edittimeends, $edittimes->edittimehasended, $canmanageentries,
    sesskey(), $currentuserrating, $ratingaggregationmode, $course, $userid, $margic->get_pagecountoptions(),
    $margic->get_pagebar(), count($margic->get_entries()), $annotationmode, $canmakeannotations,
    $margic->get_errortypes_for_form());

echo $OUTPUT->render($page);

echo $OUTPUT->footer();
