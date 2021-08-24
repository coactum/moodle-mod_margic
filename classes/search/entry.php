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
 * annotateddiary entries search.
 *
 * @package   mod_annotateddiary
 * @copyright 2019 AL Rachels (drachels@drachels.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_annotateddiary\search;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/annotateddiary/lib.php');
require_once($CFG->dirroot . '/lib/grouplib.php');

/**
 * annotateddiary entries search.
 *
 * @package   mod_annotateddiary
 * @copyright 2019 AL Rachels (drachels@drachels.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class entry extends \core_search\base_mod {

    /**
     *
     * @var array Internal quick static cache.
     */
    protected $entriesdata = array();

    /**
     * Returns recordset containing required data for indexing annotateddiary entries.
     *
     * @param int $modifiedfrom timestamp
     * @param \context|null $context Optional context to restrict scope of returned results
     * @return moodle_recordset|null Recordset (or null if no results)
     */
    public function get_document_recordset($modifiedfrom = 0, \context $context = null) {
        global $DB;

        list ($contextjoin, $contextparams) = $this->get_context_restriction_sql($context, 'annotateddiary', 'd', SQL_PARAMS_NAMED);
        if ($contextjoin === null) {
            return null;
        }

        $sql = "SELECT de.*, d.course
                  FROM {annotateddiary_entries} de
                  JOIN {annotateddiary} d ON d.id = de.annotateddiary
          $contextjoin
                 WHERE de.timemodified >= :timemodified
              ORDER BY de.timemodified ASC";
        return $DB->get_recordset_sql($sql, array_merge($contextparams, [
            'timemodified' => $modifiedfrom
        ]));
    }

    /**
     * Returns the documents associated with this annotateddiary entry id.
     *
     * @param stdClass $entry annotateddiary entry.
     * @param array $options
     * @return \core_search\document
     */
    public function get_document($entry, $options = array()) {
        try {
            $cm = $this->get_cm('annotateddiary', $entry->annotateddiary, $entry->course);
            $context = \context_module::instance($cm->id);
        } catch (\dml_missing_record_exception $ex) {
            // Notify it as we run here as admin, we should see everything.
            debugging('Error retrieving mod_annotateddiary '.$entry->id.' document, not all required data is available: '
                .$ex->getMessage(), DEBUG_DEVELOPER);
            return false;
        } catch (\dml_exception $ex) {
            // Notify it as we run here as admin, we should see everything.
            debugging('Error retrieving mod_annotateddiary' . $entry->id . ' document: ' . $ex->getMessage(), DEBUG_DEVELOPER);
            return false;
        }

        // Prepare associative array with data from DB.
        $doc = \core_search\document_factory::instance($entry->id, $this->componentname, $this->areaname);
        // I am using the entry date (timecreated) for the title.
        $doc->set('title', content_to_text((date(get_config('mod_annotateddiary', 'dateformat'), $entry->timecreated)), $entry->format));
        $doc->set('content', content_to_text('Entry: ' . $entry->text, $entry->format));
        $doc->set('contextid', $context->id);
        $doc->set('courseid', $entry->course);
        $doc->set('userid', $entry->userid);
        $doc->set('owneruserid', \core_search\manager::NO_OWNER_ID);
        $doc->set('modified', $entry->timemodified);
        $doc->set('description1', content_to_text('Feedback: ' . $entry->entrycomment, $entry->format));

        // Check if this document should be considered new.
        if (isset($options['lastindexedtime']) && ($options['lastindexedtime'] < $entry->timemodified)) {
            // If the document was created after the last index time, it must be new.
            $doc->set_is_new(true);
        }
        return $doc;
    }

    /**
     * Whether the user can access the document or not.
     *
     * @throws \dml_missing_record_exception
     * @throws \dml_exception
     * @param int $id annotateddiary entry id
     * @return bool
     */
    public function check_access($id) {
        global $USER;

        try {
            $entry = $this->get_entry($id);
            $cminfo = $this->get_cm('annotateddiary', $entry->annotateddiary, $entry->course);
        } catch (\dml_missing_record_exception $ex) {
            return \core_search\manager::ACCESS_DELETED;
        } catch (\dml_exception $ex) {
            return \core_search\manager::ACCESS_DENIED;
        }

        if (! $cminfo->uservisible) {
            return \core_search\manager::ACCESS_DENIED;
        }

        if ($entry->userid != $USER->id && ! has_capability('mod/annotateddiary:manageentries', $cminfo->context)) {
            return \core_search\manager::ACCESS_DENIED;
        }

        return \core_search\manager::ACCESS_GRANTED;
    }

    /**
     * Link to annotateddiary entry.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_doc_url(\core_search\document $doc) {
        global $USER;

        $contextmodule = \context::instance_by_id($doc->get('contextid'));

        $entryuserid = $doc->get('userid');
        if ($entryuserid == $USER->id) {
            $url = '/mod/annotateddiary/view.php';
        } else {
            // Teachers see student's entries in the report page.
            $url = '/mod/annotateddiary/report.php#entry-' . $entryuserid;
        }
        return new \moodle_url($url, array(
            'id' => $contextmodule->instanceid
        ));
    }

    /**
     * Link to the annotateddiary.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_context_url(\core_search\document $doc) {
        $contextmodule = \context::instance_by_id($doc->get('contextid'));
        return new \moodle_url('/mod/annotateddiary/view.php', array(
            'id' => $contextmodule->instanceid
        ));
    }

    /**
     * Returns the specified annotateddiary entry checking the internal cache.
     *
     * Store minimal information as this might grow.
     *
     * @throws \dml_exception
     * @param int $entryid
     * @return stdClass
     */
    protected function get_entry($entryid) {
        global $DB;
        return $DB->get_record_sql("SELECT de.*, d.course FROM {annotateddiary_entries} de
                                      JOIN {annotateddiary} d ON d.id = de.annotateddiary
                                     WHERE de.id = ?", array('id' => $entryid), MUST_EXIST);
    }
}
