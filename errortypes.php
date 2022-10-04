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
 * Prints the error type form for the margic instance.
 *
 * @package     mod_margic
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\output\notification;
use mod_margic\output\margic_error_summary;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once($CFG->dirroot . '/mod/margic/locallib.php');
require_once($CFG->dirroot . '/mod/margic/errortypes_form.php');

// Course_module ID.
$id = required_param('id', PARAM_INT);

// Module instance ID as alternative.
$m  = optional_param('m', null, PARAM_INT);

// If template (1) or margic (2) error type.
$mode  = optional_param('mode', 1, PARAM_INT);

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

require_capability('mod/margic:manageerrortypes', $context);

$redirecturl = new moodle_url('/mod/margic/error_summary.php', array('id' => $id));

if ($edit !== 0) {
    if ($mode == 1) { // If type is template error type.
        $editedtype = $DB->get_record('margic_errortype_templates', array('id' => $edit));
    } else if ($mode == 2) { // If type is margic error type.
        $editedtype = $DB->get_record('margic_errortypes', array('id' => $edit));

        if ($moduleinstance->id !== $editedtype->margic) {
            redirect($redirecturl, get_string('errortypecantbeedited', 'mod_margic'), null, notification::NOTIFY_ERROR);
        }
    }

    if ($editedtype && $mode == 2 ||
        ((isset($editedtype->defaulttype) && $editedtype->defaulttype == 1 && has_capability('mod/margic:editdefaulterrortypes', $context))
        || (isset($editedtype->defaulttype) && isset($editedtype->userid) && $editedtype->defaulttype == 0 && $editedtype->userid == $USER->id))) {
        $editedtypeid = $edit;
        $editedtypename = $editedtype->name;
        $editedcolor = $editedtype->color;

        if ($mode == 1) {
            $editeddefaulttype = $editedtype->defaulttype;
        }
    }
}

// Instantiate form.
$mform = new errortypes_form(null, array('editdefaulttype' => has_capability('mod/margic:editdefaulterrortypes', $context), 'mode' => $mode));

if (isset($editedtypeid)) {
    if ($mode == 1) { // If type is template error type.
        $mform->set_data(array('id' => $id, 'mode' => $mode, 'typeid' => $editedtypeid,
            'typename' => $editedtypename, 'color' => $editedcolor, 'standardtype' => $editeddefaulttype));
    } else if ($mode == 2) {
        $mform->set_data(array('id' => $id, 'mode' => $mode, 'typeid' => $editedtypeid, 'typename' => $editedtypename, 'color' => $editedcolor));
    }
} else {
    $mform->set_data(array('id' => $id, 'mode' => $mode));
}

if ($mform->is_cancelled()) {
    redirect($redirecturl);
} else if ($fromform = $mform->get_data()) {
    $defaulterrortypetemplateseditable = get_config('margic', 'defaulterrortypetemplateseditable');

    // In this case you process validated data. $mform->get_data() returns data posted in form.
    if ($fromform->typeid == 0 && isset($fromform->typename)) { // Create new error type.

        $errortype = new stdClass();
        $errortype->timecreated = time();
        $errortype->timemodified = 0;
        $errortype->name = format_text($fromform->typename, 1, array('para' => false));
        $errortype->color = $fromform->color;

        if (isset($fromform->standardtype) && $fromform->standardtype === 1 && has_capability('mod/margic:editdefaulterrortypes', $context)) {
            $errortype->userid = 0;
            $errortype->defaulttype = 1;
        } else {
            $errortype->userid = $USER->id;
            $errortype->defaulttype = 0;
        }

        if ($mode == 2) { // If type is margic error type.
            $errortype->priority = $margic->get_margic_errortypes()[array_key_last($margic->get_margic_errortypes())]->priority + 1;
            $errortype->margic = $moduleinstance->id;
        }

        if ($mode == 1) { // If type is template error type.
            $DB->insert_record('margic_errortype_templates', $errortype);

        } else if ($mode == 2) { // If type is margic error type.
            $DB->insert_record('margic_errortypes', $errortype);
        }

        redirect($redirecturl, get_string('errortypeadded', 'mod_margic'), null, notification::NOTIFY_SUCCESS);
    } else if ($fromform->typeid !== 0 && isset($fromform->typename)) { // Update existing annotation type.

        if ($mode == 1) { // If type is template error type.
            $errortype = $DB->get_record('margic_errortype_templates', array('id' => $fromform->typeid));
        } else if ($mode == 2) { // If type is margic error type.
            $errortype = $DB->get_record('margic_errortypes', array('id' => $fromform->typeid));
        }

        if ($errortype &&
            ($mode == 2 ||
            (isset($errortype->defaulttype) && $errortype->defaulttype == 1 && has_capability('mod/margic:editdefaulterrortypes', $context) && $defaulterrortypetemplateseditable)
            || (isset($errortype->defaulttype) && isset($errortype->userid) && $errortype->defaulttype == 0 && $errortype->userid == $USER->id))) {

            $errortype->timemodified = time();
            $errortype->name = format_text($fromform->typename, 1, array('para' => false));
            $errortype->color = $fromform->color;

            if ($mode == 1 && has_capability('mod/margic:editdefaulterrortypes', $context)) {
                global $USER;
                if ($fromform->standardtype === 1 && $errortype->defaulttype !== $fromform->standardtype) {
                    $errortype->defaulttype = 1;
                    $errortype->userid = 0;
                } else if ($fromform->standardtype === 0 && $errortype->defaulttype !== $fromform->standardtype) {
                    $errortype->defaulttype = 0;
                    $errortype->userid = $USER->id;
                }
            }

            if ($mode == 1) { // If type is template error type.
                $DB->update_record('margic_errortype_templates', $errortype);

            } else if ($mode == 2) { // If type is margic error type.
                $DB->update_record('margic_errortypes', $errortype);
            }

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

$navtitle = '';


if (isset($editedtypeid)) {
    $navtitle = get_string('editerrortype', 'mod_margic');
} else {
    $navtitle = get_string('adderrortype', 'mod_margic');
}

if ($mode == 1) { // If type is template error type.
    $navtitle .= ' (' . get_string('template', 'mod_margic') . ')';
} else if ($mode == 2) { // If type is margic error type.
    $navtitle .= ' (' . get_string('modulename', 'mod_margic') . ')';
}

$PAGE->navbar->add($navtitle);

$PAGE->set_title(get_string('modulename', 'mod_margic').': ' . $margicname);
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->force_settings_menu();

echo $OUTPUT->header();
echo $OUTPUT->heading($margicname);

if ($moduleinstance->intro) {
    echo $OUTPUT->box(format_module_intro('margic', $moduleinstance, $cm->id), 'generalbox mod_introbox', 'newmoduleintro');
}

if (isset($editedtypeid) && $mode == 1) {
    if ($editeddefaulttype) {
        echo $OUTPUT->notification(get_string('warningeditdefaulterrortypetemplate', 'mod_margic'), notification::NOTIFY_ERROR);
    }

    echo $OUTPUT->notification(get_string('changetemplate', 'mod_margic'), notification::NOTIFY_WARNING);
}

$mform->display();

echo $OUTPUT->footer();
