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
 * Prints the error summary for the margic instance.
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

// Course_module ID.
$id = required_param('id', PARAM_INT);

// Module instance ID as alternative.
$m = optional_param('m', null, PARAM_INT);

// ID of type that should be deleted.
$delete = optional_param('delete', 0, PARAM_INT);

// ID of type that should be deleted.
$addtomargic = optional_param('addtomargic', 0, PARAM_INT);

// ID of type where priority should be changed.
$priority = optional_param('priority', 0, PARAM_INT);
$action = optional_param('action', 0, PARAM_INT);

// If template (1) or margic (2) error type.
$mode = optional_param('mode', null, PARAM_INT);

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

if (! $coursesections = $DB->get_record("course_sections", ["id" => $cm->section])) {
    throw new moodle_exception(get_string('incorrectmodule', 'margic'));
}

require_login($course, true, $cm);

require_capability('mod/margic:viewerrorsummary', $context);

$manageerrortypes = has_capability('mod/margic:manageerrortypes', $context);
$defaulterrortypetemplateseditable = get_config('margic', 'defaulterrortypetemplateseditable');

global $USER;

// Add type to margic.
if ($addtomargic && $manageerrortypes) {
    require_sesskey();

    $redirecturl = new moodle_url('/mod/margic/error_summary.php', ['id' => $id]);

    if ($DB->record_exists('margic_errortype_templates', ['id' => $addtomargic])) {

        global $USER;

        $type = $DB->get_record('margic_errortype_templates', ['id' => $addtomargic]);

        if ($type->defaulttype == 1 || ($type->defaulttype == 0 && $type->userid == $USER->id)) {

            $etypes = $margic->get_margic_errortypes();

            if ($etypes) {
                $type->priority = $etypes[array_key_last($etypes)]->priority + 1;
            } else {
                $type->priority = 1;
            }

            $type->margic = $moduleinstance->id;

            $DB->insert_record('margic_errortypes', $type);

            redirect($redirecturl, get_string('errortypeadded', 'mod_margic'), null, notification::NOTIFY_SUCCESS);
        } else {
            redirect($redirecturl, get_string('notallowedtodothis', 'mod_margic'), null, notification::NOTIFY_ERROR);
        }
    } else {
        redirect($redirecturl, get_string('notallowedtodothis', 'mod_margic'), null, notification::NOTIFY_ERROR);
    }
}

// Change priority.
if ($manageerrortypes && $mode == 2 && $priority && $action && $DB->record_exists('margic_errortypes', ['id' => $priority])) {
    require_sesskey();

    $redirecturl = new moodle_url('/mod/margic/error_summary.php', ['id' => $id]);

    $type = $DB->get_record('margic_errortypes', ['margic' => $moduleinstance->id, 'id' => $priority]);

    $etypes = $margic->get_margic_errortypes();

    $prioritychanged = false;
    $oldpriority = 0;

    // Increase priority (show more in front).
    if ($type && $action == 1 && $type->priority != $etypes[array_key_first($etypes)]->priority) {
        $oldpriority = $type->priority;
        $type->priority -= 1;
        $prioritychanged = true;

        $typeswitched = $DB->get_record('margic_errortypes', ['margic' => $moduleinstance->id, 'priority' => $type->priority]);

        if (!$typeswitched) { // If no type with priority+1 search for types with higher priority values.
            $typeswitched = $DB->get_records_select('margic_errortypes',
                "margic = $moduleinstance->id AND priority < $type->priority", null, 'priority ASC');

            if ($typeswitched && isset($typeswitched[array_key_first($typeswitched)])) {
                $typeswitched = $typeswitched[array_key_first($typeswitched)];
            }
        }

    } else if ($type && $action == 2 && $type->priority != $DB->count_records('margic_errortypes',
        ['margic' => $moduleinstance->id]) + 1) { // Decrease priority (move further back).

        $oldpriority = $type->priority;
        $type->priority += 1;
        $prioritychanged = true;

        $typeswitched = $DB->get_record('margic_errortypes', ['margic' => $moduleinstance->id, 'priority' => $type->priority]);

        if (!$typeswitched) { // If no type with priority+1 search for types with higher priority values.
            $typeswitched = $DB->get_records_select('margic_errortypes',
                "margic = $moduleinstance->id AND priority > $type->priority", null, 'priority ASC');

            if ($typeswitched && isset($typeswitched[array_key_first($typeswitched)])) {
                $typeswitched = $typeswitched[array_key_first($typeswitched)];
            }
        }
    } else {
        redirect($redirecturl, get_string('prioritynotchanged', 'mod_margic'), null, notification::NOTIFY_ERROR);
    }

    if ($typeswitched) {
        // Update priority for type.
        $DB->update_record('margic_errortypes', $type);

        // Update priority for type that type is switched with.
        $typeswitched->priority = $oldpriority;
        $DB->update_record('margic_errortypes', $typeswitched);

        redirect($redirecturl, get_string('prioritychanged', 'mod_margic'), null, notification::NOTIFY_SUCCESS);
    } else {
        redirect($redirecturl, get_string('prioritynotchanged', 'mod_margic'), null, notification::NOTIFY_ERROR);
    }
}

