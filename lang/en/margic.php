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
$string['alias'] = 'Keyword';
$string['aliases'] = 'Keyword(s)';
$string['aliases_help'] = 'Each margic entry can have an associated list of keywords (or aliases).

Enter each keyword on a new line (not separated by commas).';
$string['and'] = ' and ';
$string['blankentry'] = 'Blank entry';
$string['calendarend'] = '{$a} closes';
$string['calendarstart'] = '{$a} opens';
$string['created'] = '{$a->years} years, {$a->month} months, {$a->days} days and {$a->hours} hours ago';
$string['csvexport'] = 'Export to .csv';
$string['deadline'] = 'Days Open';
$string['details'] = 'Statistics';
$string['margicclosetime'] = 'Close time';
$string['margicclosetime_help'] = 'If this option is activated, you can set a date on which the Margic is closed. Participants will no longer be able to create or edit entries after that date.';
$string['margicentrydate'] = 'Set date for this entry';
$string['margicopentime'] = 'Open time';
$string['margicopentime_help'] = 'If enabled, you can set a date for the margic to be opened for use.';
$string['editingstarts'] = 'Editing period starts at {$a}';
$string['editingended'] = 'Editing period has ended at {$a}';
$string['editingends'] = 'Editing period ends at {$a}';
$string['editthisentry'] = 'Edit this entry';
$string['entries'] = 'Entries';
$string['entry'] = 'Entry';
$string['entrycomment'] = 'Entry comment';
$string['exportfilenamemyentries'] = 'My_Margic_Entries';
$string['exportfilenamemargicentries'] = 'Margic_Entries';
$string['exportfilenameallentries'] = 'All_Margic_Entries';
$string['gradeingradebook'] = 'Current rating from gradebook';
$string['feedbackingradebook'] = 'Current feedback from gradebook';
$string['margic:addentries'] = 'Add margic entries';
$string['margic:addinstance'] = 'Add margic instances';
$string['margic:manageentries'] = 'Manage margic entries';
$string['margic:rate'] = 'Rate margic entries';
$string['margic:receivegradingmessages'] = 'Receive messages about the rating of entries';
$string['margic:editdefaulterrortypes'] = 'Edit default error type templates';
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
$string['returnto'] = 'Return to {$a}';
$string['returntoreport'] = 'Return to report page for - {$a}';
$string['savesettings'] = 'Save settings';
$string['search'] = 'Search';
$string['search:entry'] = 'margic - entries';
$string['search:entrycomment'] = 'margic - entry comment';
$string['search:activity'] = 'margic - activity information';
$string['selectentry'] = 'Select entry for marking';
$string['sortorder'] = 'Sort order is: ';
$string['sortcurrententry'] = 'From current margic entry to the first entry.';
$string['sortlowestentry'] = 'From lowest rated margic entry to the highest entry.';
$string['sorthighestentry'] = 'From highest rated margic entry to the lowest rated entry.';
$string['sortlastentry'] = 'From latest modified margic entry to the oldest modified entry.';
$string['sortoptions'] = ' Sort options: ';
$string['startnewentry'] = 'New entry';
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
$string['togglegradingform'] = 'Grading';
$string['norating'] = 'Rating disabled.';
$string['viewallmargics'] = 'View all margics in course';
$string['startoreditentry'] = 'Add or edit entry';
$string['addnewentry'] = 'Add new entry';
$string['editentry'] = 'Edit entry';
$string['editentrynotpossible'] = 'You can not edit this entry.';
$string['editdateinfuture'] = 'The specified entry date is in the future.';
$string['currenttooldest'] = 'Show entries from current to oldest';
$string['oldesttocurrent'] = 'Show entries from oldest to current';
$string['lowestgradetohighest'] = 'Show entries from the lowest rated to the highest one';
$string['highestgradetolowest'] = 'Show entries from the highest rated to the lowest one';
$string['sorting'] = 'Sorting';
$string['currententry'] = 'Current entries';
$string['oldestentry'] = 'Oldest entries';
$string['lowestgradeentry'] = 'Lowest rated entries';
$string['highestgradeentry'] = 'Highest rated entries';
$string['viewallentries'] = 'View all entries';

$string['grammar_verb'] = 'Grammar: Verb form';
$string['grammar_syntax'] = 'Grammar: Syntax';
$string['grammar_congruence'] = 'Grammar: Congruence';
$string['grammar_other'] = 'Grammar: Other';
$string['expression'] = 'Expression';
$string['orthography'] = 'Orthography';
$string['punctuation'] = 'Punctuation';
$string['other'] = 'Other';

