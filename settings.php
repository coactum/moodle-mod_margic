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

    // Availability settings.
    $settings->add(new admin_setting_heading('mod_margic/availibility', get_string('availability'), ''));

    $settings->add(new admin_setting_configselect('margic/showrecentactivity',
        get_string('showrecentactivity', 'margic'),
        get_string('showrecentactivity', 'margic'), 1, array(
        '0' => get_string('no'),
        '1' => get_string('yes')
    )));

    $settings->add(new admin_setting_configselect('margic/overview',
        get_string('showoverview', 'margic'),
        get_string('showoverview', 'margic'), 1, array(
        '0' => get_string('no'),
        '1' => get_string('yes')
    )));

    // 20201015 Default edit all entries setting.
    $settings->add(new admin_setting_configselect('margic/editall',
        get_string('editall', 'margic'),
        get_string('editall_help', 'margic'), 1, array(
        '0' => get_string('no'),
        '1' => get_string('yes')
    )));

    // 20201119 Default edit the date of any entry setting.
    $settings->add(new admin_setting_configselect('margic/editdates',
        get_string('editdates', 'margic'),
        get_string('editdates_help', 'margic'), 1, array(
        '0' => get_string('no'),
        '1' => get_string('yes')
    )));

    // Appearance settings.
    $settings->add(new admin_setting_heading('mod_margic/appearance',
        get_string('appearance'), ''));

    // Default width of annotation area.
    $settings->add(new admin_setting_configtext('mod_margic/annotationareawidth', get_string('annotationareawidth', 'margic'),
        get_string('annotationareawidthall', 'margic'), 40, '/^([2-7]\d|80)+$/')); // Range allowed: 20-80

    // Date format setting.
    $settings->add(new admin_setting_configtext('mod_margic/dateformat',
        get_string('dateformat', 'margic'),
        get_string('configdateformat', 'margic'), 'M d, Y G:i', PARAM_TEXT, 15));

    // margic entry/feedback background colour setting.
    $name = 'mod_margic/entrybgc';
    $title = get_string('entrybgc_title', 'margic');
    $description = get_string('entrybgc_descr', 'margic');
    $default = get_string('entrybgc_colour', 'margic');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // margic entry text background colour setting.
    $name = 'mod_margic/entrytextbgc';
    $title = get_string('entrytextbgc_title', 'margic');
    $description = get_string('entrytextbgc_descr', 'margic');
    $default = get_string('entrytextbgc_colour', 'margic');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
}
