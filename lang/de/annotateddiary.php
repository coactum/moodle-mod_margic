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
 * Strings for component 'diary', language 'de', version '3.9'.
 *
 * @package     diary
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['accessdenied'] = 'Zugang verweigert';
$string['alias'] = 'Schlagwort';
$string['aliases'] = 'Schlagwörter';
$string['aliases_help'] = 'Jedem Tagebucheintrag kann eine Liste an Schlagwörtern oder Aliasnamen zugeordnet werden. Verwenden Sie für jedes Schlagwort eine neue Zeile (nicht getrennt durch Kommata)';
$string['alwaysopen'] = 'Immer geöffnet';
$string['attachment'] = 'Anhang';
$string['attachment_help'] = 'Sie können auch Dateien an einen Tagebucheintrag anhängen.';
$string['blankentry'] = 'Leerer Eintrag';
$string['calendarend'] = '{$a} schließt';
$string['calendarstart'] = '{$a} öffnet';
$string['configdateformat'] = 'Damit wird festgelegt, wie Daten in Tagebuchberichten angezeigt werden. Der Standardwert "M d, Y G:i" ist Monat, Tag, Jahr und Uhrzeit im 24-Stunden-Format. Weitere Beispiele und vordefinierte Datumskonstanten finden Sie unter Datum im PHP-Handbuch.';
$string['created'] = 'Erstellt vor {$a->one} Tagen und {$a->two}  Stunden.';
$string['csvexport'] = 'Exportieren nach .csv';
$string['currententry'] = 'Aktuelle Tagebucheinträge:';
$string['dateformat'] = 'Standard-Datumsformat';
$string['daysavailable'] = 'Verfügbare Tage';
$string['daysavailable_help'] = 'Wenn Sie das Wochenformat verwenden, können Sie einstellen, wie viele Tage das Tagebuch für die Verwendung geöffnet ist.';
$string['deadline'] = 'Offene Tage';
$string['details'] = 'Details:';
$string['diary:addentries'] = 'Tagebucheinträge hinzufügen';
$string['diary:addinstance'] = 'Tagebuchinstanzen hinzufügen';
$string['diary:manageentries'] = 'Tagebucheinträge verwalten';
$string['diary:rate'] = 'Tagebucheinträge bewerten';
$string['diaryclosetime'] = 'Endzeit';
$string['diaryclosetime_help'] = 'Wenn diese Option aktiviert ist, können Sie ein Datum festlegen, an dem das Tagebuch geschlossen wird und nicht mehr verwendet werden kann.';
$string['diarydescription'] = 'Beschreibung des Tagebuchs';
$string['diaryentrydate'] = 'Datum für diesen Eintrag festlegen';
$string['diarymail'] = 'Hallo {$a->user},
{$a->teacher} hat einige Rückmeldungen zu Ihrem Tagebucheintrag für \'{$a->Tagebuch}\' veröffentlicht.

Sie können diese als Anhang zu Ihrem Tagebucheintrag sehen:

    {$a->url}';
