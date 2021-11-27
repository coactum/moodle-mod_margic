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
 * margic stats utilities for margic.
 *
 * 2020071700 Moved these functions from lib.php to here.
 *
 * @package   mod_margic
 * @copyright AL Rachels (drachels@drachels.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_margic\local;

defined('MOODLE_INTERNAL') || die();

use mod_margic\local\margicstats;
use stdClass;
use core_text;

/**
 * Utility class for margic stats.
 *
 * @package   mod_margic
 * @copyright AL Rachels (drachels@drachels.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class margicstats {

    /**
     * Update the margic statistics for this margic activity.
     *
     * @param string $entry The text for this entry.
     * @return bool
     */
    public static function get_margic_stats($entry) {
    // @codingStandardsIgnoreLine
    // public function get_margic_stats($entry) {
        // global $DB, $CFG;
        $precision = 1;
        $margicstats = array();
        $margicstats['words'] = self::get_stats_words($entry);
        $margicstats['chars'] = self::get_stats_chars($entry);
        $margicstats['sentences'] = self::get_stats_sentences($entry);
        $margicstats['paragraphs'] = self::get_stats_paragraphs($entry);
        $margicstats['uniquewords'] = self::get_stats_uniquewords($entry);
        // @codingStandardsIgnoreLine
        // print_object('This is the $margicstats array.');
        // print_object($margicstats);

        return $margicstats;
    }

    /**
     * Update the margic character count statistics for this margic activity.
     *
     * @param string $entry The text for this entry.
     * @ return int The number of characters.
     */
    public static function get_stats_chars($entry) {
        return core_text::strlen($entry);
        // @codingStandardsIgnoreLine
        // return strlen($entry);
    }

    /**
     * Update the margic word count statistics for this margic activity.
     *
     * @param string $entry The text for this entry.
     * @ return int The number of words.
     */
    public static function get_stats_words($entry) {
        return count_words($entry);
    }

    /**
     * Update the margic sentence count statistics for this margic activity.
     *
     * @param string $entry The text for this entry.
     * @ return int The number of sentences.
     */
    public static function get_stats_sentences($entry) {
        $items = preg_split('/[!?.]+(?![0-9])/', $entry);
        $items = array_filter($items);
        return count($items);
    }

    /**
     * Update the margic paragraph count statistics for this margic activity.
     *
     * @param string $entry The text for this entry.
     * @ return int The number of paragraphs.
     */
    public static function get_stats_paragraphs($entry) {
        $items = explode("\n", $entry);
        $items = array_filter($items);
        return count($items);
    }

    /**
     * Update the margic unique word count statistics for this margic activity.
     *
     * @param string $entry The text for this entry.
     * @return int The number of unique words.
     */
    public static function get_stats_uniquewords($entry) {
        $items = core_text::strtolower($entry);
        // @codingStandardsIgnoreLine
        // $items = strtolower($entry);
        $items = str_word_count($items, 1);
        $items = array_unique($items);
        return count($items);
    }
}