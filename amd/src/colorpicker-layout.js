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
 * Module for layouting custom color picker element as default form element.
 *
 * @module     mod_discourse/colorpicker-layout
 * @copyright  2023 coactum GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';

export const init = (colorpickerid) => {
    $('.path-mod-margic .mform fieldset #fitem_' + colorpickerid).addClass('row');
    $('.path-mod-margic .mform fieldset #fitem_' + colorpickerid).addClass('form-group');

    $('.path-mod-margic .mform fieldset .fitemtitle').addClass('col-md-3');
    $('.path-mod-margic .mform fieldset .fitemtitle').addClass('col-form-label');
    $('.path-mod-margic .mform fieldset .fitemtitle').addClass('d-flex');

    $('.path-mod-margic .mform fieldset .ftext').addClass('col-md-9');

};