$string['annotationssummary'] = 'Error summary';
$string['participant'] = 'Participant';
$string['backtooverview'] = 'Back to overview';
$string['adderrortype'] = 'Add error type';
$string['errortypeadded'] = 'Error type added';
$string['editerrortype'] = 'Edit error type';
$string['errortypeedited'] = 'Error type edited';
$string['editerrortypetemplate'] = 'Edit template';
$string['errortypecantbeedited'] = 'Error type could not be changed';
$string['deleteerrortype'] = 'Delete error type';
$string['errortypedeleted'] = 'Error type deleted';
$string['deleteerrortypetemplate'] = 'Delete template';
$string['errortypeinvalid'] = 'Error type invalid';
$string['nameoferrortype'] = 'Name of error type';
$string['annotationcreated'] = 'Created at {$a}';
$string['annotationmodified'] = 'Modified at {$a}';
$string['editannotation'] = 'Edit';
$string['deleteannotation'] = 'Delete';
$string['annotationcolor'] = 'Color of the error type';
$string['standardtype'] = 'Standard error type';
$string['manualtype'] = 'Manual error type';
$string['standard'] = 'Standard';
$string['custom'] = 'Custom';
$string['type'] = 'Type';
$string['color'] = 'Color';
$string['errnohexcolor'] = 'No hex value for color.';
$string['changetype'] = 'Changing the name or color of the error type only affects the template and therefore only takes effect when new margics are created. The error types in existing margics are not affected by these changes.';
$string['explanationtypename'] = 'Name of annotation type';
$string['explanationtypename_help'] = 'The name of the annotation type. For the following standard annotation types, translations are already stored in Moodle: "grammar_verb", "grammar_syntax", "grammar_congruence", "grammar_other", "expression", "orthography", "punctuation" and "other". All other names are not translated.';
$string['explanationhexcolor'] = 'Color of the annotation type';
$string['explanationhexcolor_help'] = 'The color of the annotation type as hexadecimal value. This consists of exactly 6 characters (A-F as well as 0-9) and represents a color. You can find out the hexadecimal value of any color, for example, at https://www.w3schools.com/colors/colors_picker.asp.';
$string['explanationstandardtype'] = 'Here you can select whether the error type should be a default type. In this case teachers can select it as error type that can be used in their Margics. Otherwise, only you can add this error type to your Margics.';
$string['annotatedtextnotfound'] = 'Annotated text not found';
$string['annotatedtextinvalid'] = 'The originally annotated text has become invalid (e.g. due to a subsequent change to the original entry). The marking for this annotation must therefore be redone.';
$string['notallowedtodothis'] = 'No permissions to do this.';
$string['deletederrortype'] = 'Deleted type';
$string['errtypedeleted'] = 'Annotation type does not exists.';
$string['grader'] = 'Grader';
$string['feedbackupdated'] = 'Feedback and / or rating updated';
$string['errfeedbacknotupdated'] = 'Feedback and grade not updated';
$string['errnograder'] = 'No grader.';
$string['errnofeedbackorratingdisabled'] = 'No feedback or rating disabled.';
$string['annotationareawidth_help'] = 'The width of the annotation area in percent.';
$string['errannotationareawidthinvalid'] = 'Width invalid (minimum: {$a->minwidth}%, maximum: {$a->maxwidth}%).';
$string['toggleannotation'] = 'Toggle annotation';
$string['toggleallannotations'] = 'Toggle all annotations';
$string['entryaddedoredited'] = 'Entry added or modified.';
$string['deletealluserdata'] = 'Delete all entries, annotations, files and ratings';
$string['alluserdatadeleted'] = 'All entries, annotations, files and ratings are deleted';
$string['deleteerrortypes'] = 'Delete errortypes';
$string['errortypesdeleted'] = 'Errortypes deleted';

$string['margicerrortypes'] = 'Margic error types';
$string['errortypetemplates'] = 'Error type templates';
$string['errortypes'] = 'Error types';
$string['template'] = 'Template';
$string['addtomargic'] = 'Add to Margic';
$string['switchtotemplatetypes'] = 'Switch to the errortype templates';
$string['switchtomargictypes'] = 'Switch to the error types for the Margic';
$string['notemplatetypes'] = 'No errortype templates available';
$string['movefor'] = 'Display more in front';
$string['moveback'] = 'Display further back';
$string['prioritychanged'] = 'Order changed';
$string['prioritynotchanged'] = 'Order could not be changed';
$string['revision'] = 'Revision';
$string['baseentry'] = 'Base entry';
$string['id'] = 'ID';
$string['overview'] = 'Overview';
$string['at'] = 'at';
$string['from'] = 'from';
$string['toggleolderversions'] = 'Toggle older versions of the entry';
$string['timecreatedinvalid'] = 'Change failed. There are already younger versions of this entry.';
$string['messageprovider:gradingmessages'] = 'Notifications when entries are rated';
$string['sendgradingmessage'] = 'Notify the creator of the entry immediately about the rating';
$string['gradingmailsubject'] = 'Received feedback for Margic entry';
$string['gradingmailfullmessage'] = 'Greetings {$a->user},
{$a->teacher} has published a feedback or rating for your entry in Margic {$a->margic}.
Here you can view them: {$a->url}';
$string['gradingmailfullmessagehtml'] = 'Greetings {$a->user},<br>
{$a->teacher} has published a feedback or rating for your entry in Margic <strong>{$a->margic}</strong>.<br><br>
<a href="{$a->url}"><strong>Here</strong></a> you can view them.';
$string['mailfooter'] = 'This message is about a Margic in {$a->systemname}. You can find all further information under the following link: <br> {$a->coursename} -> Margic -> {$a->name} <br> {$a->url}';
$string['hoverannotation'] = 'Hover annotation';
$string['entryadded'] = 'Entry added';

