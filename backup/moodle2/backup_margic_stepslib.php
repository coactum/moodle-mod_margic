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
 * Backup steps for mod_margic are defined here.
 *
 * @package     mod_margic
 * @category    backup
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define the complete structure for backup, with file and id annotations.
 */
class backup_margic_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines the structure of the resulting xml file.
     *
     * @return backup_nested_element The structure wrapped by the common 'activity' element.
     */
    protected function define_structure() {
        $userinfo = $this->get_setting_value('userinfo');

        // Replace with the attributes and final elements that the element will handle.
        $margic = new backup_nested_element('margic', ['id'], [
            'name', 'intro', 'introformat', 'timecreated', 'timemodified',
            'scale', 'assessed', 'assesstimestart', 'assesstimefinish',
            'timeopen', 'timeclose', 'editentries', 'editentrydates', 'annotationareawidth', ]);

        $errortypes = new backup_nested_element('errortypes');
        $errortype = new backup_nested_element('errortype', ['id'], [
            'timecreated', 'timemodified', 'name', 'color', 'priority', ]);

        $entries = new backup_nested_element('entries');
        $entry = new backup_nested_element('entry', ['id'], [
            'userid', 'timecreated', 'timemodified', 'text', 'format',
            'rating', 'feedback', 'formatfeedback', 'teacher',
            'timemarked', 'baseentry', ]);

        $annotations = new backup_nested_element('annotations');
        $annotation = new backup_nested_element('annotation', ['id'], [
            'userid', 'timecreated', 'timemodified', 'type', 'startcontainer', 'endcontainer',
            'startoffset', 'endoffset', 'annotationstart', 'annotationend', 'exact', 'prefix', 'suffix', 'text', ]);

        $ratings = new backup_nested_element('ratings');
        $rating = new backup_nested_element('rating', ['id'], [
            'component', 'ratingarea', 'scaleid', 'value', 'userid',
            'timecreated', 'timemodified', ]);

        // Build the tree with these elements with $margic as the root of the backup tree.
        $margic->add_child($errortypes);
        $errortypes->add_child($errortype);

        $margic->add_child($entries);
        $entries->add_child($entry);

        $entry->add_child($annotations);
        $annotations->add_child($annotation);

        $entry->add_child($ratings);
        $ratings->add_child($rating);

        // Define the source tables for the elements.

        $margic->set_source_table('margic', ['id' => backup::VAR_ACTIVITYID]);

        // Errortypes.
        $errortype->set_source_table('margic_errortypes', ['margic' => backup::VAR_PARENTID]);

        if ($userinfo) {

            // Entries.
            $entry->set_source_table('margic_entries', ['margic' => backup::VAR_PARENTID]);

            // Annotations.
            $annotation->set_source_table('margic_annotations', ['entry' => backup::VAR_PARENTID]);

            // Ratings (core).
            $rating->set_source_table('rating', ['contextid' => backup::VAR_CONTEXTID,
                                                      'itemid' => backup::VAR_PARENTID,
                                                      'component' => backup_helper::is_sqlparam('mod_margic'),
                                                      'ratingarea' => backup_helper::is_sqlparam('entry'), ]);

            $rating->set_source_alias('rating', 'value');
        }

        // Define id annotations.
        $margic->annotate_ids('scale', 'scale');
        $rating->annotate_ids('scale', 'scaleid');
        $rating->annotate_ids('user', 'userid');

        if ($userinfo) {
            $entry->annotate_ids('user', 'userid');
            $entry->annotate_ids('user', 'teacher');
            $annotation->annotate_ids('user', 'userid');
        }

        // Define file annotations.
        // component, filearea, elementname.
        $margic->annotate_files('mod_margic', 'intro', null); // This file area has no itemid.
        $entry->annotate_files('mod_margic', 'entry', 'id');
        $entry->annotate_files('mod_margic', 'feedback', 'id');

        return $this->prepare_activity_structure($margic);
    }
}
