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
 * All the steps to restore mod_margic are defined here.
 *
 * @package     mod_margic
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Defines the structure step to restore one mod_margic activity.
 */
class restore_margic_activity_structure_step extends restore_activity_structure_step {

    /** @var newmargicid Store id of new margic. */
    protected $newmargicid = false;

    /**
     * Defines the structure to be restored.
     *
     * @return restore_path_element[].
     */
    protected function define_structure() {
        $paths = array();

        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('margic', '/activity/margic');
        $paths[] = new restore_path_element('margic_errortype', '/activity/margic/errortypes/errortype');

        if ($userinfo) {
            $paths[] = new restore_path_element('margic_entry', '/activity/margic/entries/entry');
            $paths[] = new restore_path_element('margic_entry_rating', '/activity/margic/entries/entry/ratings/rating');
            $paths[] = new restore_path_element('margic_entry_annotation', '/activity/margic/entries/entry/annotations/annotation');
        }

        return $this->prepare_activity_structure($paths);
    }

    /**
     * Restore margic.
     *
     * @param object $data data.
     */
    protected function process_margic($data) {
        global $DB;

        $data = (object) $data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
        // See MDL-9367.
        if (!isset($data->assesstimestart)) {
            $data->assesstimestart = 0;
        }
        $data->assesstimestart = $this->apply_date_offset($data->assesstimestart);

        if (!isset($data->assesstimefinish)) {
            $data->assesstimefinish = 0;
        }
        $data->assesstimefinish = $this->apply_date_offset($data->assesstimefinish);

        if (!isset($data->timeopen)) {
            $data->timeopen = 0;
        }
        $data->timeopen = $this->apply_date_offset($data->timeopen);

        if (!isset($data->timeclose)) {
            $data->timeclose = 0;
        }
        $data->timeclose = $this->apply_date_offset($data->timeclose);

        if ($data->scale < 0) { // Scale found, get mapping.
            $data->scale = - ($this->get_mappingid('scale', abs($data->scale)));
        }

        $newitemid = $DB->insert_record('margic', $data);
        $this->apply_activity_instance($newitemid);
        $this->newmargicid = $newitemid;
    }

    /**
     * Restore margic entry.
     *
     * @param object $data data.
     */
    protected function process_margic_entry($data) {
        global $DB;

        $data = (object) $data;
        $oldid = $data->id;

        $data->margic = $this->get_new_parentid('margic');
        $data->userid = $this->get_mappingid('user', $data->userid);

        if ($data->baseentry !== null && $this->get_mappingid('margic_entry', $data->baseentry) !== null) {
            $data->baseentry = $this->get_mappingid('margic_entry', $data->baseentry);
        } else {
            $data->baseentry = null;
        }

        $newitemid = $DB->insert_record('margic_entries', $data);
        $this->set_mapping('margic_entry', $oldid, $newitemid);
    }

    /**
     * Restore margic errortype.
     *
     * @param object $data data.
     */
    protected function process_margic_errortype($data) {
        global $DB;

        $data = (object) $data;
        $oldid = $data->id;

        $data->margic = $this->get_new_parentid('margic');

        $newitemid = $DB->insert_record('margic_errortypes', $data);
        $this->set_mapping('margic_errortype', $oldid, $newitemid);
    }

    /**
     * Add annotations to restored margic entries.
     *
     * @param stdClass $data Tag
     */
    protected function process_margic_entry_annotation($data) {
        global $DB;

        $data = (object) $data;

        $oldid = $data->id;

        $data->margic = $this->newmargicid;
        $data->entry = $this->get_new_parentid('margic_entry');
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->type = $this->get_mappingid('margic_errortype', $data->type);

        $newitemid = $DB->insert_record('margic_annotations', $data);
        $this->set_mapping('margic_annotation', $oldid, $newitemid);
    }

    /**
     * Process margic entries to provide a rating restore.
     *
     * @param object $data The data in object form.
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
     * Defines post-execution actions like restoring files.
     */
    protected function after_execute() {
        // Add margic related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_margic', 'intro', null);

        $this->add_related_files('mod_margic', 'text', 'margic_entry');

        $this->add_related_files('mod_margic', 'feedback', 'margic_entry');
    }
}
