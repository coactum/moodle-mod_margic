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
    if ($oldversion < 2023030700) {
        $table = new xmldb_table('margic_annotations');
        $field = new xmldb_field('end', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $field2 = new xmldb_field('start', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'annotationend');
        }
        if ($dbman->field_exists($table, $field2)) {
            $dbman->rename_field($table, $field2, 'annotationstart');
        }
        upgrade_mod_savepoint(true, 2023030700, 'margic');
    }

    if ($oldversion < 2023100300) { // Added column for default value for send grading messages.
        $table = new xmldb_table('margic');
        $field = new xmldb_field('sendgradingmessage', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'editentrydates');

        // Conditionally launch add field for table.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('overwriteannotations', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0',
            'annotationareawidth');

        // Conditionally launch add field for table.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2023100300, 'margic');
    }

    return true;
}
