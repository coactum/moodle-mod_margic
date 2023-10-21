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

// Events.
$string['eventdownloadentries'] = 'Download entries';
$string['evententrycreated'] = 'Margic entry created';
$string['evententryupdated'] = 'Margic entry updated';
$string['eventannotationcreated'] = 'Margic annotation created';
$string['eventannotationupdated'] = 'Margic annotation updated';
$string['eventannotationdeleted'] = 'Margic annotation deleted';
$string['eventfeedbackupdated'] = 'Margic feedback updated';
$string['eventinvalidaccess'] = 'Invalid access';

// Common.
$string['modulename'] = 'Margic';
$string['modulenameplural'] = 'Margics';
$string['modulename_help'] = 'In the Margic activity, participants can create unlimited entries which can then be evaluated and annotated by teachers.

Margics can be used in a meaningful way in language lessons, for example. Students can create entries to answer variable tasks, write their own texts and stories, or practice vocabulary.

Teachers can then view, correct and evaluate these entries on a customizable overview page. For this purpose, they can mark specific text passages and write annotations for them, whereby an error type and a short text can be stored for each annotation. The entire entry can also be graded and provided with textual or acoustic feedback. Participants then have the opportunity to revise their original entry and use the feedback received to improve it.

The available error types for the annotations can be flexibly adjusted. In an error summary, instructors can also evaluate for each participant how many and which errors they made in a Margic. Finally, it is also possible to export the written entries for further use.

Core features of the plugin:

* Write and revise multimedia entries.
* Individually customizable overview page with all (own) entries available in Margic
* Extensive possibilities for annotation and evaluation of entries for teachers
* Customizable error types and accurate error evaluation';
$string['modulename_link'] = 'mod/margic/view';
$string['pluginadministration'] = 'Margic administration';

// General errors.
$string['erraccessdenied'] = 'Access denied';
$string['generalerrorinsert'] = 'Could not save the new Margic entry.';
$string['incorrectcourseid'] = 'Course ID is incorrect';
$string['incorrectmodule'] = 'Course Module ID is incorrect';

// Entry (template).
$string['entry'] = 'Entry';
$string['revision'] = 'Revision';
$string['baseentry'] = 'Base entry';
$string['editthisentry'] = 'Edit this entry';
$string['blankentry'] = 'Blank entry';
$string['created'] = '{$a->years} years, {$a->month} months, {$a->days} days and {$a->hours} hours ago';
$string['details'] = 'Statistics';
$string['numwordsraw'] = '{$a->wordscount} text words using {$a->charscount} characters, including {$a->spacescount} spaces.';
$string['lastedited'] = 'Last edited';
$string['needsgrading'] = ' This entry has not been given feedback or a rating yet.';
$string['needsregrading'] = 'This entry has changed since feedback or a rating was given.';
$string['getallentriesofuser'] = 'Show all Margic entries for this user';
$string['toggleannotation'] = 'Toggle annotation';
$string['id'] = 'ID';
$string['at'] = 'at';
$string['from'] = 'from';
$string['toggleolderversions'] = 'Toggle older versions of the entry';
$string['hoverannotation'] = 'Hover annotation';

// View (and template).
$string['overview'] = 'Overview';
$string['viewentries'] = 'View entries';
$string['startnewentry'] = 'New entry';
$string['togglegradingform'] = 'Grading';
$string['viewannotations'] = 'View annotations';
$string['hideannotations'] = 'Hide annotations';
$string['norating'] = 'Rating disabled.';
$string['forallmyentries'] = 'for all of my entries';
$string['entries'] = 'Entries';
$string['myentries'] = 'My entries';
$string['annotations'] = 'Annotations';
$string['toggleallannotations'] = 'Toggle all annotations';
$string['csvexport'] = 'Export as .csv file';
$string['pagesize'] = 'Entries per page';
$string['sorting'] = 'Sorting';
$string['currenttooldest'] = 'Show entries from most recent to oldest';
$string['oldesttocurrent'] = 'Show entries from oldest to most recent';
$string['lowestgradetohighest'] = 'Show entries from the lowest rated to the highest';
$string['highestgradetolowest'] = 'Show entries from the highest rated to the lowest';
$string['currententry'] = 'Current entries';
$string['oldestentry'] = 'Oldest entries';
$string['lowestgradeentry'] = 'Lowest rated entries';
$string['highestgradeentry'] = 'Highest rated entries';
$string['editingstarts'] = 'Editing period starts at {$a}';
$string['editingends'] = 'Editing period ends on {$a}';
$string['editingended'] = 'Editing period ended on {$a}';
$string['notstarted'] = 'You have not added any entries to this Margic yet';
$string['noentriesfound'] = 'No entries found';
$string['viewallentries'] = 'View all entries';

