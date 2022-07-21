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
 * Prints the annotation type form for the margic instance.
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
require_once($CFG->dirroot . '/mod/margic/errortypes_form.php');

// Course_module ID.
$id = required_param('id', PARAM_INT);

// Module instance ID as alternative.
$m  = optional_param('m', null, PARAM_INT);

// ID of type that should be edited.
$edit  = optional_param('edit', 0, PARAM_INT);

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

$redirecturl = new moodle_url('/mod/margic/annotations_summary.php', array('id' => $id));

if ($edit !== 0) {
    $editedtype = $DB->get_record('margic_errortypes', array('id' => $edit));
    if ($editedtype && (isset($editedtype->defaulttype) && $editedtype->defaulttype == 1 && has_capability('mod/margic:editdefaulterrortypes', $context))
        || (isset($editedtype->defaulttype) && isset($editedtype->userid) && $editedtype->defaulttype == 0 && $editedtype->userid == $USER->id)) {
        $editedtypeid = $edit;
        $editedtypename = $editedtype->name;
        $editedcolor = $editedtype->color;
        $editeddefaulttype = $editedtype->defaulttype;
    }
}

// Instantiate form.
$mform = new errortypes_form(null, array('editdefaulttype' => has_capability('mod/margic:editdefaulterrortypes', $context)));

if (isset($editedtypeid)) {
    $mform->set_data(array('id' => $id, 'typeid' => $editedtypeid, 'typename' => $editedtypename, 'color' => $editedcolor, 'defaulttype' => $editeddefaulttype));
} else {
    $mform->set_data(array('id' => $id));
}

if ($mform->is_cancelled()) {
    redirect($redirecturl);
} else if ($fromform = $mform->get_data()) {
    // In this case you process validated data. $mform->get_data() returns data posted in form.
    if ($fromform->typeid == 0 && isset($fromform->typename)) { // Create new annotation type.

        $errortype = new stdClass();
        $errortype->timecreated = time();
        $errortype->timemodified = 0;
        $errortype->name = format_text($fromform->typename, 1, array('para' => false));
        $errortype->color = $fromform->color;

        if ($fromform->defaulttype === 1 && has_capability('mod/margic:editdefaulterrortypes', $context)) {
            $errortype->userid = 0;
            $errortype->defaulttype = 1;
        } else {
            $errortype->userid = $USER->id;
            $errortype->defaulttype = 0;
        }

        $errortype->order = 0; // Temp.
        $errortype->unused = 0;
        $errortype->replaces = null;

        $DB->insert_record('margic_errortypes', $errortype);

        redirect($redirecturl, get_string('errortypeadded', 'mod_margic'), null, notification::NOTIFY_SUCCESS);
    } else if ($fromform->typeid !== 0 && isset($fromform->typename)) { // Update existing annotation type.
        $errortype = $DB->get_record('margic_errortypes', array('id' => $fromform->typeid));

        if ($errortype && (isset($errortype->defaulttype) && $errortype->defaulttype == 1 && has_capability('mod/margic:editdefaulterrortypes', $context))
        || (isset($errortype->defaulttype) && isset($errortype->userid) && $errortype->defaulttype == 0 && $errortype->userid == $USER->id)) {
            $errortype->timemodified = time();
            $errortype->name = format_text($fromform->typename, 1, array('para' => false));
            $errortype->color = $fromform->color;

            if (has_capability('mod/margic:editdefaulterrortypes', $context)) {
                global $USER;
                if ($fromform->defaulttype === 1 && $errortype->defaulttype !== $fromform->defaulttype) {
                    $errortype->defaulttype = 1;
                    $errortype->userid = 0;
                } else if ($fromform->defaulttype === 0 && $errortype->defaulttype !== $fromform->defaulttype) {
                    $errortype->defaulttype = 0;
                    $errortype->userid = $USER->id;
                }
            }

            $errortype->order = 0; // Temp.
            $errortype->unused = 0;
            $errortype->replaces = null;


            $DB->update_record('margic_errortypes', $errortype);
            redirect($redirecturl, get_string('errortypeedited', 'mod_margic'), null, notification::NOTIFY_SUCCESS);
        } else {
            redirect($redirecturl, get_string('errortypecantbeedited', 'mod_margic'), null, notification::NOTIFY_ERROR);
        }

    } else {
        redirect($redirecturl, get_string('errortypeinvalid', 'mod_margic'), null, notification::NOTIFY_ERROR);
    }
}

// Get the name for this margic activity.
$margicname = format_string($moduleinstance->name, true, array(
    'context' => $context
));

$PAGE->set_url('/mod/margic/errortypes.php', array('id' => $cm->id));

if (isset($editedtypeid)) {
    $PAGE->navbar->add(get_string('editerrortype', 'mod_margic'));
} else {
    $PAGE->navbar->add(get_string('adderrortype', 'mod_margic'));
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

if (isset($editedtypeid)) {
    echo $OUTPUT->notification(get_string('changesforall', 'mod_margic'), notification::NOTIFY_WARNING);
}

$mform->display();

echo $OUTPUT->footer();
