<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Strings for component 'margic', language 'de', version '3.9'.
 *
 * @package     mod_margic
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Events.
$string['eventdownloadentries'] = 'Margic Einträge herunterladen';
$string['evententrycreated'] = 'Margic Eintrag angelegt';
$string['evententryupdated'] = 'Margic Eintrag aktualisiert';
$string['eventfeedbackupdated'] = 'Feedback zu Margic Eintrag aktualisiert';
$string['eventinvalidaccess'] = 'Unberechtigter Zugriff';

// Common
$string['modulename'] = 'Margic';
$string['modulenameplural'] = 'Margics';
$string['modulename_help'] = 'Die Aktivität Margic erlaubt es Nutzerinnen und Nutzern, beliebige Einträge anzulegen und Lehrenden diese zu bewerten und zu annotieren.';
$string['pluginadministration'] = 'Margic Administration';

// General errors
$string['erraccessdenied'] = 'Zugang verweigert';
$string['generalerrorinsert'] = 'Speichern des neuen Margic Eintrags fehlgeschlagen.';
$string['incorrectcourseid'] = 'Inkorrekte Kurs-ID';
$string['incorrectmodule'] = 'Inkorrekte Kurs-Modul-ID';

// Entry (template)
$string['entry'] = 'Eintrag';
$string['editthisentry'] = 'Diesen Eintrag bearbeiten';
$string['blankentry'] = 'Leerer Eintrag';
$string['created'] = 'vor {$a->years} Jahren, {$a->month} Monaten, {$a->days} Tagen und {$a->hours} Stunden';
$string['details'] = 'Statistik';
$string['numwordsraw'] = '{$a->wordscount} Wörter mit {$a->charscount} Zeichen, einschließlich {$a->spacescount} Leerzeichen.';
$string['lastedited'] = 'Zuletzt bearbeitet';
$string['needsgrading'] = 'Dieser Eintrag hat noch keine Rückmeldung oder Bewertung erhalten.';
$string['needsregrading'] = 'Dieser Eintrag hat sich geändert, seit das Feedback oder die Bewertung abgegeben wurde.';
$string['getallentriesofuser'] = 'Alle Margic Enträge dieses Benutzers anzeigen';

// View (and template)
$string['viewentries'] = 'Einträge ansehen';
$string['startnewentry'] = 'Neuer Eintrag';
$string['viewannotations'] = 'Annotationen ansehen';
$string['hideannotations'] = 'Annotationen verstecken';
$string['entries'] = 'Einträge';
$string['annotations'] = 'Annotationen';
$string['csvexport'] = 'Exportieren als .csv Datei';
$string['pagesize'] = 'Einträge pro Seite';
$string['editingstarts'] = 'Der Bearbeitungszeitraum beginnt am {$a}';
$string['editingends'] = 'Der Bearbeitungszeitraum endet am {$a}';
$string['editingended'] = 'Der Bearbeitungszeitraum endete am {$a}';
$string['notstarted'] = 'Sie haben noch keine Margic Einträge angelegt';
$string['noentriesfound'] = 'Keine Einträge gefunden';
$string['viewallentries'] = 'Alle Einträge ansehen';

// Annotations
$string['annotationadded'] = 'Annotation hinzugefügt';
$string['annotationedited'] = 'Annotation geändert';
$string['annotationdeleted'] = 'Annotation gelöscht';
$string['annotationinvalid'] = 'Annotation ungültig';

// mod_form
$string['margicname'] = 'Name der Margic';
$string['margicdescription'] = 'Beschreibung des Margics';
$string['margicopentime'] = 'Startzeit';
$string['margicopentime_help'] = 'Wenn aktiviert können Sie das Datum festlegen, ab dem Einträge im Margic erstellt werden können.';
$string['margicclosetime'] = 'Endzeitpunkt';
$string['margicclosetime_help'] = 'Wenn aktiviert können Sie ein Datum festlegen, bis zu dem Einträge im Margic anlegen oder bearbeitet werden können.';

// edit_form
$string['margicentrydate'] = 'Datum für diesen Eintrag festlegen';

// grading_form
$string['gradeingradebook'] = 'Aktuelle Bewertung aus der Bewertungsübersicht';
$string['feedbackingradebook'] = 'Aktuelles Feedback aus der Bewertungsübersicht';
$string['savedrating'] = 'Gespeicherte Bewertung für diesen Eintrag';
$string['newrating'] = 'Neue Bewertung für diesen Eintrag';

// Calendar
$string['calendarend'] = '{$a} schließt';
$string['calendarstart'] = '{$a} öffnet';