// Annotations.
$string['annotationcreated'] = 'Created at {$a}';
$string['annotationmodified'] = 'Modified at {$a}';
$string['editannotation'] = 'Edit';
$string['deleteannotation'] = 'Delete';
$string['annotationadded'] = 'Annotation added';
$string['annotationedited'] = 'Annotation edited';
$string['annotationdeleted'] = 'Annotation deleted';
$string['annotationinvalid'] = 'Annotation invalid';
$string['annotatedtextnotfound'] = 'Annotated text not found';
$string['annotatedtextinvalid'] = 'The originally annotated text has become invalid. The marking for this annotation must therefore be redone.';
$string['notallowedtodothis'] = 'No permissions to do this.';
$string['deletederrortype'] = 'Deleted type';
$string['errtypedeleted'] = 'Annotation type does not exists.';
$string['annotationsarefetched'] = 'Annotations being loaded';
$string['reloadannotations'] = 'Reload annotations';

// Form: mod_form.
$string['margicname'] = 'Name of the Margic';
$string['margicdescription'] = 'Description of the Margic';
$string['margicopentime'] = 'Open time';
$string['margicopentime_help'] = 'If enabled, you can set the date from which entries can be created in the Margic.';
$string['margicclosetime'] = 'Close time';
$string['margicclosetime_help'] = 'If activated, you can set a date until which entries can be created or edited in the Margic.';
$string['annotationareawidth_help'] = 'The width of the annotation area in percent. Minimum 20 and maximum 80 percent.';
$string['errannotationareawidthinvalid'] = 'Width invalid (minimum: {$a->minwidth}%, maximum: {$a->maxwidth}%).';
$string['overwriteannotations'] = 'Overwrite annotations';
$string['overwriteannotations_help'] = 'Here you can define whether teachers can overwrite and delete annotations made by other teachers.';

// Form: edit_form.
$string['addnewentry'] = 'Add new entry';
$string['editentry'] = 'Edit entry';
$string['margicentrydate'] = 'Set date for this entry';
$string['editentrynotpossible'] = 'You can not edit this entry.';
$string['editdateinfuture'] = 'The specified entry date is in the future.';
$string['entryaddedoredited'] = 'Entry added or modified.';
$string['timecreatedinvalid'] = 'Change failed. There are already younger versions of this entry.';
$string['entryadded'] = 'Entry added';

// Form: grading_form.
$string['gradeingradebook'] = 'Current rating from gradebook';
$string['feedbackingradebook'] = 'Current feedback from gradebook';
$string['savedrating'] = 'Rating saved for this entry';
$string['newrating'] = 'New rating for this entry';
$string['forallentries'] = 'for all entries of';
$string['grader'] = 'Grader';
$string['feedbackupdated'] = 'Feedback and / or rating updated';
$string['errfeedbacknotupdated'] = 'Feedback and grade not updated';
$string['errnograder'] = 'No grader.';
$string['errnofeedbackorratingdisabled'] = 'No feedback or rating disabled.';
$string['defaultforsendgradingmessage'] = 'Default value for feedback notification';
$string['defaultforsendgradingmessage_help'] = 'Here you can set the default value for the feedback notification. This is pre-filled in the rating form, but can also be manually changed there for each rating.';

// Error summary.
$string['errorsummary'] = 'Error summary';
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
$string['deleteerrortypetemplateconfirm'] = 'Should this error type template really be deleted? This deletes the template for the entire system so that it can no longer be used as a concrete error type in new Margics. This action cannot be undone!';
$string['deleteerrortypeconfirm'] = 'Do you really want to delete this error type? This will remove it from the Margic and display it as Deleted type for existing annotations. This action cannot be undone!';
$string['errortypeinvalid'] = 'Error type invalid';
$string['nameoferrortype'] = 'Name of error type';
$string['margicerrortypes'] = 'Margic error types';
$string['errortypetemplates'] = 'Error type templates';
$string['errortypes'] = 'Error types';
$string['template'] = 'Template';
$string['addtomargic'] = 'Add to Margic';
$string['switchtotemplatetypes'] = 'Switch to the error type templates';
$string['switchtomargictypes'] = 'Switch to the error types for the Margic';
$string['notemplatetypes'] = 'No error type templates available';
$string['movefor'] = 'Display more in front';
$string['moveback'] = 'Display further back';
$string['prioritychanged'] = 'Order changed';
$string['prioritynotchanged'] = 'Order could not be changed';