$string['diarymailhtml'] = 'Hallo {$a->user},
{$a->teacher} hat einige Rückmeldungen zu Ihrem
Tagebucheintrag für \'<i>{$a->diary}</i>\' veröffentlicht.<br /><br />
Sie können diese als Anhang zu Ihrem <a href="{$a->url}">Tagebucheintrag sehen >/a>.';
$string['diaryname'] = 'Name des Tagebuchs';
$string['diaryopentime'] = 'Startzeit';
$string['diaryopentime_help'] = 'Wenn diese Option aktiviert ist, können Sie ein Datum festlegen, an dem das Tagebuch zur Verwendung geöffnet wird.';
$string['editall'] = 'Alle Einträge bearbeiten';
$string['editall_help'] = 'Wenn aktiviert, können Nutzer/innen alle Einträge bearbeiten.';
$string['editdates'] = 'Eintragsdatum bearbeiten';
$string['editdates_help'] = 'Wenn aktiviert, können Nutzer/innen das Datum jedes Eintrags bearbeiten.';
$string['editingended'] = 'Die Bearbeitungszeit ist beendet';
$string['editingends'] = 'Bearbeitungszeitraum endet';
$string['editthisentry'] = 'Diesen Eintrag bearbeiten';
$string['edittopoflist'] = 'Den Anfang der Liste bearbeiten';
$string['entries'] = 'Einträge';
$string['entry'] = 'Eintrag';
$string['entrybgc_colour'] = '#C8E5FD';
$string['entrybgc_descr'] = 'Hier wird die Hintergrundfarbe eines Tagebucheintrages bzw. eines Feedbacks festgelegt.';
$string['entrybgc_title'] = 'Hintergrundfarbe für Tagebucheinträge und Feedback';
$string['entrycomment'] = 'Kommentar zum Eintrag';
$string['entrytextbgc_colour'] = '#F9F5F0';
$string['entrytextbgc_descr'] = 'Hiermit wird die Hintergrundfarbe des Textes in einem Tagebucheintrag festgelegt.';
$string['entrytextbgc_title'] = 'Hintergrundfarbe des Tagebuchtextes';
$string['eventdiarycreated'] = 'Tagebuch erstellt';
$string['eventdiarydeleted'] = 'Tagebuch gelöscht';
$string['eventdiaryviewed'] = 'Tagebuch angezeigt';
$string['eventdownloadentriess'] = 'Tagebucheinträge herunterladen';
$string['evententriesviewed'] = 'Tagebucheinträge angezeigt';
$string['evententrycreated'] = 'Tagebucheintrag erstellt';
$string['evententryupdated'] = 'Tagebucheintrag aktualisiert';
$string['eventfeedbackupdated'] = 'Feedback zum Tagebuch aktualisiert';
$string['exportfilename'] = 'eintraege.csv';
$string['exportfilenamep1'] = 'Alle_Seite';
$string['exportfilenamep2'] = '_Tagebuch_Einträge_exportiert_Ein_';
$string['feedbackupdated'] = 'Rückmeldungen für {$a}-Einträge aktualisiert';
$string['firstentry'] = 'Erste Tagebucheinträge:';
$string['format'] = 'Format';
$string['gradeingradebook'] = 'Aktuelle Bewertung in der Bewertungsübersicht';
$string['highestgradeentry'] = 'Am höchsten bewertete Beiträge:';
$string['lastnameasc'] = 'Nachname aufsteigend:';
$string['lastnamedesc'] = 'Nachname absteigend:';
$string['latestmodifiedentry'] = 'Zuletzt geänderte Einträge:';
$string['lowestgradeentry'] = 'Am niedrigsten bewertete Einträge:';
$string['mailed'] = 'Benachrichtigt';
$string['mailsubject'] = 'Rückmeldung zum Tagebuch';
$string['modulename'] = 'Annotiertes Tagebuch';
$string['modulename_help'] = 'Die Tagebuchaktivität ermöglicht es Trainer/innen, über einen bestimmten Zeitraum hinweg von den Teilnehmer/innen ein Feedback zu bekommen.';
$string['modulenameplural'] = 'Annotierte Tagebücher';
$string['needsgrading'] = 'Dieser Eintrag hat noch keine Rückmeldung oder Bewertung erhalten.';
$string['needsregrade'] = 'Dieser Eintrag hat sich geändert, seit ein Feedback oder eine Bewertung abgegeben wurde.';
$string['newdiaryentries'] = 'Neue Tagebucheinträge';
$string['nextentry'] = 'Nächster Eintrag';
$string['nodeadline'] = 'Immer offen';
$string['noentriesmanagers'] = 'Keine Trainer/innen';
$string['noentry'] = 'Kein Eintrag';
$string['noratinggiven'] = 'Keine Bewertung abgegeben';
$string['notopenuntil'] = 'Dieses Tagebuch ist nicht geöffnet bis';
$string['notstarted'] = 'Sie haben dieses Tagebuch noch nicht begonnen';
$string['outof'] = 'aus {$a} Einträgen.';
$string['overallrating'] = 'Gesamtbewertung';
$string['pagesize'] = 'Einträge pro Seite';
$string['pluginadministration'] = 'Administration zum Tagebuch';
$string['pluginname'] = 'Tagebuch';
$string['previousentry'] = 'Vorheriger Eintrag';
$string['rate'] = 'Bewerten';
$string['rating'] = 'Bewertung für diesen Eintrag';
$string['reload'] = 'Neuladen und Anzeigen vom aktuellsten zum ältesten Tagebucheintrag';
$string['removeentries'] = 'Alle Einträge entfernen';
$string['removemessages'] = 'Alle Tagebucheinträge entfernen';
$string['reportsingle'] = 'Alle Tagebucheinträge dieser Person anzeigen.';
$string['reportsingleallentries'] = 'Alle Tagebucheinträge dieser Person.';
$string['returnto'] = 'Zurück zu {$a}';
$string['returntoreport'] = 'Zurück zur Übersicht von {$a}';
$string['saveallfeedback'] = 'Mein Feedback speichern';
$string['savesettings'] = 'Einstellungen speichern';
$string['search'] = 'Suche';
$string['search:activity'] = 'Tagebuch - Informationen zur Aktivität';
$string['search:entry'] = 'Tagebucheinträge';
$string['search:entrycomment'] = 'Kommentar zum Tagebucheintrag';
$string['selectentry'] = 'Eintrag zur Kennzeichnung auswählen';
$string['showoverview'] = 'Tagebuchübersicht im Dashboard';
$string['showrecentactivity'] = 'Aktuelle Aktivität anzeigen';
$string['sortcurrententry'] = 'Vom aktuellen Tagebucheintrag bis zum ersten.';
$string['sortfirstentry'] = 'Vom ersten Tagebucheintrag bis zum letzten.';
$string['sorthighestentry'] = 'Vom am höchsten bewerteten Tagebucheintrag bis zum am niedrigsten bewerteten.';
$string['sortlastentry'] = 'Vom zuletzt geänderten Tagebucheintrag bis zum ältesten geänderten.';
$string['sortlowestentry'] = 'Vom am niedrigsten bewerteten Tagebucheintrag bis zum höchsten.';
$string['sortoptions'] = 'Einstellungen zur Sortierung:';
$string['sortorder'] = '<h5>Sortierreihenfolge ist: </h5>';
$string['startoredit'] = 'Neuen Eintrag beginnen oder den heutigen Eintrag bearbeiten';
$string['teacher'] = 'Trainer/in';
$string['text'] = 'Text';
$string['timecreated'] = 'Zeitpunkt der Erstellung';
$string['timemarked'] = 'Zeitpunkt der Markierung';
$string['timemodified'] = 'Zeitpunkt der Bearbeitung';
$string['toolbar'] = 'Symbolleiste:';
$string['userid'] = 'Nutzer-ID';
$string['usertoolbar'] = 'Werkzeuge:';
$string['viewalldiaries'] = 'Alle Kurstagebücher anzeigen';
$string['viewallentries'] = 'Anzeigen von {$a} Tagebucheinträgen';

