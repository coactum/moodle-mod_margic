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
 * Color picker custom form element.
 *
 * @package   mod_margic
 * @copyright 2023 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("HTML/QuickForm/text.php");

/**
 * Color picker custom form element.
 *
 * @package   mod_margic
 * @copyright 2023 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_margic_colorpicker_form_element extends HTML_QuickForm_text {
    /** @var forceltr Whether to force the display of this element to flow LTR. */
    public $forceltr = false;

    /** @var _helpbutton String html for help button, if empty then no help. */
    public $_helpbutton = '';

    /** @var _hiddenlabel If true label will be hidden. */
    public $_hiddenlabel = false;

    /**
     * Constructor.
     *
     * @param string $elementname (optional) name of the text field
     * @param string $elementlabel (optional) text field label
     * @param string $attributes (optional) Either a typical HTML attribute string or an associative array
     */
    public function __construct($elementname = null, $elementlabel = null, $attributes = null) {
        parent::__construct($elementname, $elementlabel, $attributes);
        parent::setSize(30);
        parent::setMaxlength(7);
    }

    /**
     * Sets label to be hidden.
     *
     * @param bool $hiddenlabel sets if label should be hidden
     */
    public function sethiddenlabel($hiddenlabel) {
        $this->_hiddenlabel = $hiddenlabel;
    }

    /**
     * Freeze the element so that only its value is returned and set persistantfreeze to false.
     *
     * @return    void
     */
    public function freeze() {
        $this->_flagFrozen = true;
        // No hidden element is needed refer MDL-30845.
        $this->setPersistantFreeze(false);
    }

    /**
     * Returns the html to be used when the element is frozen.
     *
     * @return    string Frozen html
     */
    public function getfrozenhtml() {
        $attributes = array('readonly' => 'readonly');
        $this->updateAttributes($attributes);
        return $this->_getTabs() . '<input' . $this->_getAttrString($this->_attributes) . '/>' . $this->_getPersistantData();
    }

    /**
     * Returns HTML for this form element.
     *
     * @return string
     */
    public function tohtml() {
        global $CFG, $PAGE;

        $PAGE->requires->js_init_call('M.util.init_colour_picker', array('id_color', null));
        $PAGE->requires->js_call_amd('mod_margic/colorpicker-layout', 'init', array('id_color'));

        // Add the class at the last minute.
        if ($this->get_force_ltr()) {
            if (!isset($this->_attributes['class'])) {
                $this->_attributes['class'] = 'form-control text-ltr';
            } else {
                $this->_attributes['class'] .= 'form-control text-ltr';
            }
        }

        $this->_generateId();
        if ($this->_flagFrozen) {
            return $this->getfrozenhtml();
        }

        $html = $this->_getTabs() .
                '<div class="pb-2" id="admin-annotationtype_color">
                    <div class="form-setting">
                        <div class="form-colourpicker defaultsnext">
                            <div class="admin_colourpicker clearfix">
                            </div>
                            <input ' . $this->_getAttrString($this->_attributes) . '">
                            <div id="id_error_color" class="form-control-feedback invalid-feedback"></div>
                        </div>
                    </div>
                </div>';

        if ($this->_hiddenlabel) {
            return '<label class="accesshide" for="'.$this->getAttribute('id').'" >'.
                        $this->getLabel() . '</label>' . $html;
        } else {
             return $html;
        }
    }

    /**
     * Get html for help button.
     *
     * @return string html for help button
     */
    public function gethelpbutton() {
        return $this->_helpbutton;
    }

    /**
     * Get force LTR option.
     *
     * @return bool
     */
    public function get_force_ltr() {
        return $this->forceltr;
    }

    /**
     * Force the field to flow left-to-right.
     *
     * This is useful for fields such as URLs, passwords, settings, etc...
     *
     * @param bool $value The value to set the option to.
     */
    public function set_force_ltr($value) {
        $this->forceltr = (bool) $value;
    }
}