// Delete annotation.
if ($manageerrortypes && $delete !== 0 && $mode) {

    require_sesskey();

    $redirecturl = new moodle_url('/mod/margic/error_summary.php', ['id' => $id]);

    if ($mode == 1) { // If type is template error type.
        $table = 'margic_errortype_templates';
    } else if ($mode == 2) { // If type is margic error type.
        $table = 'margic_errortypes';
    }

    if ($DB->record_exists($table, ['id' => $delete])) {

        $type = $DB->get_record($table, ['id' => $delete]);

        if ($mode == 2 ||
            ($type->defaulttype == 1 && has_capability('mod/margic:editdefaulterrortypes', $context))
            || ($type->defaulttype == 0 && $type->userid == $USER->id)) {

            $DB->delete_records($table, ['id' => $delete]);
            redirect($redirecturl, get_string('errortypedeleted', 'mod_margic'), null, notification::NOTIFY_SUCCESS);
        } else {
            redirect($redirecturl, get_string('notallowedtodothis', 'mod_margic'), null, notification::NOTIFY_ERROR);
        }
    } else {
        redirect($redirecturl, get_string('notallowedtodothis', 'mod_margic'), null, notification::NOTIFY_ERROR);
    }
}

// Get the name for this margic activity.
$margicname = format_string($moduleinstance->name, true, ['context' => $context]);

$PAGE->set_url('/mod/margic/error_summary.php', ['id' => $cm->id]);
$PAGE->navbar->add(get_string('errorsummary', 'mod_margic'));

$PAGE->set_title(get_string('modulename', 'mod_margic').': ' . $margicname);
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

if ($CFG->branch < 400) {
    $PAGE->force_settings_menu();
}

echo $OUTPUT->header();

if ($CFG->branch < 400) {
    echo $OUTPUT->heading($margicname);

    if ($moduleinstance->intro) {
        echo $OUTPUT->box(format_module_intro('margic', $moduleinstance, $cm->id), 'generalbox', 'intro');
    }
}

$participants = array_values(get_enrolled_users($context, 'mod/margic:addentries'));
$errortypes = $margic->get_errortypes_for_form();

foreach ($participants as $key => $participant) {
    if (has_capability('mod/margic:viewerrorsfromallparticipants', $context) || $participant->id == $USER->id) {
        $participants[$key]->errors = [];

        foreach ($errortypes as $i => $type) {
            $sql = "SELECT COUNT(*)
                FROM {margic_annotations} a
                JOIN {margic_entries} e ON e.id = a.entry
                WHERE e.margic = :margic AND
                    e.userid = :userid AND
                    a.type = :atype";
            $params = ['margic' => $moduleinstance->id, 'userid' => $participant->id, 'atype' => $i];
            $count = $DB->count_records_sql($sql, $params);

            $participants[$key]->errors[$i] = $count;
        }

        $participants[$key]->errors = array_values($participants[$key]->errors);
    } else {
        unset($participants[$key]);
    }
}

$participants = array_values($participants);

$margicerrortypes = $margic->get_margic_errortypes();
$strmanager = get_string_manager();

foreach ($margicerrortypes as $i => $type) {
    $margicerrortypes[$i]->canbeedited = true;

    if ($strmanager->string_exists($type->name, 'mod_margic')) {
        $margicerrortypes[$i]->name = get_string($type->name, 'mod_margic');
    } else {
        $margicerrortypes[$i]->name = $type->name;
    }
}

$margicerrortypes = array_values($margicerrortypes);

global $USER;

$errortypetemplates = $margic->get_all_errortype_templates();
foreach ($errortypetemplates as $id => $templatetype) {
    if ($templatetype->defaulttype == 1) {
        $errortypetemplates[$id]->type = get_string('standard', 'mod_margic');

        if (has_capability('mod/margic:editdefaulterrortypes', $context) && $defaulterrortypetemplateseditable) {
            $errortypetemplates[$id]->canbeedited = true;
            $errortypetemplates[$id]->canbedeleted = true;
        } else if (has_capability('mod/margic:editdefaulterrortypes', $context)) {
            $errortypetemplates[$id]->canbeedited = false;
            $errortypetemplates[$id]->canbedeleted = true;
        } else {
            $errortypetemplates[$id]->canbeedited = false;
            $errortypetemplates[$id]->canbedeleted = false;
        }
    } else {
        $errortypetemplates[$id]->type = get_string('custom', 'mod_margic');

        if ($templatetype->userid === $USER->id) {
            $errortypetemplates[$id]->canbeedited = true;
            $errortypetemplates[$id]->canbedeleted = true;
        } else {
            $errortypetemplates[$id]->canbeedited = false;
            $errortypetemplates[$id]->canbedeleted = false;
        }
    }

    if ($templatetype->defaulttype == 1 && $strmanager->string_exists($templatetype->name, 'mod_margic')) {
        $errortypetemplates[$id]->name = get_string($templatetype->name, 'mod_margic');
    } else {
        $errortypetemplates[$id]->name = $templatetype->name;
    }
}

$errortypetemplates = array_values($errortypetemplates);

// Output page.
$page = new margic_error_summary($cm->id, $participants, $margicerrortypes, $errortypetemplates, sesskey(),
    $manageerrortypes, $defaulterrortypetemplateseditable);

echo $OUTPUT->render($page);

echo $OUTPUT->footer();
