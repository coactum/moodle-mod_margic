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
 * Plugin upgrade steps are defined here.
 *
 * @package   mod_margic
 * @copyright 2022 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade this margic instance from the given old version.
 *
 * @param int $oldversion The old version of the margic module
 * @return bool
 */
function xmldb_margic_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2022070400) {

        // Add the formatcomment field to the margic_entries table.
        $table = new xmldb_table('margic_entries');
        $field = new xmldb_field('formatcomment', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1', 'entrycomment');

        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Margic savepoint reached.
        upgrade_mod_savepoint(true, 2022070400, 'margic');

    }

    if ($oldversion < 2022071801) {

        // Add the annotationareawidth field to the margic table.
        $table = new xmldb_table('margic');
        $field = new xmldb_field('annotationareawidth', XMLDB_TYPE_INTEGER, '3', null, null, null, null, 'editdates');

        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Margic savepoint reached.
        upgrade_mod_savepoint(true, 2022071801, 'margic');

    }

    if ($oldversion < 2022072100) {

        $table = new xmldb_table('margic_errortype_templates');

        // Adding fields to table margic_errortypes_templates.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('color', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('defaulttype', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table margic_errortypes_templates.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table margic_errortypes_templates.
        $table->add_index('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));

        // Conditionally launch create table for margic_errortypes_templates.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Add the priority field to the table margic_errortypes.
        $table = new xmldb_table('margic_annotation_types');
        $field = new xmldb_field('priority', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'userid');

        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        if ($dbman->table_exists($table)) {
            $dbman->rename_table($table, 'margic_errortypes');
        }

        // Margic savepoint reached.
        upgrade_mod_savepoint(true, 2022072100, 'margic');
    }

    return true;
}