// csv export
$string['pluginname'] = 'Margic';
$string['userid'] = 'Nutzer-ID';
$string['timecreated'] = 'Zeitpunkt der Erstellung';
$string['timemodified'] = 'Zeitpunkt der Bearbeitung';
$string['text'] = 'Text';
$string['entrycomment'] = 'Feedback zum Eintrag';
$string['format'] = 'Format';
$string['teacher'] = 'Trainer/in';
$string['timemarked'] = 'Zeitpunkt der Bewertung';
$string['exportfilenamemyentries'] = 'Meine_Margic_Eintraege';
$string['exportfilenamemargicentries'] = 'Margic_Eintraege';
$string['exportfilenameallentries'] = 'Alle_Margic_Eintraege';

// Capabilities.
$string['margic:addentries'] = 'Margic Einträge hinzufügen';
$string['margic:addinstance'] = 'Margic Instanzen hinzufügen';
$string['margic:manageentries'] = 'Margic Einträge verwalten';
$string['margic:rate'] = 'Margic Einträge bewerten';
$string['margic:receivegradingmessages'] = 'Nachrichten über die Bewertung von Einträgen erhalten';
$string['margic:editdefaulterrortypes'] = 'Standardfehlertyp Vorlagen bearbeiten';
$string['margic:viewannotations'] = 'Annotationen ansehen';
$string['margic:makeannotations'] = 'Annotationen anlegen';

// Recent activity
$string['newmargicentries'] = 'Neue Margic Einträge';

// User complete
$string['noentry'] = 'Kein Eintrag';

// Search
$string['search'] = 'Suche';
$string['search:activity'] = 'Margic - Informationen zur Aktivität';
$string['search:entry'] = 'Margic-Einträge';
$string['search:entrycomment'] = 'Kommentar zum Margic-Eintrag';

$string['myentries'] = 'Meine Einträge';
$string['forallentries'] = 'für alle Einträge von';
$string['forallmyentries'] = 'für alle meine Einträge';
$string['togglegradingform'] = 'Bewerten';
$string['norating'] = 'Bewertung deaktiviert.';
$string['viewallmargics'] = 'Alle Margics im Kurs anzeigen';
$string['startoreditentry'] = 'Eintrag anlegen oder bearbeiten';
$string['addnewentry'] = 'Neuen Eintrag anlegen';
$string['editentry'] = 'Eintrag bearbeiten';
$string['editentrynotpossible'] = 'Bearbeiten des Eintrags nicht möglich.';
$string['editdateinfuture'] = 'Das angegebene Erstelldatum des Eintrags liegt in der Zukunft.';
$string['currenttooldest'] = 'Zeige die Einträge vom Aktuellsten zum Ältesten';
$string['oldesttocurrent'] = 'Zeige die Einträge vom Ältesten zum Aktuellsten';
$string['lowestgradetohighest'] = 'Zeige die Einträge vom am niedrigsten Bewerteten zum Höchsten';
$string['highestgradetolowest'] = 'Zeige die Einträge vom am höchsten Bewerteten zum Niedrigsten';
$string['sorting'] = 'Sortierung';
$string['currententry'] = 'Aktuelle Einträge';
$string['oldestentry'] = 'Älteste Einträge';
$string['lowestgradeentry'] = 'Am niedrigsten bewertete Einträge';
$string['highestgradeentry'] = 'Am höchsten bewertete Beiträge';

$string['grammar_verb'] = 'Grammatik: Verbform';
$string['grammar_syntax'] = 'Grammatik: Satzbau';
$string['grammar_congruence'] = 'Grammatik: Kongruenz';
$string['grammar_other'] = 'Grammatik: Sonstiges';
$string['expression'] = 'Ausdruck';
$string['orthography'] = 'Orthographie';
$string['punctuation'] = 'Interpunktion';
$string['other'] = 'Sonstiges';

