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
 * Define all the restore steps that will be used by the restore_margic_activity_task
 *
 * @package   mod_margic
 * @copyright 2022 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use mod_margic\local\results;

/**
 * Define the complete margic structure for restore, with file and id annotations.
 *
 * @package   mod_margic
 * @copyright 2022 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_margic_activity_structure_step extends restore_activity_structure_step {

    /**
     * Define the structure of the restore workflow.
     *
     * @return restore_path_element $structure
     */
    protected function define_structure() {
        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('margic', '/activity/margic');

        if ($userinfo) {
            $paths[] = new restore_path_element('margic_entry', '/activity/margic/entries/entry');
            $paths[] = new restore_path_element('margic_entry_rating', '/activity/margic/entries/entry/ratings/rating');
            $paths[] = new restore_path_element('margic_entry_tag', '/activity/margic/entriestags/tag');
        }

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process a margic restore.
     *
     * @param object $margic
     *            The margic in object form
     * @return void
     */
    protected function process_margic($margic) {
        global $DB;

        $margic = (object) $margic;
        $oldid = $margic->id;
        $margic->course = $this->get_courseid();

        unset($margic->id);

        // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
        // See MDL-9367.
        $margic->course = $this->get_courseid();
        $margic->assesstimestart = $this->apply_date_offset($margic->assesstimestart);
        $margic->assesstimefinish = $this->apply_date_offset($margic->assesstimefinish);
        $margic->timemodified = $this->apply_date_offset($margic->timemodified);
        $margic->timeopen = $this->apply_date_offset($margic->timeopen);
        $margic->timeclose = $this->apply_date_offset($margic->timeclose);

        if ($margic->scale < 0) { // Scale found, get mapping.
            $margic->scale = - ($this->get_mappingid('scale', abs($margic->scale)));
        }

        // Insert the data record.
        $newid = $DB->insert_record('margic', $margic);
        $this->apply_activity_instance($newid);
    }

    /**
     * Process a margicentry restore.
     *
     * @param object $margicentry
     *            The margicentry in object form.
     * @return void
     */
    protected function process_margic_entry($margicentry) {
        global $DB;

        $margicentry = (object) $margicentry;

        $oldid = $margicentry->id;
        unset($margicentry->id);

        $margicentry->margic = $this->get_new_parentid('margic');
        $margicentry->timemcreated = $this->apply_date_offset($margicentry->timecreated);
        $margicentry->timemodified = $this->apply_date_offset($margicentry->timemodified);
        $margicentry->timemarked = $this->apply_date_offset($margicentry->timemarked);
        $margicentry->userid = $this->get_mappingid('user', $margicentry->userid);

        $newid = $DB->insert_record('margic_entries', $margicentry);
        $this->set_mapping('margic_entry', $oldid, $newid);
    }

    /**
     * Add tags to restored entries.
     *
     * @param stdClass $data
     *            Tag
     */
    protected function process_margic_entry_tag($data) {
        $data = (object) $data;

        if (! core_tag_tag::is_enabled('mod_margic', 'margic_entries')) { // Tags disabled in server, nothing to process.
            return;
        }

        if (! $itemid = $this->get_mappingid('margic_entries', $data->itemid)) {
            // Some orphaned tag, we could not find the data record for it - ignore.
            return;
        }

        $tag = $data->rawname;
        $context = context_module::instance($this->task->get_moduleid());
        core_tag_tag::add_item_tag('mod_margic', 'margic_entries', $itemid, $context, $tag);
    }

    /**
     * Process margic entries to provide a rating restore.
     *
     * @param object $data
     *            The data in object form.
     * @return void
     */
    protected function process_margic_entry_rating($data) {
        global $DB;

        $data = (object) $data;

        // Cannot use ratings API, cause, it's missing the ability to specify times (modified/created).
        $data->contextid = $this->task->get_contextid();
        $data->itemid = $this->get_new_parentid('margic_entry');
        if ($data->scaleid < 0) { // Scale found, get mapping.
            $data->scaleid = - ($this->get_mappingid('scale', abs($data->scaleid)));
        }
        $data->rating = $data->value;
        $data->userid = $this->get_mappingid('user', $data->userid);

        // We need to check that component and ratingarea are both set here.
        if (empty($data->component)) {
            $data->component = 'mod_margic';
        }
        if (empty($data->ratingarea)) {
            $data->ratingarea = 'entry';
        }

        $newitemid = $DB->insert_record('rating', $data);
    }

    /**
     * Once the database tables have been fully restored, restore the files
     *
     * @return void
     */
    protected function after_execute() {
        $this->add_related_files('mod_margic', 'intro', null);
        $this->add_related_files('mod_margic_entries', 'text', null);
        $this->add_related_files('mod_margic_entries', 'entrycomment', null);
    }
}
