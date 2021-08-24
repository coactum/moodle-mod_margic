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
 * Administration settings definitions for the annotateddiary module.
 *
 * @package   mod_annotateddiary
 * @copyright 2019 AL Rachels (drachels@drachels.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    // Availability settings.
    $settings->add(new admin_setting_heading('mod_annotateddiary/availibility', get_string('availability'), ''));

    $settings->add(new admin_setting_configselect('annotateddiary/showrecentactivity',
        get_string('showrecentactivity', 'annotateddiary'),
        get_string('showrecentactivity', 'annotateddiary'), 1, array(
        '0' => get_string('no'),
        '1' => get_string('yes')
    )));

    $settings->add(new admin_setting_configselect('annotateddiary/overview',
        get_string('showoverview', 'annotateddiary'),
        get_string('showoverview', 'annotateddiary'), 1, array(
        '0' => get_string('no'),
        '1' => get_string('yes')
    )));

    // 20201015 Default edit all entries setting.
    $settings->add(new admin_setting_configselect('annotateddiary/editall',
        get_string('editall', 'annotateddiary'),
        get_string('editall_help', 'annotateddiary'), 1, array(
        '0' => get_string('no'),
        '1' => get_string('yes')
    )));

    // 20201119 Default edit the date of any entry setting.
    $settings->add(new admin_setting_configselect('annotateddiary/editdates',
        get_string('editdates', 'annotateddiary'),
        get_string('editdates_help', 'annotateddiary'), 1, array(
        '0' => get_string('no'),
        '1' => get_string('yes')
    )));

    // Appearance settings.
    $settings->add(new admin_setting_heading('mod_annotateddiary/appearance',
        get_string('appearance'), ''));

    // Date format setting.
    $settings->add(new admin_setting_configtext('mod_annotateddiary/dateformat',
        get_string('dateformat', 'annotateddiary'),
        get_string('configdateformat', 'annotateddiary'), 'M d, Y G:i', PARAM_TEXT, 15));

    // annotateddiary entry/feedback background colour setting.
    $name = 'mod_annotateddiary/entrybgc';
    $title = get_string('entrybgc_title', 'annotateddiary');
    $description = get_string('entrybgc_descr', 'annotateddiary');
    $default = get_string('entrybgc_colour', 'annotateddiary');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // annotateddiary entry text background colour setting.
    $name = 'mod_annotateddiary/entrytextbgc';
    $title = get_string('entrytextbgc_title', 'annotateddiary');
    $description = get_string('entrytextbgc_descr', 'annotateddiary');
    $default = get_string('entrytextbgc_colour', 'annotateddiary');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
}