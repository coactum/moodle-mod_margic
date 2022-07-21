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

        // The table 'margic_entries' stores the user entries saved in all margics.
        $items->add_database_table('margic_entries', [
            'margic' => 'privacy:metadata:margic_entries:margic',
            'userid' => 'privacy:metadata:margic_entries:userid',
            'timecreated' => 'privacy:metadata:margic_entries:timecreated',
            'timemodified' => 'privacy:metadata:margic_entries:timemodified',
            'text' => 'privacy:metadata:margic_entries:text',
            'rating' => 'privacy:metadata:margic_entries:rating',
            'entrycomment' => 'privacy:metadata:margic_entries:entrycomment',
            'teacher' => 'privacy:metadata:margic_entries:teacher',
            'timemarked' => 'privacy:metadata:margic_entries:timemarked',
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

        // The table 'margic_errortypes' stores the annotation types of all margics.
        $items->add_database_table('margic_errortypes', [
            'userid' => 'privacy:metadata:margic_errortypes:userid',
            'timecreated' => 'privacy:metadata:margic_errortypes:timecreated',
            'timemodified' => 'privacy:metadata:margic_errortypes:timemodified',
            'name' => 'privacy:metadata:margic_errortypes:name',
            'color' => 'privacy:metadata:margic_errortypes:color',
        ], 'privacy:metadata:margic_errortypes');

        // The margic uses multiple subsystems that save personal data.
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

        // TODO: Get errortypes for margic.

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

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
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

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

                if ($margic) {
                    $context = \context::instance_by_id($margic->contextid);

                    // Store the main margic data.
                    $contextdata = helper::get_context_data($context, $user);

                    // Write it.
                    writer::with_context($context)->export_data([], $contextdata);

                    // Todo: Store related metadata.

                    // Write generic module intro files.
                    helper::export_context_files($context, $user);

                    self::export_entries_data($userid, $margic->id, $margic->contextid);

                    self::export_annotations_data($userid, $margic->id, $margic->contextid);
                }

            }
        }

        $margics->close();
    }

    /**
     * Store all information about all entries made by this user.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     * @param   int         $margicid The id of the margic.
     * @param   int         $margiccontextid The context id of the margic.
     */
    protected static function export_entries_data(int $userid, $margicid, $margiccontextid) {
        global $DB;

        // Find all entries for this margic written by the user.
        $sql = "SELECT
                    e.id,
                    e.margic,
                    e.userid,
                    e.timecreated,
                    e.timemodified,
                    e.text,
                    e.format,
                    e.rating,
                    e.entrycomment,
                    e.formatcomment,
                    e.teacher,
                    e.timemarked
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

        if ($entries->valid()) {
            foreach ($entries as $entry) {
                if ($entry) {
                    $context = \context::instance_by_id($margiccontextid);

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

        // Store related metadata.
        $entrydata = (object) [
            'margic' => $entry->margic,
            'userid' => $entry->userid,
            'timecreated' => transform::datetime($entry->timecreated),
            'timemodified' => transform::datetime($entry->timemodified),
            'rating' => $entry->rating,
            'teacher' => $entry->teacher,
            'timemarked' => transform::datetime($entry->timemarked),
        ];

        $entrydata->text = writer::with_context($context)->rewrite_pluginfile_urls($subcontext, 'mod_margic', 'entry', $entry->id, $entry->text);

        $entrydata->text = format_text($entrydata->text, $entry->format, (object) [
            'para'    => false,
            'context' => $context,
        ]);

        $entrydata->entrycomment = writer::with_context($context)->rewrite_pluginfile_urls($subcontext, 'mod_margic', 'feedback', $entry->id, $entry->entrycomment);

        $entrydata->entrycomment = format_text($entrydata->entrycomment, $entry->formatcomment, (object) [
            'para'    => false,
            'context' => $context,
        ]);

        // Store the entry data.
        writer::with_context($context)
            ->export_data($subcontext, $entrydata)
            ->export_area_files($subcontext, 'mod_margic', 'entry', $entry->id)
            ->export_area_files($subcontext, 'mod_margic', 'feedback', $entry->id);

        // Store all ratings against this entry as the entry belongs to the user. All ratings on it are ratings of their content.
        \core_rating\privacy\provider::export_area_ratings($userid, $context, $subcontext, 'mod_margic', 'entry', $entry->id, false);
    }

    /**
     * Store all information about all annotations made by this user.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     * @param   int         $margicid The id of the margic.
     * @param   int         $margiccontextid The context id of the margic.
     */
    protected static function export_annotations_data(int $userid, $margicid, $margiccontextid) {
        global $DB;

        // Find all annotations for this margic made by the user.
        $sql = "SELECT
                    a.id,
                    a.margic,
                    a.entry,
                    a.userid,
                    a.timecreated,
                    a.timemodified,
                    a.type,
                    a.text
                   FROM {margic_annotations} a
                   WHERE (
                    a.margic = :margicid AND
                    a.userid = :userid
                    )
        ";

        $params['userid'] = $userid;
        $params['margicid'] = $margicid;

        // Get the margics from the annotations.
        $annotations = $DB->get_recordset_sql($sql, $params);

        if ($annotations->valid()) {
            foreach ($annotations as $annotation) {
                if ($annotation) {
                    $context = \context::instance_by_id($margiccontextid);

                    self::export_annotation_data($userid, $context, ['margic-annotation-' . $annotation->id], $annotation);
                }
            }
        }

        $annotations->close();
    }

    /**
     * Export all data of the annotation.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     * @param   \context    $context The instance of the margic context.
     * @param   array       $subcontext The location within the current context that this data belongs.
     * @param   \stdClass   $annotation The annotation.
     */
    protected static function export_annotation_data(int $userid, \context $context, $subcontext, $annotation) {

        // Store related metadata.
        $annotationdata = (object) [
            'margic' => $annotation->margic,
            'entry' => $annotation->entry,
            'userid' => $annotation->userid,
            'timecreated' => transform::datetime($annotation->timecreated),
            'timemodified' => transform::datetime($annotation->timemodified),
            'type' => $annotation->type,
            'text' => format_text($annotation->text, 2, array('para' => false)),
        ];

        // Store the annotation data.
        writer::with_context($context)->export_data($subcontext, $annotationdata);
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        // Check that this is a context_module.
        if (!$context instanceof \context_module) {
            return;
        }

        // Get the course module.
        if (!$cm = get_coursemodule_from_id('margic', $context->instanceid)) {
            return;
        }

        // Delete advanced grading information.
        /* $gradingmanager = get_grading_manager($context, 'mod_margic', 'margic');
        $controller = $gradingmanager->get_active_controller();

        if (isset($controller)) {
            \core_grading\privacy\provider::delete_instance_data($context);
        } */

        // Delete all ratings in the context.
        \core_rating\privacy\provider::delete_ratings($context, 'mod_margic', 'entry');

        // Delete all files from the entry.
        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'mod_margic', 'entry');
        $fs->delete_area_files($context->id, 'mod_margic', 'feedback');

        // Delete all entries.
        if ($DB->record_exists('margic_entries', ['margic' => $cm->instance])) {
            $DB->delete_records('margic_entries', ['margic' => $cm->instance]);
        }

        // Delete all annotations.
        if ($DB->record_exists('margic_annotations', ['margic' => $cm->instance])) {
            $DB->delete_records('margic_annotations', ['margic' => $cm->instance]);
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

        foreach ($contextlist->get_contexts() as $context) {
            // Get the course module.
            $cm = $DB->get_record('course_modules', ['id' => $context->instanceid]);

            // Handle any advanced grading method data first.
            /* $grades = $DB->get_records('margic_entries', ['margic' => $cm->instance, 'userid' => $userid]);
            $gradingmanager = get_grading_manager($context, 'margic_entries', 'margic');
            $controller = $gradingmanager->get_active_controller();
            foreach ($grades as $grade) {
                // Delete advanced grading information.
                if (isset($controller)) {
                    \core_grading\privacy\provider::delete_instance_data($context, $grade->id);
                }
            } */

            // Delete ratings.
            $entriessql = "SELECT
                                e.id
                                FROM {margic_entries} e
                                WHERE (
                                    e.margic = :margicid AND
                                    e.userid = :userid
                                )
            ";

            $entriesparams = [
                'margicid' => $cm->instance,
                'userid' => $userid,
            ];

            \core_rating\privacy\provider::delete_ratings_select($context, 'mod_margic', 'entry', "IN ($entriessql)", $entriesparams);

            // Delete all files from the entries.
            $fs = get_file_storage();
            $fs->delete_area_files_select($context->id, 'mod_margic', 'entry', "IN ($entriessql)", $entriesparams);
            $fs->delete_area_files_select($context->id, 'mod_margic', 'feedback', "IN ($entriessql)", $entriesparams);

            $entriesselect = "entry IN (SELECT id FROM {margic_entries} e WHERE e.margic = :margicid AND e.userid = :userid)";

            // Delete annotations for user entries that should be deleted.
            if ($DB->record_exists_select('margic_annotations', $entriesselect, $entriesparams)) {
                $DB->delete_records_select('margic_annotations', $entriesselect, $entriesparams);
            }

            // Delete entries for user.
            if ($DB->record_exists('margic_entries', ['margic' => $cm->instance, 'userid' => $userid])) {

                $DB->delete_records('margic_entries', [
                    'margic' => $cm->instance,
                    'userid' => $userid,
                ]);

            }

            // Delete annotations for user.
            if ($DB->record_exists('margic_annotations', ['margic' => $cm->instance, 'userid' => $userid])) {

                $DB->delete_records('margic_annotations', [
                    'margic' => $cm->instance,
                    'userid' => $userid,
                ]);

            }

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
        $cm = $DB->get_record('course_modules', ['id' => $context->instanceid]);

        list($userinsql, $userinparams) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
        $params = array_merge(['margicid' => $cm->instance], $userinparams);

        // Handle any advanced grading method data first.
        /* $grades = $DB->get_records('margic_entries', ['margic' => $cm->instance, 'userid' => $userid]);
        $gradingmanager = get_grading_manager($context, 'margic_entries', 'margic');
        $controller = $gradingmanager->get_active_controller();
        foreach ($grades as $grade) {
            // Delete advanced grading information.
            if (isset($controller)) {
                \core_grading\privacy\provider::delete_instance_data($context, $grade->id);
            }
        } */

        // Delete ratings.
        $entriesselect = "SELECT
                            e.id
                            FROM {margic_entries} e
                            WHERE (
                                e.margic = :margicid AND
                                userid {$userinsql}
                            )
        ";

        \core_rating\privacy\provider::delete_ratings_select($context, 'mod_margic', 'entry', "IN ($entriesselect)", $params);

        // Delete all files from the entries.
        $fs = get_file_storage();
        $fs->delete_area_files_select($context->id, 'mod_margic', 'entry', "IN ($entriesselect)", $params);
        $fs->delete_area_files_select($context->id, 'mod_margic', 'feedback', "IN ($entriesselect)", $params);

        // Delete annotations for users entries that should be deleted.
        if ($DB->record_exists_select('margic_annotations', "entry IN ({$entriesselect})", $params)) {
            $DB->delete_records_select('margic_annotations', "entry IN ({$entriesselect})", $params);
        }

        // Delete entries for users.
        if ($DB->record_exists_select('margic_entries', "margic = :margicid AND userid {$userinsql}", $params)) {
            $DB->delete_records_select('margic_entries', "margic = :margicid AND userid {$userinsql}", $params);
        }

        // Delete annotations for users.
        if ($DB->record_exists_select('margic_annotations', "margic = :margicid AND userid {$userinsql}", $params)) {
            $DB->delete_records_select('margic_annotations', "margic = :margicid AND userid {$userinsql}", $params);
        }
    }
}
