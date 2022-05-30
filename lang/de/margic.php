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

$string['accessdenied'] = 'Zugang verweigert';
$string['alias'] = 'Schlagwort';
$string['aliases'] = 'Schlagwörter';
$string['aliases_help'] = 'Jedem Margic-Eintrag kann eine Liste an Schlagwörtern oder Aliasnamen zugeordnet werden. Verwenden Sie für jedes Schlagwort eine neue Zeile (nicht getrennt durch Kommata)';
$string['alwaysopen'] = 'Immer geöffnet';
$string['attachment'] = 'Anhang';
$string['attachment_help'] = 'Sie können auch Dateien an einen Margic-Eintrag anhängen.';
$string['blankentry'] = 'Leerer Eintrag';
$string['calendarend'] = '{$a} schließt';
$string['calendarstart'] = '{$a} öffnet';
$string['configdateformat'] = 'Damit wird festgelegt, wie Daten in Margic-Berichten angezeigt werden. Der Standardwert "M d, Y G:i" ist Monat, Tag, Jahr und Uhrzeit im 24-Stunden-Format. Weitere Beispiele und vordefinierte Datumskonstanten finden Sie unter Datum im PHP-Handbuch.';
$string['created'] = 'Erstellt vor {$a->days} Tagen und {$a->hours} Stunden.';
$string['csvexport'] = 'Exportieren nach .csv';
$string['dateformat'] = 'Standard-Datumsformat';
$string['daysavailable'] = 'Verfügbare Tage';
$string['daysavailable_help'] = 'Wenn Sie das Wochenformat verwenden, können Sie einstellen, wie viele Tage die Margic-Instanz für die Verwendung geöffnet ist.';
$string['deadline'] = 'Offene Tage';
$string['details'] = 'Details';
$string['margic:addentries'] = 'Margic-Einträge hinzufügen';
$string['margic:addinstance'] = 'Margic-Instanzen hinzufügen';
$string['margic:manageentries'] = 'Margic-Einträge verwalten';
$string['margic:rate'] = 'Margic-Einträge bewerten';
$string['margicclosetime'] = 'Endzeit';
$string['margicclosetime_help'] = 'Wenn diese Option aktiviert ist, können Sie ein Datum festlegen, an dem die Margic-Instanz geschlossen wird und nicht mehr verwendet werden kann.';
$string['margicdescription'] = 'Beschreibung der Margic-Instanz';
$string['margicentrydate'] = 'Datum für diesen Eintrag festlegen';
$string['margicmail'] = 'Hallo {$a->user},
{$a->teacher} hat einige Rückmeldungen zu Ihrem Margic-Eintrag für \'{$a->margic}\' veröffentlicht.

Sie können diese als Anhang zu Ihrem Margic-Eintrag sehen:

    {$a->url}';
