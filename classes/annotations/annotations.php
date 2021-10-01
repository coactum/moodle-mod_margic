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
 * File containing the annotation menu.
 *
 * @package     mod_annotateddiary
 * @copyright   2021 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core\output\notification;

require_once($CFG->dirroot . '/mod/annotateddiary/classes/annotations/annotation_form.php');

global $DB;


echo '<div class="col-sm-4 annotationarea annotationarea-'.$entryid.'">';

// Show annotations.
$annotations = $DB->get_records('annotateddiary_annotations', array('annotateddiary' => $cm->instance, 'entry' => $entryid));

echo '<h2 class="text-center">'.get_string('annotations', 'annotateddiary').'</h2>';

if ($annotations) {
    foreach ($annotations as $annotation) {
        echo '<div class="annotation-box">';
        echo '<span id="annotation-'.$annotation->id.'" class="annotation annotation-'.$annotation->id.'">' . $annotation->text . '</span>';
        echo '<span class="pull-right"><a href="javascript:void(0);"><i id="edit-annotation-'.$annotation->id.'" class="fa fa-2x fa-pencil m-r-1 edit-annotation" aria-hidden="true"></i></a><a href="'. new moodle_url('/mod/annotateddiary/view.php', array('id' => $id, 'annotationmode' => 1, 'deleteannotation' => $annotation->id)) . '"><i id="delete-annotation-'.$annotation->id.'" class="fa fa-2x fa-trash delete-annotation" aria-hidden="true"></i></a></span>';
        echo '<br>';
        echo '</div>';
    }
}


// Instantiate form.
$mform = new annotation_form(null, array('entry' => $entryid));

if ($fromform = $mform->get_data()) {
    // In this case you process validated data. $mform->get_data() returns data posted in form.

    if ((isset($fromform->annotationid[$entryid]) && $fromform->annotationid[$entryid] !== 0) && isset($fromform->text[$entryid])) { // Update existing annotation.
        $annotation = $DB->get_record('annotateddiary_annotations', array('annotateddiary' => $cm->instance, 'entry' => $entryid, 'id' => $fromform->annotationid[$entryid]));
        $annotation->timemodified = time();
        $annotation->text = $fromform->text[$entryid];

        $DB->update_record('annotateddiary_annotations', $annotation);

        redirect(new moodle_url('/mod/annotateddiary/view.php', array('id' => $id, 'annotationmode' => 1)), get_string('annotationedited', 'mod_annotateddiary'), null, notification::NOTIFY_SUCCESS);

    } else if ((!isset($fromform->annotationid[$entryid]) || $fromform->annotationid[$entryid] === 0) && isset($fromform->text[$entryid])) { // New annotation.

        if ($fromform->startcontainer[$entryid] != -1 && $fromform->endcontainer[$entryid] != -1 && $fromform->startposition[$entryid] != -1 && $fromform->endposition[$entryid] != -1) {
            $annotation = new stdClass();
            $annotation->annotateddiary = (int) $cm->instance;
            $annotation->entry = (int) $entryid;
            $annotation->userid = $USER->id;
            $annotation->timecreated = time();
            $annotation->timemodified = 0;
            $annotation->type = 1;
            $annotation->startcontainer = $fromform->startcontainer[$entryid];
            $annotation->endcontainer = $fromform->endcontainer[$entryid];
            $annotation->startposition = $fromform->startposition[$entryid];
            $annotation->endposition = $fromform->endposition[$entryid];
            $annotation->text = $fromform->text[$entryid];

            $DB->insert_record('annotateddiary_annotations', $annotation);

            redirect(new moodle_url('/mod/annotateddiary/view.php', array('id' => $id, 'annotationmode' => 1)), get_string('annotationadded', 'mod_annotateddiary'), null, notification::NOTIFY_SUCCESS);

        } else {
            redirect(new moodle_url('/mod/annotateddiary/view.php', array('id' => $id, 'annotationmode' => 1)), get_string('annotationinvalid', 'mod_annotateddiary'), null, notification::NOTIFY_ERROR);
        }


    }
} else {
    // This branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
    // or on the first display of the form.

    // Set default data.
    $mform->set_data(array('id' => $id));
}

echo '<div class="annotation-box annotation-form">';
// Displays the form.
$mform->display();
echo '</div>';

echo '</div>';
