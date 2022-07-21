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
 * Code to be executed after the plugin's database scheme has been installed is defined here.
 *
 * @package     mod_margic
 * @category    upgrade
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Custom code to be run on installing the plugin.
 */
function xmldb_margic_install() {

    global $DB;

    $errortype = new stdClass();
    $errortype->id = 1;
    $errortype->timecreated = time();
    $errortype->timemodified = 0;
    $errortype->name = 'grammar_verb';
    $errortype->color = 'FFFF00';
    $errortype->defaulttype = 1;
    $errortype->userid = 0;

    $DB->insert_record('margic_errortype_templates', $errortype);

    $errortype = new stdClass();
    $errortype->id = 2;
    $errortype->timecreated = time();
    $errortype->timemodified = 0;
    $errortype->name = 'grammar_syntax';
    $errortype->color = 'FFFF00';
    $errortype->defaulttype = 1;
    $errortype->userid = 0;

    $DB->insert_record('margic_errortype_templates', $errortype);

    $errortype = new stdClass();
    $errortype->id = 3;
    $errortype->timecreated = time();
    $errortype->timemodified = 0;
    $errortype->name = 'grammar_congruence';
    $errortype->color = 'FFFF00';
    $errortype->defaulttype = 1;
    $errortype->userid = 0;

    $DB->insert_record('margic_errortype_templates', $errortype);

    $errortype = new stdClass();
    $errortype->id = 4;
    $errortype->timecreated = time();
    $errortype->timemodified = 0;
    $errortype->name = 'grammar_other';
    $errortype->color = 'FFFF00';
    $errortype->defaulttype = 1;
    $errortype->userid = 0;

    $DB->insert_record('margic_errortype_templates', $errortype);

    $errortype = new stdClass();
    $errortype->id = 5;
    $errortype->timecreated = time();
    $errortype->timemodified = 0;
    $errortype->name = 'expression';
    $errortype->color = 'FFFF00';
    $errortype->defaulttype = 1;
    $errortype->userid = 0;

    $DB->insert_record('margic_errortype_templates', $errortype);

    $errortype = new stdClass();
    $errortype->id = 6;
    $errortype->timecreated = time();
    $errortype->timemodified = 0;
    $errortype->name = 'orthography';
    $errortype->color = 'FFFF00';
    $errortype->defaulttype = 1;
    $errortype->userid = 0;

    $DB->insert_record('margic_errortype_templates', $errortype);

    $errortype = new stdClass();
    $errortype->id = 7;
    $errortype->timecreated = time();
    $errortype->timemodified = 0;
    $errortype->name = 'punctuation';
    $errortype->color = 'FFFF00';
    $errortype->defaulttype = 1;
    $errortype->userid = 0;

    $DB->insert_record('margic_errortype_templates', $errortype);

    $errortype = new stdClass();
    $errortype->id = 8;
    $errortype->timecreated = time();
    $errortype->timemodified = 0;
    $errortype->name = 'other';
    $errortype->color = 'FFFF00';
    $errortype->defaulttype = 1;
    $errortype->userid = 0;

    $DB->insert_record('margic_errortype_templates', $errortype);

    return true;
}