$string['margicmailhtml'] = 'Hallo {$a->user},
{$a->teacher} hat einige Rückmeldungen zu Ihrem
Margic-Eintrag für \'<i>{$a->margic}</i>\' veröffentlicht.<br /><br />
Sie können diese als Anhang zu Ihrem <a href="{$a->url}">Margic-Eintrag sehen >/a>.';
$string['margicname'] = 'Name der Margic-Instanz';
$string['margicdescription'] = 'Beschreibung der Margic-Instanz';
$string['margicopentime'] = 'Startzeit';
$string['margicopentime_help'] = 'Wenn diese Option aktiviert ist, können Sie ein Datum festlegen, an dem die Margic-Instanz zur Verwendung geöffnet wird.';
$string['editall'] = 'Alle Einträge bearbeiten';
$string['editall_help'] = 'Wenn aktiviert, können Nutzer/innen alle Einträge bearbeiten.';
$string['editdates'] = 'Eintragsdatum bearbeiten';
$string['editdates_help'] = 'Wenn aktiviert, können Nutzer/innen das Datum jedes Eintrags bearbeiten.';
$string['editingended'] = 'Die Bearbeitungszeit ist beendet';
$string['editingends'] = 'Bearbeitungszeitraum endet';
$string['editthisentry'] = 'Diesen Eintrag bearbeiten';
$string['entries'] = 'Einträge';
$string['entry'] = 'Eintrag';
$string['entrybgc_colour'] = '#C8E5FD';
$string['entrybgc_descr'] = 'Hier wird die Hintergrundfarbe eines Margic-Eintrages bzw. eines Feedbacks festgelegt.';
$string['entrybgc_title'] = 'Hintergrundfarbe für Margic-Einträge und Feedback';
$string['entrycomment'] = 'Kommentar zum Eintrag';
$string['entrytextbgc_colour'] = '#F9F5F0';
$string['entrytextbgc_descr'] = 'Hiermit wird die Hintergrundfarbe des Textes in einem Margic-Eintrag festgelegt.';
$string['entrytextbgc_title'] = 'Hintergrundfarbe des Textes';
$string['eventmargiccreated'] = 'Margic erstellt';
$string['eventmargicdeleted'] = 'Margic gelöscht';
$string['eventmargicviewed'] = 'Margic angezeigt';
$string['eventdownloadentriess'] = 'Margic-Einträge herunterladen';
$string['evententriesviewed'] = 'Margic-Einträge angezeigt';
$string['evententrycreated'] = 'Margic-Eintrag erstellt';
$string['evententryupdated'] = 'Margic-Eintrag aktualisiert';
$string['eventfeedbackupdated'] = 'Feedback zur Margic-Instanz aktualisiert';
$string['exportfilenamemyentries'] = 'Meine_Margic_Eintraege';
$string['exportfilenamemargicentries'] = 'Margic_Eintraege';
$string['exportfilenameallentries'] = 'Alle_Margic_Einträge';
$string['feedbackupdated'] = 'Rückmeldungen für {$a}-Einträge aktualisiert';
$string['format'] = 'Format';
$string['gradeingradebook'] = 'Aktuelle Bewertung in der Bewertungsübersicht';
$string['lastnameasc'] = 'Nachname aufsteigend:';
$string['lastnamedesc'] = 'Nachname absteigend:';
$string['mailed'] = 'Benachrichtigt';
$string['mailsubject'] = 'Rückmeldung zur Margic-Instanz';
$string['modulename'] = 'Margic';
$string['modulename_help'] = 'Die Margic-Instanz kann tolle Dinge ...';
$string['modulenameplural'] = 'Margics';
$string['needsgrading'] = 'Dieser Eintrag hat noch keine Rückmeldung oder Bewertung erhalten.';
$string['needsregrading'] = 'Dieser Eintrag hat sich geändert, seit ein Feedback oder eine Bewertung abgegeben wurde.';
$string['newmargicentries'] = 'Neue Margic-Einträge';
$string['nextentry'] = 'Nächster Eintrag';
$string['nodeadline'] = 'Immer offen';
$string['noentriesmanagers'] = 'Keine Trainer/innen';
$string['noentry'] = 'Kein Eintrag';
$string['notopenuntil'] = 'Diese Margic-Instanz ist nicht geöffnet bis';
$string['notstarted'] = 'Sie haben diese Margic-Instanz noch nicht begonnen';
$string['overallrating'] = 'Gesamtbewertung';
$string['pagesize'] = 'Einträge pro Seite';
$string['pluginadministration'] = 'Administration zu Margic';
$string['pluginname'] = 'Margic';
$string['previousentry'] = 'Vorheriger Eintrag';
$string['rate'] = 'Bewerten';
$string['rating'] = 'Bewertung für diesen Eintrag';
$string['removeentries'] = 'Alle Einträge entfernen';
$string['removemessages'] = 'Alle Margic-Einträge entfernen';
$string['reportsingle'] = 'Alle Margic-Einträge dieser Person anzeigen.';
$string['reportsingleallentries'] = 'Alle Margic-Einträge dieser Person.';
$string['returnto'] = 'Zurück zu {$a}';
$string['returntoreport'] = 'Zurück zur Übersicht von {$a}';
$string['saveallfeedback'] = 'Mein Feedback speichern';
$string['savesettings'] = 'Einstellungen speichern';
$string['search'] = 'Suche';
$string['search:activity'] = 'Margic - Informationen zur Aktivität';
$string['search:entry'] = 'Margic-Einträge';
$string['search:entrycomment'] = 'Kommentar zum Margic-Eintrag';
$string['selectentry'] = 'Eintrag zur Kennzeichnung auswählen';
$string['showoverview'] = 'Margic-Übersicht im Dashboard';
$string['showrecentactivity'] = 'Aktuelle Aktivität anzeigen';
$string['sortcurrententry'] = 'Vom aktuellen Margic-Eintrag bis zum ersten.';
$string['sorthighestentry'] = 'Vom am höchsten bewerteten Margic-Eintrag bis zum am niedrigsten bewerteten.';
$string['sortlastentry'] = 'Vom zuletzt geänderten Margic-Eintrag bis zum ältesten geänderten.';
$string['sortlowestentry'] = 'Vom am niedrigsten bewerteten Margic-Eintrag bis zum höchsten.';
$string['sortoptions'] = 'Einstellungen zur Sortierung:';
$string['sortorder'] = '<h5>Sortierreihenfolge ist: </h5>';
$string['teacher'] = 'Trainer/in';
$string['text'] = 'Text';
$string['timecreated'] = 'Zeitpunkt der Erstellung';
$string['timemarked'] = 'Zeitpunkt der Bewertung';
$string['timemodified'] = 'Zeitpunkt der Bearbeitung';
$string['toolbar'] = 'Symbolleiste:';
$string['userid'] = 'Nutzer-ID';
$string['usertoolbar'] = 'Werkzeuge:';
$string['viewallentries'] = 'Anzeigen von {$a} Margic-Einträgen';

