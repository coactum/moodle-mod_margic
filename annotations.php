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
 * File for handling the annotation form.
 *
 * @package     mod_margic
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\output\notification;

require_once("../../config.php");
require_once($CFG->dirroot . '/mod/margic/locallib.php');

global $DB, $CFG;

// Course Module ID.
$id = required_param('id', PARAM_INT);

// Module instance ID as alternative.
$m  = optional_param('m', null, PARAM_INT);

// Param if annotation should be deleted.
$deleteannotation = optional_param('deleteannotation',  0, PARAM_INT); // Annotation to be deleted.

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

// Header.
$PAGE->set_url('/mod/margic/annotations.php', array('id' => $id));
$PAGE->navbar->add(get_string('startoreditentry', 'mod_margic'));
$PAGE->set_title(format_string($moduleinstance->name) . ' - ' . get_string('startoreditentry', 'mod_margic'));
$PAGE->set_heading($course->fullname);

$urlparams = array('id' => $id, 'annotationmode' => 1);

$redirecturl = new moodle_url('/mod/margic/view.php', $urlparams);

// Delete annotation.
if (has_capability('mod/margic:makeannotations', $context) && $deleteannotation !== 0) {
    global $USER;

    if ($DB->record_exists('margic_annotations', array('id' => $deleteannotation, 'margic' => $moduleinstance->id, 'userid' => $USER->id))) {
        $DB->delete_records('margic_annotations', array('id' => $deleteannotation, 'margic' => $moduleinstance->id, 'userid' => $USER->id));

        redirect($redirecturl, get_string('annotationdeleted', 'mod_margic'), null, notification::NOTIFY_SUCCESS);
    } else {
        redirect($redirecturl, get_string('notallowedtodothis', 'mod_margic'), null, notification::NOTIFY_ERROR);
    }
}

// Save annotation
require_once($CFG->dirroot . '/mod/margic/annotation_form.php');

// Instantiate form.
$mform = new annotation_form(null, array('types' => $margic->get_errortypes_for_form()));

if ($fromform = $mform->get_data()) {

    // In this case you process validated data. $mform->get_data() returns data posted in form.
    if ((isset($fromform->annotationid) && $fromform->annotationid !== 0) && isset($fromform->text)) { // Update existing annotation.
        $annotation = $DB->get_record('margic_annotations', array('margic' => $cm->instance, 'entry' => $fromform->entry, 'id' => $fromform->annotationid));

        // Prevent changes by user in hidden form fields.
        if (!$annotation) {
            redirect($redirecturl, get_string('annotationinvalid', 'mod_margic'), null, notification::NOTIFY_ERROR);
        } else if ($annotation->userid != $USER->id) {
            redirect($redirecturl, get_string('notallowedtodothis', 'mod_margic'), null, notification::NOTIFY_ERROR);
        }

        if (!isset($fromform->type)) {
            redirect($redirecturl, get_string('errtypedeleted', 'mod_margic'), null, notification::NOTIFY_ERROR);
        }

        $annotation->timemodified = time();
        $annotation->text = format_text($fromform->text, 2, array('para' => false));
        $annotation->type = $fromform->type;

        $DB->update_record('margic_annotations', $annotation);

        redirect($redirecturl, get_string('annotationedited', 'mod_margic'), null, notification::NOTIFY_SUCCESS);
    } else if ((!isset($fromform->annotationid) || $fromform->annotationid === 0) && isset($fromform->text)) { // New annotation.

        if ($fromform->startcontainer != -1 && $fromform->endcontainer != -1 &&
            $fromform->startposition != -1 && $fromform->endposition != -1) {

            if (!isset($fromform->type)) {
                redirect($redirecturl, get_string('errtypedeleted', 'mod_margic'), null, notification::NOTIFY_ERROR);
            }

            if (preg_match("/[^a-zA-Z0-9()\/[\]]/", $fromform->startcontainer) || preg_match("/[^a-zA-Z0-9()\/[\]]/", $fromform->endcontainer)) {
                redirect($redirecturl, get_string('annotationinvalid', 'mod_margic'), null, notification::NOTIFY_ERROR);
            }

            if (!$DB->record_exists('margic_entries', array('margic' => $cm->instance, 'id' => $fromform->entry))) {
                redirect($redirecturl, get_string('annotationinvalid', 'mod_margic'), null, notification::NOTIFY_ERROR);
            }

            $annotation = new stdClass();
            $annotation->margic = (int) $cm->instance;
            $annotation->entry = (int) $fromform->entry;
            $annotation->userid = $USER->id;
            $annotation->timecreated = time();
            $annotation->timemodified = 0;
            $annotation->type = $fromform->type;
            $annotation->startcontainer = $fromform->startcontainer;
            $annotation->endcontainer = $fromform->endcontainer;
            $annotation->startposition = $fromform->startposition;
            $annotation->endposition = $fromform->endposition;
            $annotation->text = format_text($fromform->text, 2, array('para' => false));

            $DB->insert_record('margic_annotations', $annotation);

            redirect($redirecturl, get_string('annotationadded', 'mod_margic'), null, notification::NOTIFY_SUCCESS);
        } else {
            redirect($redirecturl, get_string('annotationinvalid', 'mod_margic'), null, notification::NOTIFY_ERROR);
        }
    }
} else {
    redirect($redirecturl, get_string('annotationinvalid', 'mod_margic'), null, notification::NOTIFY_ERROR);
}