$string['annotationssummary'] = 'Fehlerauswertung';
$string['participant'] = 'TeilnehmerIn';
$string['backtooverview'] = 'Zurück zur Übersicht';
$string['adderrortype'] = 'Fehlertyp anlegen';
$string['errortypeadded'] = 'Fehlertyp angelegt';
$string['editerrortype'] = 'Fehlertyp bearbeiten';
$string['errortypeedited'] = 'Fehlertyp bearbeitet';
$string['editerrortypetemplate'] = 'Vorlage bearbeiten';
$string['errortypecantbeedited'] = 'Fehlertyp konnte nicht geändert werden';
$string['deleteerrortype'] = 'Fehlertyp entfernen';
$string['errortypedeleted'] = 'Fehlertyp entfernt';
$string['deleteerrortypetemplate'] = 'Vorlage löschen';
$string['errortypeinvalid'] = 'Fehlertyp ungültig';
$string['nameoferrortype'] = 'Name des Fehlertyps';
$string['annotationcreated'] = 'Erstellt am {$a}';
$string['annotationmodified'] = 'Bearbeitet am {$a}';
$string['editannotation'] = 'Bearbeiten';
$string['deleteannotation'] = 'Löschen';
$string['annotationcolor'] = 'Farbe des Fehlertyps';
$string['standardtype'] = 'Standard Fehlertyp';
$string['manualtype'] = 'Manueller Fehlertyp';
$string['standard'] = 'Standard';
$string['custom'] = 'Benutzerdefiniert';
$string['type'] = 'Art';
$string['color'] = 'Farbe';
$string['errnohexcolor'] = 'Kein hexadezimaler Farbwert.';
$string['changetype'] = 'Die Änderung des Namens oder der Farbe des Fehlertypen wirkt sich nur auf die Vorlage aus und wird daher erst bei der Erstellung neuer Margics wirksam. Die Fehlertypen in bestehenden Margics sind von diesen Änderungen nicht betroffen.';
$string['explanationtypename'] = 'Name des Fehlertyps';
$string['explanationtypename_help'] = 'Der Name des Fehlertypen. Für folgende Standardfehlertypen sind bereits Übersetzungen in Moodle hinterlegt: "grammar_verb", "grammar_syntax", "grammar_congruence", "grammar_other", "expression", "orthography", "punctuation" und "other". Alle anderen Namen werden nicht übersetzt.';
$string['explanationhexcolor'] = 'Farbe des Fehlertyps';
$string['explanationhexcolor_help'] = 'Die Farbe des Fehlertypen als Hexadezimalwert. Dieser besteht aus genau 6 Zeichen (A-F sowie 0-9) und repräsentiert eine Farbe. Den Hexwert von beliebigen Farben kann man z. B. unter https://www.w3schools.com/colors/colors_picker.asp herausfinden.';
$string['explanationstandardtype'] = 'Hier kann ausgewählt werden, ob der Fehlertyp ein Standardtyp sein soll. In diesem Fall kann er von allen Lehrenden für ihre Margics ausgewählt und dann in diesen verwendet werden. Andernfalls kann er nur von Ihnen selbst in Ihren Margics verwendet werden.';
$string['annotatedtextnotfound'] = 'Annotierter Text nicht gefunden';
$string['annotatedtextinvalid'] = 'Der ursprünglich annotierte Text ist (z. B. durch eine nachträgliche Änderung des ursprünglichen Beitrags) ungültig geworden. Die Markierung für diese Annotation muss deshalb neu gesetzt werden.';
$string['notallowedtodothis'] = 'Vorgang nicht möglich.';
$string['deletederrortype'] = 'Gelöschter Typ';
$string['errtypedeleted'] = 'Fehlertyp nicht vorhanden.';
$string['grader'] = 'Bewerter';
$string['feedbackupdated'] = 'Rückmeldung und / oder Note aktualisiert';
$string['errfeedbacknotupdated'] = 'Rückmeldung und Note konnte nicht aktualisiert werden';
$string['errnograder'] = 'Kein Bewerter.';
$string['errnofeedbackorratingdisabled'] = 'Keine Rückmeldung oder Rückmeldung ist deaktiviert.';
$string['annotationareawidth_help'] = 'Die Breite des Annotationsbereichs in Prozent.';
$string['errannotationareawidthinvalid'] = 'Breite ungültig (Minimum: {$a->minwidth}, Maximum: {$a->maxwidth}).';
$string['toggleannotation'] = 'Annotation aus- / einklappen';
$string['toggleallannotations'] = 'Alle Annotation aus- / einklappen';
$string['entryaddedoredited'] = 'Eintrag angelegt oder bearbeitet';
$string['deletealluserdata'] = 'Alle Einträge, deren Annotationen, Dateien und Bewertungen löschen';
$string['alluserdatadeleted'] = 'Alle Einträge, deren Annotationen, Dateien und Bewertungen wurden entfernt';
$string['deleteerrortypes'] = 'Fehlertypen löschen';
$string['errortypesdeleted'] = 'Fehlertypen gelöscht';