$string['startnewentry'] = 'Neuen Eintrag schreiben';
$string['viewentries'] = 'Einträge ansehen';
$string['numwordsraw'] = '{$a->one} rohe Wörter mit  {$a->two} Zeichen, einschließlich {$a->three} Leerzeichen. ';
$string['numwordscln'] = '{$a->one} bereinigte Wörter mit {$a->two} Zeichen, AUSSCHLIEßLICH {$a->three} Leerzeichen. ';
$string['numwordsstd'] = '{$a->one} standardisierte Wörter mit {$a->two} Zeichen, einschließlich {$a->three} Leerzeichen. ';
$string['annotateddiaryentrydate'] = 'Datum des Eintrages bestimmen';

$string['annotateddiary:viewannotations'] = 'Annotierungen ansehen';
$string['annotateddiary:makeannotations'] = 'Annotierungen anlegen';
$string['annotations'] = 'Annotierungen';
$string['viewannotations'] = 'Annotierungen ansehen';
$string['hideannotations'] = 'Annotierungen verstecken';
$string['annotationadded'] = 'Annotierung hinzugefügt';
$string['annotationedited'] = 'Annotierung geändert';
$string['annotationdeleted'] = 'Annotierung gelöscht';
$string['annotationinvalid'] = 'Annotierung ungültig';