// Form: mod_margic_errortypes_form.
$string['annotationcolor'] = 'Color of the error type';
$string['standardtype'] = 'Standard error type';
$string['manualtype'] = 'Manual error type';
$string['standard'] = 'Standard';
$string['custom'] = 'Custom';
$string['type'] = 'Type';
$string['color'] = 'Color';
$string['errnohexcolor'] = 'No hexadecimal value for color.';
$string['warningeditdefaulterrortypetemplate'] = 'WARNING: This will change the error type template system-wide. When creating new Margics, the changed template will then be available for selecting the concrete Margic error types.';
$string['changetemplate'] = 'Changing the name or color of the error type only affects the template and therefore only takes effect when new Margics are created. The error types in existing Margics are not affected by these changes.';
$string['explanationtypename'] = 'Name of the error type';
$string['explanationtypename_help'] = 'The name of the error type. For the following standard error types, translations are already stored in Moodle: "grammar_verb", "grammar_syntax", "grammar_congruence", "grammar_other", "expression", "orthography", "punctuation" and "other". All other names are not translated.';
$string['explanationhexcolor'] = 'Color of the error type';
$string['explanationhexcolor_help'] = 'The color of the error type as hexadecimal value. This consists of exactly 6 characters (A-F as well as 0-9) and represents a color. You can find out the hexadecimal value of any color, for example, at <a href="https://www.w3schools.com/colors/colors_picker.asp" target="_blank">https://www.w3schools.com/colors/colors_picker.asp</a>.';
$string['explanationstandardtype'] = 'Here you can select whether the error type should be a default type. In this case teachers can select it as error type that can be used in their Margics. Otherwise, only you can add this error type to your Margics.';

// Calendar.
$string['calendarend'] = '{$a} closes';
$string['calendarstart'] = '{$a} opens';

// CSV export.
$string['pluginname'] = 'Margic';
$string['userid'] = 'User id';
$string['timecreated'] = 'Time created';
$string['timemodified'] = 'Time modified';
$string['text'] = 'Text';
$string['feedback'] = 'Entry feedback';
$string['format'] = 'Format';
$string['teacher'] = 'Teacher';
$string['timemarked'] = 'Time graded';
$string['rating'] = 'Rating';
$string['exportfilenamemyentries'] = 'My_Margic_Entries';
$string['exportfilenamemargicentries'] = 'Margic_Entries';
$string['exportfilenameallentries'] = 'All_Margic_Entries';

// Capabilities.
$string['margic:addentries'] = 'Add Margic entries';
$string['margic:addinstance'] = 'Add Margic instances';
$string['margic:manageentries'] = 'Manage Margic entries';
$string['margic:rate'] = 'Rate Margic entries';
$string['margic:receivegradingmessages'] = 'Receive messages about the rating of entries';
$string['margic:manageerrortypes'] = 'Manage Margic error types';
$string['margic:viewerrorsummary'] = 'View Margic error summary';
$string['margic:viewerrorsfromallparticipants'] = 'View errors from all participants';
$string['margic:editdefaulterrortypes'] = 'Edit default error type templates';
$string['margic:viewannotations'] = 'View annotations';
$string['margic:makeannotations'] = 'Make annotations';
$string['margic:deleteannotations'] = 'Delete annotations';
$string['margic:viewotherusersentrytimes'] = 'View time of creation for foreign entries ';
$string['margic:viewotherusersannotationtimes'] = 'View time of creation for foreign annotations ';
$string['margic:viewotherusersfeedbacktimes'] = 'View the time of the grading by other teachers ';

// Recent activity.
$string['newmargicentries'] = 'New Margic entries';

// User complete.
$string['noentry'] = 'No entry';

// Search.
$string['search'] = 'Search';
$string['search:activity'] = 'Margic - activity information';
$string['search:entry'] = 'Margic entries';
$string['search:feedback'] = 'Feedback to Margic entries';

