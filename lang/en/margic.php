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
 * @copyright 2022 coactum GmbH
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
$string['eventfeedbackupdated'] = 'Margic feedback updated';
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
$string['editall'] = 'Edit own entries';
$string['editall_help'] = 'When enabled, users can edit all own entries in the margic.';
$string['editdates'] = 'Edit entry dates';
$string['editdates_help'] = 'When enabled, users can edit the date of any entry.';
$string['editingended'] = 'Editing period has ended at {$a}';
$string['editingends'] = 'Editing period ends at {$a}';
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
$string['gradeingradebook'] = 'Current rating from gradebook';
$string['feedbackingradebook'] = 'Current feedback from gradebook';
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
$string['pluginname'] = 'Margic';
$string['previousentry'] = 'Previous entry';
$string['rate'] = 'Rate';
$string['rating'] = 'Rating';
$string['savedrating'] = 'Rating saved for this entry';
$string['newrating'] = 'New rating for this entry';
$string['removeentries'] = 'Remove all entries';
$string['removemessages'] = 'Remove all margic entries';
$string['reportsingle'] = 'Get all margic entries for this user.';
$string['reportsingleallentries'] = 'All margic entries for this user.';
$string['returnto'] = 'Return to {$a}';
$string['returntoreport'] = 'Return to report page for - {$a}';
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
$string['viewandmakeannotations'] = 'View and create annotations';
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

$string['annotationssummary'] = 'Annotations summary and error types';
$string['participant'] = 'Participant';
$string['backtooverview'] = 'Back to overview';
$string['addannotationtype'] = 'Add annotation type';
$string['annotationtypeadded'] = 'Annotation type added';
$string['editannotationtype'] = 'Edit annotation type';
$string['annotationtypeedited'] = 'Annotation type edited';
$string['annotationtypecantbeedited'] = 'Annotation type could not be changed';
$string['deleteannotationtype'] = 'Delete annotation type';
$string['annotationtypedeleted'] = 'Annotation type deleted';
$string['annotationtypeinvalid'] = 'Annotation type invalid';
$string['nameofannotationtype'] = 'Name of annotation type';
$string['annotationcreated'] = 'Created at {$a}';
$string['annotationmodified'] = 'Modified at {$a}';
$string['editannotation'] = 'Edit';
$string['deleteannotation'] = 'Delete';
$string['annotationcolor'] = 'Color of the annotation type';
$string['defaulttype'] = 'Default error type';
$string['customtype'] = 'Custom error type';
$string['errnohexcolor'] = 'No hex value for color.';
$string['changesforall'] = 'Changing the name or color of the annotation type will affect all already created annotations as well as all future annotations immediately after saving.';
$string['explanationtypename'] = 'Name of annotation type';
$string['explanationtypename_help'] = 'The name of the annotation type. For the following standard annotation types, translations are already stored in Moodle: "grammar_verb", "grammar_syntax", "grammar_congruence", "grammar_other", "expression", "orthography", "punctuation" and "other". All other names are not translated.';
$string['explanationhexcolor'] = 'Color of the annotation type';
$string['explanationhexcolor_help'] = 'The color of the annotation type as hexadecimal value. This consists of exactly 6 characters (A-F as well as 0-9) and represents a color. You can find out the hexadecimal value of any color, for example, at https://www.w3schools.com/colors/colors_picker.asp.';
$string['explanationdefaulttype'] = 'Here you can select whether the annotation type should be a default type. In this case it will be displayed to all teachers in all Margic instances and can be used by them. Otherwise, it becomes a normal error type and can only be used by its creator.';
$string['annotatedtextnotfound'] = 'Annotated text not found';
$string['annotatedtextinvalid'] = 'The originally annotated text has become invalid (e.g. due to a subsequent change to the original entry). The marking for this annotation must therefore be redone.';
$string['notallowedtodothis'] = 'No permissions to do this.';
$string['deletedannotationtype'] = 'Deleted type';
$string['errtypedeleted'] = 'Annotation type does not exists.';
$string['grader'] = 'Grader';
$string['feedbackupdated'] = 'Feedback and / or rating updated';
$string['errfeedbacknotupdated'] = 'Feedback and grade not updated';
$string['errnograder'] = 'No grader.';
$string['errnofeedbackorratingdisabled'] = 'No feedback or rating disabled.';
$string['annotationareawidth'] = 'Width of the annotation area';
$string['annotationareawidthall'] = 'The width of the annotation area in percent for all margics. Can be overridden by teachers in the individual margics.';
$string['annotationareawidth_help'] = 'The width of the annotation area in percent.';
$string['errannotationareawidthinvalid'] = 'Width invalid (minimum: {$a->minwidth}%, maximum: {$a->maxwidth}%).';
$string['toggleannotation'] = 'Toggle annotation';
$string['toggleallannotations'] = 'Toggle all annotations';