$string['margicerrortypes'] = 'Margic Fehlertypen';
$string['errortypetemplates'] = 'Fehlertyp-Vorlagen';
$string['errortypes'] = 'Fehlertypen';
$string['template'] = 'Vorlage';
$string['addtomargic'] = 'Zum Margic hinzufügen';
$string['switchtotemplatetypes'] = 'Zu den Fehlertyp-Vorlagen wechseln';
$string['switchtomargictypes'] = 'Zu den Fehlertypen des Margics wechseln';
$string['notemplatetypes'] = 'Keine Fehlertyp-Vorlagen verfügbar';
$string['movefor'] = 'Weiter vorne anzeigen';
$string['moveback'] = 'Weiter hinten anzeigen';
$string['prioritychanged'] = 'Reihenfolge geändert';
$string['prioritynotchanged'] = 'Reihenfolge konnte nicht geändert werden';
$string['revision'] = 'Überarbeitung';
$string['baseentry'] = 'Originaleintrag';
$string['id'] = 'ID';
$string['overview'] = 'Übersicht';
$string['at'] = 'am';
$string['from'] = 'von';
$string['toggleolderversions'] = 'Ältere Versionen ein- oder ausblenden';
$string['timecreatedinvalid'] = 'Änderung fehlgeschlagen. Es gibt bereits jüngere Versionen dieses Beitrags.';
$string['messageprovider:gradingmessages'] = 'Systemnachrichten bei der Bewertung von Einträgen';
$string['sendgradingmessage'] = 'Ersteller/in des Eintrags sofort über die Bewertung benachrichtigen';
$string['gradingmailsubject'] = 'Feedback zu Margic-Eintrag erhalten';
$string['gradingmailfullmessage'] = 'Hallo {$a->user},
{$a->teacher} hat eine Rückmeldung beziehungsweise Bewertung zu Ihrem Eintrag im Margic {$a->margic} veröffentlicht.
Hier können Sie diese ansehen: {$a->url}';
$string['gradingmailfullmessagehtml'] = 'Hallo {$a->user},<br>
{$a->teacher} hat eine Rückmeldung beziehungsweise Bewertung zu Ihrem Eintrag im Margic <strong>{$a->margic}</strong> veröffentlicht.<br><br>
<a href="{$a->url}"><strong>Hier</strong></a> können Sie diese ansehen.';
$string['mailfooter'] = 'Diese Nachricht bezieht sich auf ein Margic in {$a->systemname}. Unter dem folgenden Link finden Sie alle weiteren Informationen. <br> {$a->coursename} -> Margic -> {$a->name} <br> {$a->url}';
$string['hoverannotation'] = 'Annotation hervorheben';
$string['entryadded'] = 'Eintrag angelegt';

// Admin settings.
$string['editentries'] = 'Eigene Einträge bearbeiten';
$string['editentries_help'] = 'Wenn aktiviert können Lehrende in jedem Margic festlegen, ob Nutzer/innen ihre eigenen Einträge bearbeiten können.';
$string['editentrydates'] = 'Eintragsdatum bearbeiten';
$string['editentrydates_help'] = 'Wenn aktiviert können Lehrende in jedem Margic festlegen, ob Nutzer/innen das Datum jedes neuen Eintrags bearbeiten können.';
$string['annotationareawidth'] = 'Breite des Annotationsbereichs';
$string['annotationareawidthall'] = 'Die Breite des Annotationsbereichs in Prozent für alle Margics. Kann von Lehrenden in den einzelnen Margics überschrieben werden.';
$string['editability'] = 'Bearbeitbarkeit';
$string['entrybgc_title'] = 'Hintergrundfarbe für die Einträge und Annotationen';
$string['entrybgc_descr'] = 'Hier kann die Hintergrundfarbe der Bereiche für die Einträge und Annotationen festgelegt werden.';
$string['entrybgc_colour'] = '#C8E5FD';
$string['textbgc_title'] = 'Hintergrundfarbe der Texte';
$string['textbgc_descr'] = 'Hier kann die Hintergrundfarbe der Texte in den Einträgen und Annotationen festgelegt werden.';
$string['textbgc_colour'] = '#F9F5F0';