// Default error type templates.
$string['grammar_verb'] = 'Grammar: Verb form';
$string['grammar_syntax'] = 'Grammar: Syntax';
$string['grammar_congruence'] = 'Grammar: Agreement';
$string['grammar_other'] = 'Grammar: Other';
$string['expression'] = 'Expression';
$string['orthography'] = 'Orthography';
$string['punctuation'] = 'Punctuation';
$string['other'] = 'Other';

// Lib.
$string['deletealluserdata'] = 'Delete all entries, annotations, files and ratings';
$string['alluserdatadeleted'] = 'All entries, annotations, files and ratings are deleted';
$string['deleteerrortypes'] = 'Delete error types';
$string['errortypesdeleted'] = 'Error types deleted';

// Messages.
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

// Admin settings.
$string['defaulterrortypetemplateseditable'] = 'Editing of the default error type templates';
$string['defaulterrortypetemplateseditable_help'] = 'If enabled, administrators or users with the editdefaulterrortypes permission can edit the default error type templates in a Margic on the error summary page. Modifying a template changes it throughout the system so that when new Margics are created, the modified template is displayed when the concrete error types are selected. Existing concrete Margic error types are not changed by modifying a template. If no is selected here, error type templates cannot be changed from inside a Margic.';
$string['editentrydates'] = 'Edit entry dates';
$string['editentrydates_help'] = 'If enabled, teachers can configure in each Margic whether users can edit their own entries.';
$string['editentries'] = 'Edit own entries';
$string['editentries_help'] = 'If enabled, teachers can configure in each Margic whether users can edit the date of each new entry.';
$string['sendgradingmessagedefault'] = 'Notify entry creators about feedback';
$string['sendgradingmessagedefault_help'] = 'Set the default value for the feedback forms in all Margics. Defines if entry creators should be notified if the teacher grades an entry. Can be changed in each Margic or in the feedback form itself.';
$string['annotationareawidth'] = 'Width of the annotation area';
$string['annotationareawidthall'] = 'The width of the annotation area in percent for all margics. Can be overridden by teachers in the individual margics. Minimum 20 and maximum 80 percent.';
$string['editability'] = 'Editability';
$string['entrybgc_title'] = 'Background color for the entries and annotations';
$string['entrybgc_descr'] = 'Here you can set the background color of the areas for the entries and annotations.';
$string['textbgc_title'] = 'Background color of the texts';
$string['textbgc_descr'] = 'Here you can set the background color of the texts in the entries and annotations.';

// Privacy.
$string['privacy:metadata:margic_entries'] = 'Contains the user entries saved in all margics.';
$string['privacy:metadata:margic_annotations'] = 'Contains the annotations made in all margics.';
$string['privacy:metadata:margic_errortype_templates'] = 'Contains the error type templates created by teachers.';
$string['privacy:metadata:margic_entries:margic'] = 'ID of the Margic the entry belongs to.';
$string['privacy:metadata:margic_entries:userid'] = 'ID of the user the entry belongs to.';
$string['privacy:metadata:margic_entries:timecreated'] = 'Date on which the entry was created.';
$string['privacy:metadata:margic_entries:timemodified'] = 'Time the entry was last modified.';
$string['privacy:metadata:margic_entries:text'] = 'The content of the entry.';
$string['privacy:metadata:margic_entries:rating'] = 'The grade with which the entry was rated.';
$string['privacy:metadata:margic_entries:feedback'] = 'The teachers feedback for the entry.';
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
$string['privacy:metadata:margic_errortype_templates:timecreated'] = 'Date on which the error type template was created.';
$string['privacy:metadata:margic_errortype_templates:timemodified'] = 'Time the error type template was last modified.';
$string['privacy:metadata:margic_errortype_templates:name'] = 'Name of the error type template.';
$string['privacy:metadata:margic_errortype_templates:color'] = 'Color of the error type template as hexadecimal value.';
$string['privacy:metadata:margic_errortype_templates:userid'] = 'ID of the user that made the error type template.';
$string['privacy:metadata:core_rating'] = 'Ratings added to Margic entries are saved.';
$string['privacy:metadata:core_files'] = 'Files associated with Margic entries are saved.';
$string['privacy:metadata:core_message'] = 'Messages are sent to users about the grading of Margic entries.';
$string['privacy:metadata:preference:margic_sortoption'] = 'The preference for the sorting of a Margic.';
$string['privacy:metadata:preference:margic_pagecount'] = 'The number of entries that should be shown per page for a Margic.';
$string['privacy:metadata:preference:margic_activepage'] = 'The number of the last opened page in a Margic.';