$string['startnewentry'] = 'Neuen Eintrag schreiben';
$string['viewentries'] = 'Einträge ansehen';
$string['numwordsraw'] = '{$a->wordscount} Wörter mit {$a->charscount} Zeichen, einschließlich {$a->spacescount} Leerzeichen. ';
$string['margicentrydate'] = 'Datum des Eintrages bestimmen';

$string['margic:viewannotations'] = 'Annotierungen ansehen';
$string['margic:makeannotations'] = 'Annotierungen anlegen';
$string['annotations'] = 'Annotierungen';
$string['viewannotations'] = 'Annotierungen ansehen';
$string['viewandmakeannotations'] = 'Annotierungen erstellen und ansehen';
$string['hideannotations'] = 'Annotierungen verstecken';
$string['annotationadded'] = 'Annotierung hinzugefügt';
$string['annotationedited'] = 'Annotierung geändert';
$string['annotationdeleted'] = 'Annotierung gelöscht';
$string['annotationinvalid'] = 'Annotierung ungültig';
$string['noentriesfound'] = 'Keine Einträge gefunden';
$string['lastedited'] = 'Zuletzt bearbeitet';
$string['getallentriesofuser'] = 'Alle Margic Enträge dieses Benutzers anzeigen';
$string['myentries'] = 'Meine Einträge';
$string['forallentries'] = 'für alle Einträge von';
$string['forallmyentries'] = 'für alle meine Einträge';
$string['toggleratingform'] = 'Bewertungsmodus öffnen/schließen';
$string['norating'] = 'Bewertung deaktiviert.';
$string['viewallmargics'] = 'Alle Margics im Kurs anzeigen';
$string['startoreditentry'] = 'Eintrag anlegen oder bearbeiten';
$string['editentrynotpossible'] = 'Bearbeiten des Eintrages nicht möglich.';
$string['entrydateinfuture'] = 'Das Datum der Erstellung des Eintrages kann nicht in der Zukunft liegen.';
$string['currenttooldest'] = 'Zeige die Einträge vom Aktuellsten zum Ältesten';
$string['oldesttocurrent'] = 'Zeige die Einträge vom Ältesten zum Aktuellsten';
$string['lowestgradetohighest'] = 'Zeige die Einträge vom am niedrigsten Bewerteten zum Höchsten';
$string['highestgradetolowest'] = 'Zeige die Einträge vom am höchsten Bewerteten zum Niedrigsten';
$string['lastmodified'] = 'Zeige die zuletzt geänderten Einträge';
$string['sorting'] = 'Sortierung';
$string['currententry'] = 'Aktuelle Einträge';
$string['oldestentry'] = 'Älteste Einträge';
$string['lowestgradeentry'] = 'Am niedrigsten bewertete Einträge';
$string['highestgradeentry'] = 'Am höchsten bewertete Beiträge';
$string['latestmodifiedentry'] = 'Zuletzt geänderte Einträge';
$string['viewallentries'] = 'Alle Einträge ansehen';