// Admin settings.
$string['editentrydates'] = 'Edit entry dates';
$string['editentrydates_help'] = 'If enabled, teachers can configure in each margic whether users can edit their own entries.';
$string['editentries'] = 'Edit own entries';
$string['editentries_help'] = 'If enabled, teachers can configure in each margic whether users can edit the date of each new entry.';
$string['annotationareawidth'] = 'Width of the annotation area';
$string['annotationareawidthall'] = 'The width of the annotation area in percent for all margics. Can be overridden by teachers in the individual margics.';
$string['editability'] = 'Editability';
$string['entrybgc_title'] = 'Background color for the entries and annotations';
$string['entrybgc_descr'] = 'Here you can set the background color of the areas for the entries and annotations.';
$string['entrybgc_colour'] = '#C8E5FD';
$string['textbgc_title'] = 'Background color of the texts';
$string['textbgc_descr'] = 'Here you can set the background color of the texts in the entries and annotations.';
$string['textbgc_colour'] = '#F9F5F0';

// Privacy.
$string['privacy:metadata:margic_entries'] = 'Contains the user entries saved in all margics.';
$string['privacy:metadata:margic_annotations'] = 'Contains the annotations made in all margics.';
$string['privacy:metadata:margic_errortype_templates'] = 'Contains the errortype templates created by teachers.';
$string['privacy:metadata:margic_entries:margic'] = 'ID of the Margic the entry belongs to.';
$string['privacy:metadata:margic_entries:userid'] = 'ID of the user the entry belongs to.';
$string['privacy:metadata:margic_entries:timecreated'] = 'Date on which the entry was created.';
$string['privacy:metadata:margic_entries:timemodified'] = 'Time the entry was last modified.';
$string['privacy:metadata:margic_entries:text'] = 'The content of the entry.';
$string['privacy:metadata:margic_entries:rating'] = 'The grade with which the entry was rated.';
$string['privacy:metadata:margic_entries:entrycomment'] = 'The teachers comment for the entry.';
$string['privacy:metadata:margic_entries:teacher'] = 'ID of the grader.';
$string['privacy:metadata:margic_entries:timemarked'] = 'Time the entry was graded.';
$string['privacy:metadata:margic_entries:baseentry'] = 'The ID of the original entry on which this revised entry is based';
$string['privacy:metadata:margic_annotations:margic'] = 'ID of the Margic the annotated entry belongs to.';
$string['privacy:metadata:margic_annotations:entry'] = 'ID of the entry the annotation belongs to.';
$string['privacy:metadata:margic_annotations:userid'] = 'ID of the user that made the annotation.';
$string['privacy:metadata:margic_annotations:timecreated'] = 'Date on which the annotation was created.';
$string['privacy:metadata:margic_annotations:timemodified'] = 'Time the annotation was last modified.';
$string['privacy:metadata:margic_annotations:type'] = 'ID of the type of the annotation.';
$string['privacy:metadata:margic_annotations:text'] = 'Content of the annotation.';
$string['privacy:metadata:margic_errortype_templates:timecreated'] = 'Date on which the errortype template was created.';
$string['privacy:metadata:margic_errortype_templates:timemodified'] = 'Time the errortype template was last modified.';
$string['privacy:metadata:margic_errortype_templates:name'] = 'Name of the errortype template.';
$string['privacy:metadata:margic_errortype_templates:color'] = 'Color of the errortype template as hex value.';
$string['privacy:metadata:margic_errortype_templates:userid'] = 'ID of the user that made the errortype template.';
$string['privacy:metadata:core_rating'] = 'Ratings added to Margic entries are saved.';
$string['privacy:metadata:core_files'] = 'Files associated with Margic entries are saved.';
$string['privacy:metadata:core_message'] = 'Messages are sent to users about the grading of Margic entries.';
$string['privacy:metadata:preference:margic_sortoption'] = 'The preference for the sorting of a margic.';
$string['privacy:metadata:preference:margic_pagecount'] = 'The number of entries that should be shown per page for a Margic.';
$string['privacy:metadata:preference:margic_activepage'] = 'The number of the last opened page in a Margic.';

// Löschen.
$string['numwordscln'] = '{$a->one} clean text words using {$a->two} characters, NOT including {$a->three} spaces. ';
$string['numwordsstd'] = '{$a->one} standardized words using {$a->two} characters, including {$a->three} spaces. ';
$string['numwordsnew'] = 'New calculation: {$a->one} raw text words using {$a->two} characters, in {$a->three} sentences, in {$a->four} paragraphs. ';
$string['edittopoflist'] = 'Edit top of the list';
$string['reload'] = 'Reload and show from current to oldest margic entry';
$string['sortfirstentry'] = 'From first margic entry to the latest entry.';
$string['outof'] = ' out of {$a} entries.';