// Privacy.
$string['privacy:metadata:margic_entries'] = 'Enthält die gespeicherten Benutzereinträge aller Margics.';
$string['privacy:metadata:margic_annotations'] = 'Enthält die in allen Margics gemacht Annotationen.';
$string['privacy:metadata:margic_errortype_templates'] = 'Enthält die von Lehrenden angelegten Fehlertyp-Vorlagen.';
$string['privacy:metadata:margic_entries:margic'] = 'ID des Margic, zu dem der Eintrag gehört.';
$string['privacy:metadata:margic_entries:userid'] = 'ID des Benutzers, zu dem der Eintrag gehört.';
$string['privacy:metadata:margic_entries:timecreated'] = 'Datum, an dem der Eintrag erstellt wurde.';
$string['privacy:metadata:margic_entries:timemodified'] = 'Zeitpunkt der letzten Änderung des Eintrags.';
$string['privacy:metadata:margic_entries:text'] = 'Der Inhalt des Eintrags.';
$string['privacy:metadata:margic_entries:rating'] = 'Die Note, mit der der Eintrag bewertet wurde.';
$string['privacy:metadata:margic_entries:entrycomment'] = 'Der Kommentar des Lehrers zu diesem Eintrag.';
$string['privacy:metadata:margic_entries:teacher'] = 'ID der Bewerterin oder des Bewerters.';
$string['privacy:metadata:margic_entries:timemarked'] = 'Zeitpunkt der Bewertung.';
$string['privacy:metadata:margic_entries:baseentry'] = 'Die ID des Originaleintrags auf dem dieser überarbeitete Eintrag basiert.';
$string['privacy:metadata:margic_annotations:margic'] = 'ID des Margics, zu dem der annotierte Eintrag gehört.';
$string['privacy:metadata:margic_annotations:entry'] = 'ID des Eintrags, zu dem die Annotation gehört.';
$string['privacy:metadata:margic_annotations:userid'] = 'ID des Benutzers, der die Annotation angelegt hat.';
$string['privacy:metadata:margic_annotations:timecreated'] = 'Datum, an dem die Annotation erstellt wurde.';
$string['privacy:metadata:margic_annotations:timemodified'] = 'Zeitpunkt der letzten Änderung der Annotation.';
$string['privacy:metadata:margic_annotations:type'] = 'ID des Typs der Annotation.';
$string['privacy:metadata:margic_annotations:text'] = 'Inhalt der Annotation.';
$string['privacy:metadata:margic_errortype_templates:timecreated'] = 'Datum, an dem die Fehlertyp-Vorlage erstellt wurde.';
$string['privacy:metadata:margic_errortype_templates:timemodified'] = 'Zeitpunkt der letzten Änderung der Fehlertyp-Vorlage.';
$string['privacy:metadata:margic_errortype_templates:name'] = 'Name der Fehlertyp-Vorlage.';
$string['privacy:metadata:margic_errortype_templates:color'] = 'Farbe der Fehlertyp-Vorlage als Hex-Wert.';
$string['privacy:metadata:margic_errortype_templates:userid'] = 'ID des Benutzers, der die Fehlertyp-Vorlage erstellt hat.';
$string['privacy:metadata:core_rating'] = 'Es werden zu Margic-Einträgen hinzugefügte Bewertungen gespeichert.';
$string['privacy:metadata:core_files'] = 'Es werden mit Margic-Einträgen verknüpfte Dateien gespeichert.';
$string['privacy:metadata:core_message'] = 'Es werden Nachrichten über die Bewertung von Margic-Einträgen an Benutzer versendet.';
$string['privacy:metadata:preference:margic_sortoption'] = 'Die Präferenz für die Sortierung des Margics.';
$string['privacy:metadata:preference:margic_pagecount'] = 'Die Anzahl der Einträge, die pro Seite in einem Margic angezeigt werden sollen.';
$string['privacy:metadata:preference:margic_activepage'] = 'Die Nummer der zuletzt geöffneten Seite im Margic.';

// löschen
// $string['numwordscln'] = '{$a->one} bereinigte Wörter mit {$a->two} Zeichen, AUSSCHLIEßLICH {$a->three} Leerzeichen. ';
// $string['numwordsstd'] = '{$a->one} standardisierte Wörter mit {$a->two} Zeichen, einschließlich {$a->three} Leerzeichen. ';
// $string['edittopoflist'] = 'Den Anfang der Liste bearbeiten';
// $string['reload'] = 'Neuladen und Anzeigen vom aktuellsten zum ältesten Margic-Eintrag';
// $string['sortfirstentry'] = 'Vom ersten Margic-Eintrag bis zum letzten.';
// $string['outof'] = 'aus {$a} Einträgen.';
// $string['sortcurrententry'] = 'Vom aktuellen Margic-Eintrag bis zum ersten.';
// $string['sorthighestentry'] = 'Vom am höchsten bewerteten Margic-Eintrag bis zum am niedrigsten bewerteten.';
// $string['sortlastentry'] = 'Vom zuletzt geänderten Margic-Eintrag bis zum ältesten geänderten.';
// $string['sortlowestentry'] = 'Vom am niedrigsten bewerteten Margic-Eintrag bis zum höchsten.';
