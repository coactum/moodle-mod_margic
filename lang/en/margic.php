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
 * English strings for margic plugin.
 *
 * @package   mod_margic
 * @category  string
 * @copyright 2021 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$string['eventmargiccreated'] = 'margic created';
$string['eventmargicviewed'] = 'margic viewed';
$string['evententriesviewed'] = 'margic entries viewed';
$string['eventmargicdeleted'] = 'margic deleted';
$string['eventdownloadentriess'] = 'Download entries';
$string['evententryupdated'] = 'margic entry updated';
$string['evententrycreated'] = 'margic entry created';
$string['eventfeedbackupdated'] = 'margic feedback updated';
$string['eventinvalidentryattempt'] = 'margic invalid entry attempt';

$string['accessdenied'] = 'Access denied';
$string['alwaysopen'] = 'Always open';
$string['alias'] = 'Keyword';
$string['aliases'] = 'Keyword(s)';
$string['aliases_help'] = 'Each margic entry can have an associated list of keywords (or aliases).

Enter each keyword on a new line (not separated by commas).';
$string['and'] = ' and ';
$string['attachment'] = 'Attachment';
$string['attachment_help'] = 'You can optionally attach one or more files to a margic entry.';
$string['blankentry'] = 'Blank entry';
$string['calendarend'] = '{$a} closes';
$string['calendarstart'] = '{$a} opens';
$string['configdateformat'] = 'This defines how dates are shown in margic reports. The default value, "M d, Y G:i" is Month, day, year and 24 hour format time. Refer to Date in the PHP manual for more examples and predefined date constants.';
$string['created'] = 'Created {$a->days} days and {$a->hours} hours ago.';
$string['csvexport'] = 'Export to .csv';
$string['daysavailable'] = 'Days available';
$string['daysavailable_help'] = 'If using Weekly format, you can set how many days the margic is open for use.';
$string['deadline'] = 'Days Open';
$string['dateformat'] = 'Default date format';
$string['details'] = 'Details';
$string['margicclosetime'] = 'Close time';
$string['margicclosetime_help'] = 'If enabled, you can set a date for the margic to be closed and no longer open for use.';
$string['margicentrydate'] = 'Set date for this entry';
$string['margicopentime'] = 'Open time';
$string['margicopentime_help'] = 'If enabled, you can set a date for the margic to be opened for use.';
$string['editall'] = 'Edit all entries';
$string['editall_help'] = 'When enabled, users can edit any entry.';
$string['editdates'] = 'Edit entry dates';
$string['editdates_help'] = 'When enabled, users can edit the date of any entry.';
$string['editingended'] = 'Editing period has ended';
$string['editingends'] = 'Editing period ends';
$string['editthisentry'] = 'Edit this entry';
$string['entries'] = 'Entries';
$string['entry'] = 'Entry';
$string['entrybgc_title'] = 'margic entry/feedback background color';
$string['entrybgc_descr'] = 'This sets the background color of a margic entry/feedback.';
$string['entrybgc_colour'] = '#C8E5FD';
$string['entrycomment'] = 'Entry comment';
$string['entrytextbgc_title'] = 'margic text background color';
$string['entrytextbgc_descr'] = 'This sets the background color of the text in a margic entry.';
$string['entrytextbgc_colour'] = '#F9F5F0';
$string['exportfilenamemyentries'] = 'My_Margic_Entries';
$string['exportfilenamemargicentries'] = 'Margic_Entries';
$string['exportfilenameallentries'] = 'All_Margic_Entries';
$string['feedbackupdated'] = 'Feedback updated for {$a} entries';
$string['gradeingradebook'] = 'Current rating in gradebook';
$string['margic:addentries'] = 'Add margic entries';
$string['margic:addinstance'] = 'Add margic instances';
$string['margic:manageentries'] = 'Manage margic entries';
$string['margic:rate'] = 'Rate margic entries';
$string['margicmail'] = 'Greetings {$a->user},
{$a->teacher} has posted some feedback on your margic entry for \'{$a->margic}\'.

You can see it appended to your margic entry:

    {$a->url}';
$string['margicmailhtml'] = 'Greetings {$a->user},
{$a->teacher} has posted some feedback on your
margic entry for \'<i>{$a->margic}</i>\'.<br /><br />
You can see it appended to your <a href="{$a->url}">margic entry</a>.';
$string['margicname'] = 'Name of the margic';
$string['margicdescription'] = 'Description of the margic';
$string['format'] = 'Format';
$string['generalerror'] = 'There has been an error.';
$string['generalerrorupdate'] = 'Could not update your margic.';
$string['generalerrorinsert'] = 'Could not insert a new margic entry.';
$string['incorrectcourseid'] = 'Course ID is incorrect';
$string['incorrectmodule'] = 'Course Module ID was incorrect';
$string['invalidaccess'] = 'Invalid access';
$string['invalidtimechange'] = 'An invalid attempt to change this entry\'s, Time created, has been detected. ';
$string['invalidtimechangeoriginal'] = 'The original time was: {$a->one}. ';
$string['invalidtimechangenewtime'] = 'The changed time was: {$a->one}. ';
$string['invalidtimeresettime'] = 'The time was reset to the original time of: {$a->one}.';

$string['lastnameasc'] = 'Last name ascending:';
$string['lastnamedesc'] = 'Last name descending:';
$string['mailed'] = 'Mailed';
$string['mailsubject'] = 'margic feedback';
$string['modulename'] = 'Margic';
$string['modulename_help'] = 'The margic activity enables teachers to obtain students feedback
 over a period of time.';
