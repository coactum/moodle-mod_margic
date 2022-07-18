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

    return true;
}
