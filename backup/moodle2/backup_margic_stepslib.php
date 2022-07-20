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
        $margic = new backup_nested_element('margic', array('id'), array(
            'name', 'intro', 'introformat', 'timecreated', 'timemodified',
            'scale', 'assessed', 'assesstimestart', 'assesstimefinish',
            'timeopen', 'timeclose', 'editall', 'editdates', 'annotationareawidth'));

        $entries = new backup_nested_element('entries');

        $entry = new backup_nested_element('entry', array('id'), array(
            'userid', 'timecreated', 'timemodified', 'text', 'format',
            'rating', 'entrycomment', 'formatcomment', 'teacher',
            'timemarked', 'mailed'));

        $annotations = new backup_nested_element('annotations');

        $annotation = new backup_nested_element('annotation', array('id'), array(
            'userid', 'timecreated', 'timemodified', 'type', 'startcontainer', 'endcontainer',
            'startposition', 'endposition', 'text'));

        $tags = new backup_nested_element('tags');
        $tag = new backup_nested_element('tag', array('id'), array('itemid', 'rawname'));

        $ratings = new backup_nested_element('ratings');
        $rating = new backup_nested_element('rating', array('id'), array(
            'component', 'ratingarea', 'scaleid', 'value', 'userid',
            'timecreated', 'timemodified'));

        // Build the tree with these elements with $margic as the root of the backup tree.
        $margic->add_child($entries);
        $entries->add_child($entry);

        $entry->add_child($annotations);
        $annotations->add_child($annotation);

        $entry->add_child($ratings);
        $ratings->add_child($rating);

        $margic->add_child($tags);
        $tags->add_child($tag);

        // Define the source tables for the elements.

        $margic->set_source_table('margic', array('id' => backup::VAR_ACTIVITYID));

        if ($userinfo) {

            // Entries.
            $entry->set_source_table('margic_entries', array('margic' => backup::VAR_PARENTID));

            // Annotations.
            $annotation->set_source_table('margic_annotations', array('entry' => backup::VAR_PARENTID));

            // Ratings (core).
            $rating->set_source_table('rating', array('contextid'  => backup::VAR_CONTEXTID,
                                                      'itemid'     => backup::VAR_PARENTID,
                                                      'component'  => backup_helper::is_sqlparam('mod_margic'),
                                                      'ratingarea' => backup_helper::is_sqlparam('entry')));

            $rating->set_source_alias('rating', 'value');

            // Tags (core).
            if (core_tag_tag::is_enabled('mod_margic', 'margic_entries')) {
                $tag->set_source_sql('SELECT t.id, ti.itemid, t.rawname
                                        FROM {tag} t
                                        JOIN {tag_instance} ti
                                          ON ti.tagid = t.id
                                       WHERE ti.itemtype = ?
                                         AND ti.component = ?
                                         AND ti.contextid = ?', array(
                    backup_helper::is_sqlparam('margic_entries'),
                    backup_helper::is_sqlparam('mod_margic'),
                    backup::VAR_CONTEXTID));
            }

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
        $margic->annotate_files('mod_margic', 'intro', null); // This file area has no itemid.
        $entry->annotate_files('mod_margic_entries', 'entry', 'id');
        $entry->annotate_files('mod_margic_entries', 'feedback', 'id');

        return $this->prepare_activity_structure($margic);
    }
}