// Privacy.
$string['privacy:metadata:margic_entries'] = 'Contains the user entries saved in all margics.';
$string['privacy:metadata:margic_annotations'] = 'Contains the annotations made in all margics.';
$string['privacy:metadata:margic_annotation_types'] = 'Contains the annotation types of all margics.';
$string['privacy:metadata:margic_entries:margic'] = 'ID of the Margic the entry belongs to.';
$string['privacy:metadata:margic_entries:userid'] = 'ID of the user the entry belongs to.';
$string['privacy:metadata:margic_entries:timecreated'] = 'Date on which the entry was created.';
$string['privacy:metadata:margic_entries:timemodified'] = 'Time the entry was last modified.';
$string['privacy:metadata:margic_entries:text'] = 'The content of the entry.';
$string['privacy:metadata:margic_entries:rating'] = 'The grade with which the entry was rated.';
$string['privacy:metadata:margic_entries:entrycomment'] = 'The teachers comment for the entry.';
$string['privacy:metadata:margic_entries:teacher'] = 'ID of the grader.';
$string['privacy:metadata:margic_entries:timemarked'] = 'Time the entry was graded.';
$string['privacy:metadata:margic_annotations:margic'] = 'ID of the Margic the annotated entry belongs to.';
$string['privacy:metadata:margic_annotations:entry'] = 'ID of the entry the annotation belongs to.';
$string['privacy:metadata:margic_annotations:userid'] = 'ID of the user that made the annotation.';
$string['privacy:metadata:margic_annotations:timecreated'] = 'Date on which the annotation was created.';
$string['privacy:metadata:margic_annotations:timemodified'] = 'Time the annotation was last modified.';
$string['privacy:metadata:margic_annotations:type'] = 'Id of the type of the annotation.';
$string['privacy:metadata:margic_annotations:text'] = 'Content of the annotation.';
$string['privacy:metadata:margic_annotation_types:userid'] = 'ID of the user that made the annotation type.';
$string['privacy:metadata:margic_annotation_types:timecreated'] = 'Date on which the annotation type was created.';
$string['privacy:metadata:margic_annotation_types:timemodified'] = 'Time the annotation type was last modified.';
$string['privacy:metadata:margic_annotation_types:name'] = 'Name of the annotation type.';
$string['privacy:metadata:margic_annotation_types:color'] = 'Color of the annotation type as hex value.';
$string['privacy:metadata:core_rating'] = 'Ratings added to margic entries are stored using the core_rating system.';
$string['privacy:metadata:core_files'] = 'Files linked to margic entries are stored using the core_files system.';
$string['privacy:metadata:preference:sortoption'] = 'The preference for the sorting of each margic.';
$string['privacy:metadata:preference:margic_pagecount'] = 'The number of entries that should be shown per page for each Margic.';
$string['privacy:metadata:preference:margic_activepage'] = 'The number of the page currently opened in each Margic.';

// Löschen.
$string['numwordscln'] = '{$a->one} clean text words using {$a->two} characters, NOT including {$a->three} spaces. ';
$string['numwordsstd'] = '{$a->one} standardized words using {$a->two} characters, including {$a->three} spaces. ';
$string['numwordsnew'] = 'New calculation: {$a->one} raw text words using {$a->two} characters, in {$a->three} sentences, in {$a->four} paragraphs. ';
$string['edittopoflist'] = 'Edit top of the list';
$string['reload'] = 'Reload and show from current to oldest margic entry';
$string['sortfirstentry'] = 'From first margic entry to the latest entry.';
$string['outof'] = ' out of {$a} entries.';
