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
 * Administration settings definitions for the margic module.
 *
 * @package   mod_margic
 * @copyright 2022 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    // Editability settings.
    $settings->add(new admin_setting_heading('margic/editability', get_string('editability', 'margic'), ''));

    // Edit all own entries.
    $settings->add(new admin_setting_configselect('margic/editentries',
        get_string('editentries', 'margic'),
        get_string('editentries_help', 'margic'), 1, array(
        '0' => get_string('no'),
        '1' => get_string('yes')
    )));

    // Change the date of any new entry.
    $settings->add(new admin_setting_configselect('margic/editentrydates',
        get_string('editentrydates', 'margic'),
        get_string('editentrydates_help', 'margic'), 1, array(
        '0' => get_string('no'),
        '1' => get_string('yes')
    )));

    // Appearance settings.
    $settings->add(new admin_setting_heading('margic/appearance', get_string('appearance'), ''));

    // Default width of annotation area.
    $settings->add(new admin_setting_configtext('margic/annotationareawidth', get_string('annotationareawidth', 'margic'),
        get_string('annotationareawidthall', 'margic'), 40, '/^([2-7]\d|80)+$/')); // Range allowed: 20-80.

    // Background color of entry and annotation area.
    $name = 'margic/entrybgc';
    $title = get_string('entrybgc_title', 'margic');
    $description = get_string('entrybgc_descr', 'margic');
    $default = get_string('entrybgc_colour', 'margic');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Background color of texts.
    $name = 'margic/textbgc';
    $title = get_string('textbgc_title', 'margic');
    $description = get_string('textbgc_descr', 'margic');
    $default = get_string('textbgc_colour', 'margic');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
}
