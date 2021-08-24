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
 * English strings for annotateddiary plugin.
 *
 * @package   mod_annotateddiary
 * @copyright 2019 AL Rachels (drachels@drachels.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$string['eventannotateddiarycreated'] = 'annotateddiary created';
$string['eventannotateddiaryviewed'] = 'annotateddiary viewed';
$string['evententriesviewed'] = 'annotateddiary entries viewed';
$string['eventannotateddiarydeleted'] = 'annotateddiary deleted';
$string['eventdownloadentriess'] = 'Download entries';
$string['evententryupdated'] = 'annotateddiary entry updated';
$string['evententrycreated'] = 'annotateddiary entry created';
$string['eventfeedbackupdated'] = 'annotateddiary feedback updated';
$string['eventinvalidentryattempt'] = 'annotateddiary invalid entry attempt';

$string['accessdenied'] = 'Access denied';
$string['alwaysopen'] = 'Always open';
$string['alias'] = 'Keyword';
$string['aliases'] = 'Keyword(s)';
$string['aliases_help'] = 'Each annotateddiary entry can have an associated list of keywords (or aliases).

Enter each keyword on a new line (not separated by commas).';
$string['and'] = ' and ';
$string['attachment'] = 'Attachment';
$string['attachment_help'] = 'You can optionally attach one or more files to a annotateddiary entry.';
$string['blankentry'] = 'Blank entry';
$string['calendarend'] = '{$a} closes';
$string['calendarstart'] = '{$a} opens';
$string['configdateformat'] = 'This defines how dates are shown in annotateddiary reports. The default value, "M d, Y G:i" is Month, day, year and 24 hour format time. Refer to Date in the PHP manual for more examples and predefined date constants.';
$string['created'] = 'Created {$a->one} days and {$a->two} hours ago.';
$string['csvexport'] = 'Export to .csv';
$string['currententry'] = 'Current annotateddiary entries:';
$string['daysavailable'] = 'Days available';
$string['daysavailable_help'] = 'If using Weekly format, you can set how many days the annotateddiary is open for use.';
$string['deadline'] = 'Days Open';
$string['dateformat'] = 'Default date format';
$string['details'] = 'Details: ';
$string['annotateddiaryclosetime'] = 'Close time';
$string['annotateddiaryclosetime_help'] = 'If enabled, you can set a date for the annotateddiary to be closed and no longer open for use.';
$string['annotateddiaryentrydate'] = 'Set date for this entry';
$string['annotateddiaryopentime'] = 'Open time';
$string['annotateddiaryopentime_help'] = 'If enabled, you can set a date for the annotateddiary to be opened for use.';
$string['edittopoflist'] = 'Edit top of the list';
$string['editall'] = 'Edit all entries';
$string['editall_help'] = 'When enabled, users can edit any entry.';
$string['editdates'] = 'Edit entry dates';
$string['editdates_help'] = 'When enabled, users can edit the date of any entry.';
$string['editingended'] = 'Editing period has ended';
$string['editingends'] = 'Editing period ends';
$string['editthisentry'] = 'Edit this entry';
$string['entries'] = 'Entries';
$string['entry'] = 'Entry';
$string['entrybgc_title'] = 'annotateddiary entry/feedback background color';
$string['entrybgc_descr'] = 'This sets the background color of a annotateddiary entry/feedback.';
$string['entrybgc_colour'] = '#93FC84';
$string['entrycomment'] = 'Entry comment';
$string['entrytextbgc_title'] = 'annotateddiary text background color';
$string['entrytextbgc_descr'] = 'This sets the background color of the text in a annotateddiary entry.';
$string['entrytextbgc_colour'] = '#EEFC84';
$string['exportfilename'] = 'entries.csv';
$string['exportfilenamep1'] = 'All_Site';
$string['exportfilenamep2'] = '_annotateddiary_Entries_Exported_On_';
$string['feedbackupdated'] = 'Feedback updated for {$a} entries';
$string['firstentry'] = 'First annotateddiary entries:';
$string['gradeingradebook'] = 'Current rating in gradebook';
$string['annotateddiary:addentries'] = 'Add annotateddiary entries';
$string['annotateddiary:addinstance'] = 'Add annotateddiary instances';
$string['annotateddiary:manageentries'] = 'Manage annotateddiary entries';
$string['annotateddiary:rate'] = 'Rate annotateddiary entries';
$string['annotateddiarymail'] = 'Greetings {$a->user},
{$a->teacher} has posted some feedback on your annotateddiary entry for \'{$a->annotateddiary}\'.

You can see it appended to your annotateddiary entry:

    {$a->url}';
$string['annotateddiarymailhtml'] = 'Greetings {$a->user},
{$a->teacher} has posted some feedback on your
annotateddiary entry for \'<i>{$a->annotateddiary}</i>\'.<br /><br />
You can see it appended to your <a href="{$a->url}">annotateddiary entry</a>.';
$string['annotateddiaryname'] = 'annotateddiary name';
$string['annotateddiarydescription'] = 'annotateddiary description';
$string['format'] = 'Format';
$string['generalerror'] = 'There has been an error.';
$string['generalerrorupdate'] = 'Could not update your annotateddiary.';
$string['generalerrorinsert'] = 'Could not insert a new annotateddiary entry.';
$string['highestgradeentry'] = 'Highest rated entries:';
$string['incorrectcourseid'] = 'Course ID is incorrect';
$string['incorrectmodule'] = 'Course Module ID was incorrect';
$string['invalidaccess'] = 'Invalid access';
$string['invalidaccessexp'] = 'You do not have permission to view the page you attempted to access! The attempt was logged!';
$string['invalidtimechange'] = 'An invalid attempt to change this entry\'s, Time created, has been detected. ';
$string['invalidtimechangeoriginal'] = 'The original time was: {$a->one}. ';
$string['invalidtimechangenewtime'] = 'The changed time was: {$a->one}. ';
$string['invalidtimeresettime'] = 'The time was reset to the original time of: {$a->one}.';

$string['lastnameasc'] = 'Last name ascending:';
$string['lastnamedesc'] = 'Last name descending:';
$string['latestmodifiedentry'] = 'Most recently modified entries:';
$string['lowestgradeentry'] = 'Lowest rated entries:';
$string['mailed'] = 'Mailed';
$string['mailsubject'] = 'annotateddiary feedback';
$string['modulename'] = 'Annotated annotateddiary';
$string['modulename_help'] = 'The annotateddiary activity enables teachers to obtain students feedback
 over a period of time.';
$string['modulenameplural'] = 'annotateddiarys';
$string['needsgrading'] = ' This entry has not been given feedback or rated yet.';
$string['needsregrade'] = 'This entry has changed since feedback or a rating was given.';
$string['newannotateddiaryentries'] = 'New annotateddiary entries';
$string['nextentry'] = 'Next entry';
$string['nodeadline'] = 'Always open';
$string['noentriesmanagers'] = 'There are no teachers';
$string['noentry'] = 'No entry';
$string['noratinggiven'] = 'No rating given';
$string['notopenuntil'] = 'This annotateddiary won\'t be open until';
$string['numwordsraw'] = '{$a->one} raw text words using  {$a->two} characters, including {$a->three} spaces. ';
$string['numwordscln'] = '{$a->one} clean text words using {$a->two} characters, NOT including {$a->three} spaces. ';
$string['numwordsstd'] = '{$a->one} standardized words using {$a->two} characters, including {$a->three} spaces. ';

$string['numwordsnew'] = 'New calculation: {$a->one} raw text words using {$a->two} characters, in {$a->three} sentences, in {$a->four} paragraphs. ';


$string['notstarted'] = 'You have not started this annotateddiary yet';
$string['outof'] = ' out of {$a} entries.';
$string['overallrating'] = 'Overall rating';
$string['pagesize'] = 'Entries per page';
$string['pluginadministration'] = 'annotateddiary module administration';
$string['pluginname'] = 'annotateddiary';
$string['previousentry'] = 'Previous entry';
$string['rate'] = 'Rate';
$string['rating'] = 'Rating for this entry';
$string['reload'] = 'Reload and show from current to oldest annotateddiary entry';
$string['removeentries'] = 'Remove all entries';
$string['removemessages'] = 'Remove all annotateddiary entries';
$string['reportsingle'] = 'Get all annotateddiary entries for this user.';
$string['reportsingleallentries'] = 'All annotateddiary entries for this user.';
$string['returnto'] = 'Return to {$a}';
$string['returntoreport'] = 'Return to report page for - {$a}';
$string['saveallfeedback'] = 'Save all my feedback';
$string['savesettings'] = 'Save settings';
$string['search'] = 'Search';
$string['search:entry'] = 'annotateddiary - entries';
$string['search:entrycomment'] = 'annotateddiary - entry comment';
$string['search:activity'] = 'annotateddiary - activity information';
$string['selectentry'] = 'Select entry for marking';
$string['showrecentactivity'] = 'Show recent activity';
$string['showoverview'] = 'Show annotateddiarys overview on my moodle';
$string['sortorder'] = 'Sort order is: ';
$string['sortcurrententry'] = 'From current annotateddiary entry to the first entry.';
$string['sortfirstentry'] = 'From first annotateddiary entry to the latest entry.';
$string['sortlowestentry'] = 'From lowest rated annotateddiary entry to the highest entry.';
$string['sorthighestentry'] = 'From highest rated annotateddiary entry to the lowest rated entry.';
$string['sortlastentry'] = 'From latest modified annotateddiary entry to the oldest modified entry.';
$string['sortoptions'] = ' Sort options: ';
$string['startnewentry'] = 'Start new entry';
$string['startoredit'] = 'Start new or edit today\'s entry';
$string['teacher'] = 'Teacher';
$string['text'] = 'Text';
$string['timecreated'] = 'Time created';
$string['timemarked'] = 'Time marked';
$string['timemodified'] = 'Time modified';
$string['toolbar'] = 'Toolbar:';
$string['userid'] = 'User id';
$string['usertoolbar'] = 'User toolbar:';
$string['viewalldiaries'] = 'View all course diaries';
$string['viewallentries'] = 'View {$a} annotateddiary entries';
$string['viewentries'] = 'View entries';
