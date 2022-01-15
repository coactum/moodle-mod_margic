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
 * Stats utilities for margic entries.
 *
 * @package   mod_margic
 * @copyright 2021 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_margic\local;

use stdClass;
use core_text;

/**
 * Utility class for margic entry stats.
 *
 * @package   mod_margic
 * @copyright 2021 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class entrystats {

    /**
     * Get the statistics for this margic entry.
     *
     * @param string $entrytext The text for this entry.
     * @param string $entrytimecreated The time then the entry was created.
     * @return array entrystats Array with the statistics of the entry.
     */
    public static function get_entry_stats($entrytext, $entrytimecreated) {

        $cleantext = preg_replace('#<[^>]+>#', ' ', $entrytext, -1, $replacementspacescount);

        $entrystats = array();
        $entrystats['words'] = self::get_stats_words($cleantext);
        $entrystats['chars'] = self::get_stats_chars($cleantext) - $replacementspacescount;
        $entrystats['sentences'] = self::get_stats_sentences($cleantext);
        $entrystats['paragraphs'] = self::get_stats_paragraphs($cleantext);
        $entrystats['uniquewords'] = self::get_stats_uniquewords($cleantext);
        $entrystats['spaces'] = self::get_stats_spaces($cleantext) - $replacementspacescount;
        $entrystats['charswithoutspaces'] = $entrystats['chars'] - $entrystats['spaces'];
        $entrystats['datediff'] = date_diff(new \DateTime(date('Y-m-d G:i:s', time())), new \DateTime(date('Y-m-d G:i:s', $entrytimecreated)));
        return $entrystats;
    }

    /**
     * Get the character count statistics for this margic entry.
     *
     * @param string $entrytext The text for this entry.
     * @ return int The number of characters.
     */
    public static function get_stats_chars($entrytext) {
        return core_text::strlen($entrytext);
    }

    /**
     * Get the word count statistics for this margic entry.
     *
     * @param string $entrytext The text for this entry.
     * @ return int The number of words.
     */
    public static function get_stats_words($entrytext) {
        return count_words($entrytext);
    }

    /**
     * Get the sentence count statistics for this margic entry.
     *
     * @param string $entrytext The text for this entry.
     * @ return int The number of sentences.
     */
    public static function get_stats_sentences($entrytext) {
        $sentences = preg_split('/[!?.]+(?![0-9])/', $entrytext);
        $sentences = array_filter($sentences);
        return count($sentences);
    }

    /**
     * Get the paragraph count statistics for this margic entry.
     *
     * @param string $entrytext The text for this entry.
     * @ return int The number of paragraphs.
     */
    public static function get_stats_paragraphs($entrytext) {
        $paragraphs = explode("\n", $entrytext);
        $paragraphs = array_filter($paragraphs);
        return count($paragraphs);
    }

    /**
     * Get the unique word count statistics for this margic entry.
     *
     * @param string $entrytext The text for this entry.
     * @return int The number of unique words.
     */
    public static function get_stats_uniquewords($entrytext) {
        $items = core_text::strtolower($entrytext);
        $items = str_word_count($items, 1);
        $items = array_unique($items);
        return count($items);
    }

    /**
     * Get the raw spaces count statistics for this margic entry.
     *
     * @param string $entrytext The text for this entry.
     * @return int The number of spaces.
     */
    public static function get_stats_spaces($entrytext) {
        return substr_count($entrytext, ' ');
    }
}
