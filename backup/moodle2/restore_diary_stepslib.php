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
 * Define all the restore steps that will be used by the restore_annotateddiary_activity_task
 *
 * @package   mod_annotateddiary
 * @copyright 2020 AL Rachels <drachels@drachels.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use mod_annotateddiary\local\results;
defined('MOODLE_INTERNAL') || die();

/**
 * Define the complete annotateddiary structure for restore, with file and id annotations.
 *
 * @package   mod_annotateddiary
 * @copyright 2020 AL Rachels <drachels@drachels.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_annotateddiary_activity_structure_step extends restore_activity_structure_step {

    /**
     * Define the structure of the restore workflow.
     *
     * @return restore_path_element $structure
     */
    protected function define_structure() {
        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('annotateddiary', '/activity/annotateddiary');

        if ($userinfo) {
            $paths[] = new restore_path_element('annotateddiary_entry', '/activity/annotateddiary/entries/entry');
            $paths[] = new restore_path_element('annotateddiary_entry_rating', '/activity/annotateddiary/entries/entry/ratings/rating');
            $paths[] = new restore_path_element('annotateddiary_entry_tag', '/activity/annotateddiary/entriestags/tag');
        }

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process a annotateddiary restore.
     *
     * @param object $annotateddiary
     *            The annotateddiary in object form
     * @return void
     */
    protected function process_annotateddiary($annotateddiary) {
        global $DB;

        $annotateddiary = (object) $annotateddiary;
        $oldid = $annotateddiary->id;
        $annotateddiary->course = $this->get_courseid();

        unset($annotateddiary->id);

        // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
        // See MDL-9367.
        $annotateddiary->course = $this->get_courseid();
        $annotateddiary->assesstimestart = $this->apply_date_offset($annotateddiary->assesstimestart);
        $annotateddiary->assesstimefinish = $this->apply_date_offset($annotateddiary->assesstimefinish);
        $annotateddiary->timemodified = $this->apply_date_offset($annotateddiary->timemodified);
        $annotateddiary->timeopen = $this->apply_date_offset($annotateddiary->timeopen);
        $annotateddiary->timeclose = $this->apply_date_offset($annotateddiary->timeclose);

        if ($annotateddiary->scale < 0) { // Scale found, get mapping.
            $annotateddiary->scale = - ($this->get_mappingid('scale', abs($annotateddiary->scale)));
        }

        // Insert the data record.
        $newid = $DB->insert_record('annotateddiary', $annotateddiary);
        $this->apply_activity_instance($newid);
    }

    /**
     * Process a annotateddiaryentry restore.
     *
     * @param object $annotateddiaryentry
     *            The annotateddiaryentry in object form.
     * @return void
     */
    protected function process_annotateddiary_entry($annotateddiaryentry) {
        global $DB;

        $annotateddiaryentry = (object) $annotateddiaryentry;

        $oldid = $annotateddiaryentry->id;
        unset($annotateddiaryentry->id);

        $annotateddiaryentry->annotateddiary = $this->get_new_parentid('annotateddiary');
        $annotateddiaryentry->timemcreated = $this->apply_date_offset($annotateddiaryentry->timecreated);
        $annotateddiaryentry->timemodified = $this->apply_date_offset($annotateddiaryentry->timemodified);
        $annotateddiaryentry->timemarked = $this->apply_date_offset($annotateddiaryentry->timemarked);
        $annotateddiaryentry->userid = $this->get_mappingid('user', $annotateddiaryentry->userid);

        $newid = $DB->insert_record('annotateddiary_entries', $annotateddiaryentry);
        $this->set_mapping('annotateddiary_entry', $oldid, $newid);
    }

    /**
     * Add tags to restored entries.
     *
     * @param stdClass $data
     *            Tag
     */
    protected function process_annotateddiary_entry_tag($data) {
        $data = (object) $data;

        if (! core_tag_tag::is_enabled('mod_annotateddiary', 'annotateddiary_entries')) { // Tags disabled in server, nothing to process.
            return;
        }

        if (! $itemid = $this->get_mappingid('annotateddiary_entries', $data->itemid)) {
            // Some orphaned tag, we could not find the data record for it - ignore.
            return;
        }

        $tag = $data->rawname;
        $context = context_module::instance($this->task->get_moduleid());
        core_tag_tag::add_item_tag('mod_annotateddiary', 'annotateddiary_entries', $itemid, $context, $tag);
    }

    /**
     * Process annotateddiary entries to provide a rating restore.
     *
     * @param object $data
     *            The data in object form.
     * @return void
     */
    protected function process_annotateddiary_entry_rating($data) {
        global $DB;

        $data = (object) $data;

        // Cannot use ratings API, cause, it's missing the ability to specify times (modified/created).
        $data->contextid = $this->task->get_contextid();
        $data->itemid = $this->get_new_parentid('annotateddiary_entry');
        if ($data->scaleid < 0) { // Scale found, get mapping.
            $data->scaleid = - ($this->get_mappingid('scale', abs($data->scaleid)));
        }
        $data->rating = $data->value;
        $data->userid = $this->get_mappingid('user', $data->userid);

        // We need to check that component and ratingarea are both set here.
        if (empty($data->component)) {
            $data->component = 'mod_annotateddiary';
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
        $this->add_related_files('mod_annotateddiary', 'intro', null);
        $this->add_related_files('mod_annotateddiary_entries', 'text', null);
        $this->add_related_files('mod_annotateddiary_entries', 'entrycomment', null);
    }
}