$string['modulenameplural'] = 'Margics';
$string['needsgrading'] = ' This entry has not been given feedback or a rating yet.';
$string['needsregrading'] = 'This entry has changed since feedback or a rating was given.';
$string['newmargicentries'] = 'New margic entries';
$string['nextentry'] = 'Next entry';
$string['nodeadline'] = 'Always open';
$string['noentriesmanagers'] = 'There are no teachers';
$string['noentry'] = 'No entry';
$string['notopenuntil'] = 'This margic won\'t be open until';

$string['notstarted'] = 'You have not started this margic yet';
$string['overallrating'] = 'Overall rating';
$string['pagesize'] = 'Entries per page';
$string['pluginadministration'] = 'margic module administration';
$string['pluginname'] = 'margic';
$string['previousentry'] = 'Previous entry';
$string['rate'] = 'Rate';
$string['rating'] = 'Rating for this entry';
$string['removeentries'] = 'Remove all entries';
$string['removemessages'] = 'Remove all margic entries';
$string['reportsingle'] = 'Get all margic entries for this user.';
$string['reportsingleallentries'] = 'All margic entries for this user.';
$string['returnto'] = 'Return to {$a}';
$string['returntoreport'] = 'Return to report page for - {$a}';
$string['saveallfeedback'] = 'Save all my feedback';
$string['savesettings'] = 'Save settings';
$string['search'] = 'Search';
$string['search:entry'] = 'margic - entries';
$string['search:entrycomment'] = 'margic - entry comment';
$string['search:activity'] = 'margic - activity information';
$string['selectentry'] = 'Select entry for marking';
$string['showrecentactivity'] = 'Show recent activity';
$string['showoverview'] = 'Show margics overview on my moodle';
$string['sortorder'] = 'Sort order is: ';
$string['sortcurrententry'] = 'From current margic entry to the first entry.';
$string['sortlowestentry'] = 'From lowest rated margic entry to the highest entry.';
$string['sorthighestentry'] = 'From highest rated margic entry to the lowest rated entry.';
$string['sortlastentry'] = 'From latest modified margic entry to the oldest modified entry.';
$string['sortoptions'] = ' Sort options: ';
$string['startnewentry'] = 'Start new entry';
$string['teacher'] = 'Teacher';
$string['text'] = 'Text';
$string['timecreated'] = 'Time created';
$string['timemarked'] = 'Time graded';
$string['timemodified'] = 'Time modified';
$string['toolbar'] = 'Toolbar:';
$string['userid'] = 'User id';
$string['usertoolbar'] = 'User toolbar:';
$string['viewallentries'] = 'View {$a} margic entries';
$string['viewentries'] = 'View entries';

$string['margic:viewannotations'] = 'View annotations';
$string['margic:makeannotations'] = 'Make annotations';
$string['annotations'] = 'Annotations';
$string['viewannotations'] = 'View annotations';
$string['hideannotations'] = 'Hide annotations';
$string['annotationadded'] = 'Annotation added';
$string['annotationedited'] = 'Annotation edited';
$string['annotationdeleted'] = 'Annotation deleted';
$string['annotationinvalid'] = 'Annotation invalid';
$string['noentriesfound'] = 'No entries found';
$string['lastedited'] = 'Last edited';
$string['getallentriesofuser'] = 'Get all margic entries for this user';
$string['myentries'] = 'My entries';
$string['numwordsraw'] = '{$a->wordscount} text words using {$a->charscount} characters, including {$a->spacescount} spaces. ';
$string['forallentries'] = 'for all entries of';
$string['forallmyentries'] = 'for all of my entries';
$string['toggleratingform'] = 'Open/close rating form';
$string['norating'] = 'Rating disabled.';
$string['viewallmargics'] = 'View all margics in course';
$string['startoreditentry'] = 'Add or edit entry';
$string['editentrynotpossible'] = 'You can not edit this entry.';
$string['entrydateinfuture'] = 'Entry date can not be in the future.';
$string['currenttooldest'] = 'Show entries from current to oldest';
$string['oldesttocurrent'] = 'Show entries from oldest to current';
$string['lowestgradetohighest'] = 'Show entries from the lowest rated to the highest one';
$string['highestgradetolowest'] = 'Show entries from the highest rated to the lowest one';
$string['latestmodified'] = 'Show the last modified entries';
$string['sorting'] = 'Sorting';
$string['currententry'] = 'Current entries';
$string['oldestentry'] = 'Oldest entries';
$string['lowestgradeentry'] = 'Lowest rated entries';
$string['highestgradeentry'] = 'Highest rated entries';
$string['latestmodifiedentry'] = 'Last modified entries';
$string['viewallentries'] = 'View all entries';

$string['grammar_verb'] = 'Grammar: Verb form';
$string['grammar_syntax'] = 'Grammar: Syntax';
$string['grammar_congruence'] = 'Grammar: Congruence';
$string['grammar_other'] = 'Grammar: Other';
$string['expression'] = 'Expression';
$string['orthography'] = 'Orthography';
$string['punctuation'] = 'Punctuation';
$string['other'] = 'Other';

// Löschen.
$string['numwordscln'] = '{$a->one} clean text words using {$a->two} characters, NOT including {$a->three} spaces. ';
$string['numwordsstd'] = '{$a->one} standardized words using {$a->two} characters, including {$a->three} spaces. ';
$string['numwordsnew'] = 'New calculation: {$a->one} raw text words using {$a->two} characters, in {$a->three} sentences, in {$a->four} paragraphs. ';
$string['edittopoflist'] = 'Edit top of the list';
$string['reload'] = 'Reload and show from current to oldest margic entry';
$string['sortfirstentry'] = 'From first margic entry to the latest entry.';
$string['outof'] = ' out of {$a} entries.';
