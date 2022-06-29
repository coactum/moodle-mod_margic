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
 * Privacy subsystem implementation for margic.
 *
 * @package   mod_margic
 * @copyright 2022 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_margic\privacy;

use core_privacy\local\request\userlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\writer;
use core_privacy\local\request\helper;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\transform;
use core_privacy\local\request\contextlist;

use core_privacy\local\request\user_preference_provider;

use core_grades\component_gradeitem as gradeitem; // needed?

defined('MOODLE_INTERNAL') || die(); // needed?

/**
 * Privacy class for requesting user data.
 *
 * @package   mod_margic
 * @copyright 2022 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\provider,
                          \core_privacy\local\request\core_userlist_provider,
                          \core_privacy\local\request\plugin\provider {

    /**
     * Provides the meta data stored for usera stored by mod_margic.
     *
     * @param collection $items The initialized collection to add items to.
     * @return collection Returns the collection of metadata.
     */
    public static function get_metadata(collection $items): collection {
        error_log('PRIVACY API: get_metadata');

        // The table 'margic_entries' stores the user entries saved in all margics.
        $items->add_database_table('margic_entries', [
            'margic' => 'privacy:metadata:margic_entries:margic',
            'userid' => 'privacy:metadata:margic_entries:userid',
            'timecreated' => 'privacy:metadata:margic_entries:timecreated',
            'timemodified' => 'privacy:metadata:margic_entries:timemodified',
            'text' => 'privacy:metadata:margic_entries:text',
            'rating' => 'privacy:metadata:margic_entries:rating',
            'entrycomment' => 'privacy:metadata:margic_entries:entrycomment'
        ], 'privacy:metadata:margic_entries');

        // The table 'margic_annotations' stores the annotations made in all margics.
        $items->add_database_table('margic_annotations', [
            'margic' => 'privacy:metadata:margic_annotations:margic',
            'entry' => 'privacy:metadata:margic_annotations:entry',
            'userid' => 'privacy:metadata:margic_annotations:userid',
            'timecreated' => 'privacy:metadata:margic_annotations:timecreated',
            'timemodified' => 'privacy:metadata:margic_annotations:timemodified',
            'type' => 'privacy:metadata:margic_annotations:type',
            'text' => 'privacy:metadata:margic_annotations:text',
        ], 'privacy:metadata:margic_annotations');

        // The table 'margic_annotation_types' stores the annotation types of all margics.
        $items->add_database_table('margic_annotation_types', [
            'userid' => 'privacy:metadata:margic_annotation_types:userid',
            'timecreated' => 'privacy:metadata:margic_annotation_types:timecreated',
            'timemodified' => 'privacy:metadata:margic_annotation_types:timemodified',
            'name' => 'privacy:metadata:margic_annotation_types:name',
            'color' => 'privacy:metadata:margic_annotation_types:color',
        ], 'privacy:metadata:margic_annotation_types');

        // The margic uses the grading subsystem that saves personal data.
        $items->add_subsystem_link('core_files', [], 'privacy:metadata:core_files');
        $items->add_subsystem_link('core_rating', [], 'privacy:metadata:core_rating');


        // User preferences in the margic.
        $items->add_user_preference('sortoption', 'privacy:metadata:preference:sortoption');
        $items->add_user_preference('margic_pagecount', 'privacy:metadata:preference:margic_pagecount');
        $items->add_user_preference('margic_activepage', 'privacy:metadata:preference:margic_activepage');

        return $items;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        error_log('MARGIC PRIVACY API: get_contexts_for_userid for user');
        error_log(var_export($userid, true));

        $params = [
            'modulename' => 'margic',
            'contextlevel' => CONTEXT_MODULE,
            'userid' => $userid
        ];

        // Get contexts for entries.
        $sql = "SELECT c.id
                    FROM {context} c
                    JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                    JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                    JOIN {margic} ma ON ma.id = cm.instance
                    JOIN {margic_entries} e ON e.margic = ma.id
                    WHERE e.userid = :userid
        ";

        $contextlist->add_from_sql($sql, $params);

        // Get contexts for annotations.
        $sql = "SELECT c.id
                    FROM {context} c
                    JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                    JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                    JOIN {margic} ma ON ma.id = cm.instance
                    JOIN {margic_annotations} a ON a.margic = ma.id
                    WHERE a.userid = :userid
         ";

        $contextlist->add_from_sql($sql, $params);

        // Annotationtypes have no specific contexts.

        error_log(var_export($contextlist, true));

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        error_log('PRIVACY API: get_users_in_context');

        if (! is_a($context, \context_module::class)) {
            return;
        }

        $params = [
            'instanceid'    => $context->id,
            'modulename'    => 'margic',
        ];

        // Find users with margic entries.
        $sql = "SELECT e.userid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {margic} ma ON ma.id = cm.instance
                  JOIN {margic_entries} e ON e.margic = ma.id
                 WHERE cm.id = :instanceid
        ";

        $userlist->add_from_sql('userid', $sql, $params);

        // Find users with annotations in margics.
        $sql = "SELECT a.userid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {margic} ma ON ma.id = cm.instance
                  JOIN {margic_annotations} a ON a.margic = ma.id
                 WHERE cm.id = :instanceid
        ";

        $userlist->add_from_sql('userid', $sql, $params);

        error_log(var_export($userlist, true));

    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        error_log('export_user_data');

        error_log(var_export($contextlist, true));

        if (! count($contextlist)) {
            return;
        }

        $user = $contextlist->get_user();
        $userid = $user->id;

        list ($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);
        $params = $contextparams;

        $sql = "SELECT
                c.id AS contextid,
                m.*,
                cm.id AS cmid
            FROM {context} c
            JOIN {course_modules} cm ON cm.id = c.instanceid
            JOIN {margic} m ON m.id = cm.instance
            WHERE (
                c.id {$contextsql}
            )
        ";

        $margics = $DB->get_recordset_sql($sql, $params);

        if ($margics->valid()) {
            foreach ($margics as $margic) {

                error_log('Margic:');
                error_log(var_export($margic->id, true));

                if ($margic) {
                    $context = \context::instance_by_id($margic->contextid);

                    // Store the main margic data.
                    $contextdata = helper::get_context_data($context, $user);
                    // Write it.
                    writer::with_context($context)->export_data([], $contextdata);
                    // Write generic module intro files.
                    helper::export_context_files($context, $user);

                    self::export_entries_data($userid, $margic->id, $margic->contextid);

                    // export_annotations_data($userid, $mappings)
                }

            }
        }

        $margics->close();
    }

    /**
     * Store all information about all entries off this user.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     * @param   int         $margicid The id of the margic.
     * @param   int         $margiccontextid The context id of the margic.
     */
    protected static function export_entries_data(int $userid, $margicid, $margiccontextid) {
        global $DB;

        error_log('export_entries_data');

        error_log('margicid:');
        error_log(var_export($margicid, true));

        error_log('margiccontextid:');
        error_log(var_export($margiccontextid, true));


        // Find all entries for this margic written by the user.
        $sql = "SELECT
                    e.id,
                    e.userid,
                    e.timecreated,
                    e.timemodified,
                    e.text,
                    e.format,
                    e.rating,
                    e.entrycomment
                   FROM {margic_entries} e
                   WHERE (
                    e.margic = :margicid AND
                    e.userid = :userid
                    )
        ";

        $params['userid'] = $userid;
        $params['margicid'] = $margicid;

        // Get the margics from the entries.
        $entries = $DB->get_recordset_sql($sql, $params);

        // $discussions = $DB->get_recordset_sql($sql, $params);
        if ($entries->valid()) {
            foreach ($entries as $entry) {
                if ($entry) {
                    $context = \context::instance_by_id($margiccontextid);

                    // // Store related metadata.

                    // $metadata = (object) [
                    //     'name' => format_string($discussion->name, true),
                    //     'timemodified' => transform::datetime($discussion->timemodified),
                    //     'creator_was_you' => transform::yesno($discussion->userid == $userid),
                    // ];

                    error_log('ENTRY:');
                    error_log(var_export($entry->id, true));

                    // Store the entries content.
                    //writer::with_context($context)->export_data('test', $metadata);

                    self::export_entry_data($userid, $context, ['margic-entry-' . $entry->id], $entry);

                }
            }
        }

        $entries->close();
    }

    /**
     * Export all data in the entry.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     * @param   \context    $context The instance of the margic context.
     * @param   array       $subcontext The location within the current context that this data belongs.
     * @param   \stdClass   $entry The entry.
     */
    protected static function export_entry_data(int $userid, \context $context, $subcontext, $entry) {

        error_log('export_entry_data');

        error_log('context:');
        error_log(var_export($context, true));

        error_log('subcontext:');
        error_log(var_export($subcontext, true));

        error_log('entry:');
        error_log(var_export($entry, true));

        unset($entrydata);

        // Store related metadata.
        $entrydata = (object) [
            'userid' => $entry->userid,
            'timecreated' => transform::datetime($entry->timecreated),
            'timemodified' => transform::datetime($entry->timemodified),
            'rating' => $entry->rating,
            'entrycomment' => $entry->entrycomment,
        ];

        $entrydata->text = writer::with_context($context)->rewrite_pluginfile_urls($subcontext, 'mod_margic', 'entry', $entry->id, $entry->text);

        $entrydata->text = format_text($entrydata->text, $entry->format, (object) [
            'para'    => false,
            'context' => $context,
        ]);

        // Store the entry data.
        writer::with_context($context)
            ->export_data($subcontext, $entrydata)
            ->export_area_files($subcontext, 'mod_margic', 'entry', $entry->id);

        // Store all ratings against this entry as the entry belongs to the user. All ratings on it are ratings of their content.
        \core_rating\privacy\provider::export_area_ratings($userid, $context, $subcontext, 'mod_margic', 'entry', $entry->id, false);
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        // This should not happen, but just in case.
        if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }

        // Prepare SQL to gather all completed IDs.

        $completedsql = "
            SELECT fc.id
              FROM {%s} fc
              JOIN {modules} m
                ON m.name = :margic
              JOIN {course_modules} cm
                ON cm.instance = fc.margic
               AND cm.module = m.id
             WHERE cm.id = :cmid";
        $completedparams = [
            'cmid' => $context->instanceid,
            'margic' => 'margic'
        ];

        // Delete margic entries.
        $completedtmpids = $DB->get_fieldset_sql(sprintf($completedsql, 'margic_entries'), $completedparams);
        if (! empty($completedtmpids)) {
            list ($insql, $inparams) = $DB->get_in_or_equal($completedtmpids, SQL_PARAMS_NAMED);
            $DB->delete_records_select('margic_entries', "id $insql", $inparams);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        $userid = $contextlist->get_user()->id;

        // Ensure that we only act on module contexts.
        $contextids = array_map(function ($context) {
            return $context->instanceid;
        }, array_filter($contextlist->get_contexts(), function ($context) {
            return $context->contextlevel == CONTEXT_MODULE;
        }));

        // Prepare SQL to gather all completed IDs.
        list ($insql, $inparams) = $DB->get_in_or_equal($contextids, SQL_PARAMS_NAMED);
        $completedsql = "
            SELECT fc.id
              FROM {%s} fc
              JOIN {modules} m
                ON m.name = :margic
              JOIN {course_modules} cm
                ON cm.instance = fc.margic
               AND cm.module = m.id
             WHERE fc.userid = :userid
               AND cm.id $insql";
        $completedparams = array_merge($inparams, [
            'userid' => $userid,
            'margic' => 'margic'
        ]);

        // Delete margic entries.
        $completedtmpids = $DB->get_fieldset_sql(sprintf($completedsql, 'margic_entries'), $completedparams);
        if (! empty($completedtmpids)) {
            list ($insql, $inparams) = $DB->get_in_or_equal($completedtmpids, SQL_PARAMS_NAMED);
            $DB->delete_records_select('margic_entries', "id $insql", $inparams);
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();
        $userids = $userlist->get_userids();

        // Prepare SQL to gather all completed IDs.
        list ($insql, $inparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $completedsql = "
            SELECT fc.id
              FROM {%s} fc
              JOIN {modules} m
                ON m.name = :margic
              JOIN {course_modules} cm
                ON cm.instance = fc.margic
               AND cm.module = m.id
             WHERE cm.id = :instanceid
               AND fc.userid $insql";
        $completedparams = array_merge($inparams, [
            'instanceid' => $context->instanceid,
            'margic' => 'margic'
        ]);

        // Delete all margic entries.
        $completedtmpids = $DB->get_fieldset_sql(sprintf($completedsql, 'margic_entries'), $completedparams);
        if (! empty($completedtmpids)) {
            list ($insql, $inparams) = $DB->get_in_or_equal($completedtmpids, SQL_PARAMS_NAMED);
            $DB->delete_records_select('margic_entries', "id $insql", $inparams);
        }
    }
}