$string['grammar_verb'] = 'Grammatik: Verbform';
$string['grammar_syntax'] = 'Grammatik: Satzbau';
$string['grammar_congruence'] = 'Grammatik: Kongruenz';
$string['grammar_other'] = 'Grammatik: Sonstiges';
$string['expression'] = 'Ausdruck';
$string['orthography'] = 'Orthographie';
$string['punctuation'] = 'Interpunktion';
$string['other'] = 'Sonstiges';

$string['annotationssummary'] = 'Annotationsauswertung und Fehlertypen';
$string['participant'] = 'TeilnehmerIn';
$string['backtooverview'] = 'Zurück zur Übersicht';
$string['addannotationtype'] = 'Fehlertypen anlegen';
$string['annotationtypeadded'] = 'Fehlertyp angelegt';
$string['editannotationtype'] = 'Fehlertyp bearbeiten';
$string['annotationtypeedited'] = 'Fehlertyp bearbeitet';
$string['annotationtypecantbeedited'] = 'Fehlertyp konnte nicht geändert werden';
$string['deleteannotationtype'] = 'Fehlertyp entfernen';
$string['annotationtypedeleted'] = 'Fehlertyp entfernt';
$string['annotationtypeinvalid'] = 'Fehlertyp ungültig';
$string['nameofannotationtype'] = 'Name des Fehlertyps';
$string['annotationcreated'] = 'Erstellt';
$string['annotationmodified'] = 'Bearbeitet';
$string['editannotation'] = 'Bearbeiten';
$string['deleteannotation'] = 'Löschen';
$string['annotationcolor'] = 'Farbe des Fehlertyps';
$string['defaulttype'] = 'Standard Fehlertyp';
$string['customtype'] = 'Eigener Fehlertyp';
$string['erremptyannotation'] = 'Text fehlt. Annotierung nicht gespeichert.';
$string['errnohexcolor'] = 'Kein hexadezimaler Farbwert.';
$string['changesforall'] = 'Die Änderung des Namens oder der Farbe des Fehlertypen wirkt sich sofort nach dem Speichern auf alle bereits Angelegten sowie alle zukünftigen Annotationen aus.';
$string['explanationtypename'] = 'Name des Fehlertyps';
$string['explanationtypename_help'] = 'Der Name des Fehlertypen. Für folgende Standardfehlertypen sind bereits Übersetzungen in Moodle hinterlegt: "grammar_verb", "grammar_syntax", "grammar_congruence", "grammar_other", "expression", "orthography", "punctuation" und "other". Alle anderen Namen werden nicht übersetzt.';
$string['explanationhexcolor'] = 'Farbe des Fehlertyps';
$string['explanationhexcolor_help'] = 'Die Farbe des Fehlertypen als Hexadezimalwert. Dieser besteht aus genau 6 Zeichen (A-F sowie 0-9) und repräsentiert eine Farbe. Den Hexwert von beliebigen Farben kann man z. B. unter https://www.w3schools.com/colors/colors_picker.asp herausfinden.';
$string['explanationdefaulttype'] = 'Hier kann ausgewählt werden, ob der Fehlertyp ein Standardtyp sein soll. In diesem Fall wird er allen Lehrenden in allen Margic-Instanzen angezeigt und kann von diesen verwendet werden. Andernfalls wird er ein normaler Fehlertyp und kann nur vom Ersteller verwendet werden.';
$string['annotatedtextnotfound'] = 'Annotierter Text nicht gefunden';
$string['annotatedtextinvalid'] = 'Der ursprünglich annotierte Text ist (z. B. durch eine nachträgliche Änderung des ursprünglichen Beitrags) ungültig geworden. Die Markierung für diese Annotierung muss deshalb neu gesetzt werden.';

// löschen
$string['numwordscln'] = '{$a->one} bereinigte Wörter mit {$a->two} Zeichen, AUSSCHLIEßLICH {$a->three} Leerzeichen. ';
$string['numwordsstd'] = '{$a->one} standardisierte Wörter mit {$a->two} Zeichen, einschließlich {$a->three} Leerzeichen. ';
$string['edittopoflist'] = 'Den Anfang der Liste bearbeiten';
$string['reload'] = 'Neuladen und Anzeigen vom aktuellsten zum ältesten Margic-Eintrag';
$string['sortfirstentry'] = 'Vom ersten Margic-Eintrag bis zum letzten.';
$string['outof'] = 'aus {$a} Einträgen.';
