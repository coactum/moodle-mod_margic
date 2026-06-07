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
 * Strings for component 'margic', language 'de'.
 *
 * @package     mod_margic
 * @category    string
 * @copyright   2022 coactum GmbH
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['adderrortype'] = 'Fehlertyp anlegen';
$string['addnewentry'] = 'Neuen Eintrag anlegen';
$string['addtomargic'] = 'Zum Margic hinzufügen';
$string['alluserdatadeleted'] = 'Alle Einträge, deren Annotationen, Dateien und Bewertungen wurden entfernt';
$string['annotatedtextinvalid'] = 'Der ursprünglich annotierte Text ist ungültig geworden. Die Markierung für diese Annotation muss deshalb neu gesetzt werden.';
$string['annotatedtextnotfound'] = 'Annotierter Text nicht gefunden';
$string['annotationadded'] = 'Annotation hinzugefügt';
$string['annotationareawidth'] = 'Breite des Annotationsbereichs';
$string['annotationareawidth_help'] = 'Die Breite des Annotationsbereichs in Prozent. Mindestens 20 und maximal 80 Prozent.';
$string['annotationareawidthall'] = 'Die Breite des Annotationsbereichs in Prozent für alle Margics. Kann von Lehrenden in den einzelnen Margics überschrieben werden. Minimal 20 and maximal 80 Prozent.';
$string['annotationcolor'] = 'Farbe des Fehlertyps';
$string['annotationcreated'] = 'Erstellt am {$a}';
$string['annotationdeleted'] = 'Annotation gelöscht';
$string['annotationedited'] = 'Annotation geändert';
$string['annotationinvalid'] = 'Annotation ungültig';
$string['annotationmodified'] = 'Bearbeitet am {$a}';
$string['annotations'] = 'Annotationen';
$string['annotationsarefetched'] = 'Annotationen werden geladen';
$string['at'] = 'am';
$string['backtooverview'] = 'Zurück zur Übersicht';
$string['baseentry'] = 'Originaleintrag';
$string['blankentry'] = 'Leerer Eintrag';
$string['calendarend'] = '{$a} schließt';
$string['calendarstart'] = '{$a} öffnet';
$string['changetemplate'] = 'Die Änderung des Namens oder der Farbe des Fehlertypen wirkt sich nur auf die Vorlage aus und wird daher erst bei der Erstellung neuer Margics wirksam. Die Fehlertypen in bestehenden Margics sind von diesen Änderungen nicht betroffen.';
$string['color'] = 'Farbe';
$string['created'] = 'vor {$a->years} Jahren, {$a->month} Monaten, {$a->days} Tagen und {$a->hours} Stunden';
$string['csvexport'] = 'Exportieren als .csv Datei';
$string['currententry'] = 'Aktuelle Einträge';
$string['currenttooldest'] = 'Zeige die Einträge vom Aktuellsten zum Ältesten';
$string['custom'] = 'Benutzerdefiniert';
$string['defaulterrortypetemplateseditable'] = 'Bearbeitung der Standard Fehlertyp-Vorlagen';
$string['defaulterrortypetemplateseditable_help'] = 'Wenn aktiviert können Administratoren oder Benutzer mit der Berechtigung editdefaulterrortypes die Standard Fehlertyp-Vorlagen in einem Margic auf der Seite der Fehlerauswertung bearbeiten. Das Bearbeiten einer Vorlage ändert sie im gesamten System, sodass beim Erstellen neuer Margics die geänderte Vorlage bei der Auswahl der konkreten Fehlertypen angezeigt wird. Bestehende konkrete Margic Fehlertypen werden durch das Ändern einer Vorlage nicht verändert. Wird diese Option deaktiviert können Fehlertyp-Vorlagen nicht geändert werden.';
$string['defaultforsendgradingmessage'] = 'Standardwert für die Benachrichtigung bei Feedback';
$string['defaultforsendgradingmessage_help'] = 'Hier kann der Standardwert für die Benachrichtigung bei Feedback eingestellt werden. Dieser wird im Bewertungsformular vorausgefüllt, kann dort aber bei jeder Bewertung auch manuell verändert werden.';
$string['deletealluserdata'] = 'Alle Einträge, deren Annotationen, Dateien und Bewertungen löschen';
$string['deleteannotation'] = 'Löschen';
$string['deletederrortype'] = 'Gelöschter Typ';
$string['deleteerrortype'] = 'Fehlertyp entfernen';
$string['deleteerrortypeconfirm'] = 'Soll dieser Fehlertyp wirklich gelöscht werden? Dadurch wird er aus dem Margic entfernt und bei bestehenden Annotationen als Gelöschter Typ angezeigt. Diese Aktion kann nicht rückgängig gemacht werden!';
$string['deleteerrortypes'] = 'Fehlertypen löschen';
$string['deleteerrortypetemplate'] = 'Vorlage löschen';
$string['deleteerrortypetemplateconfirm'] = 'Soll diese Fehlertyp-Vorlage wirklich gelöscht werden? Dadurch wird die Vorlage für das gesamte System gelöscht und kann nicht mehr in neuen Margics als konkreter Fehlertyp ausgewählt werden. Diese Aktion kann nicht rückgängig gemacht werden!';
$string['details'] = 'Statistik';
$string['editability'] = 'Bearbeitbarkeit';
$string['editannotation'] = 'Bearbeiten';
$string['editdateinfuture'] = 'Das angegebene Erstelldatum des Eintrags liegt in der Zukunft.';
$string['editentries'] = 'Eigene Einträge bearbeiten';
$string['editentries_help'] = 'Wenn aktiviert können Lehrende in jedem Margic festlegen, ob Nutzer/innen ihre eigenen Einträge bearbeiten können.';
$string['editentry'] = 'Eintrag bearbeiten';
$string['editentrydates'] = 'Eintragsdatum bearbeiten';
$string['editentrydates_help'] = 'Wenn aktiviert können Lehrende in jedem Margic festlegen, ob Nutzer/innen das Datum jedes neuen Eintrags bearbeiten können.';
$string['editentrynotpossible'] = 'Bearbeiten des Eintrags nicht möglich.';
$string['editerrortype'] = 'Fehlertyp bearbeiten';
$string['editerrortypetemplate'] = 'Vorlage bearbeiten';
$string['editingended'] = 'Der Bearbeitungszeitraum endete am {$a}';
$string['editingends'] = 'Der Bearbeitungszeitraum endet am {$a}';
$string['editingstarts'] = 'Der Bearbeitungszeitraum beginnt am {$a}';
$string['editthisentry'] = 'Diesen Eintrag bearbeiten';
$string['entries'] = 'Einträge';
$string['entry'] = 'Eintrag';
$string['entryadded'] = 'Eintrag angelegt';
$string['entryaddedoredited'] = 'Eintrag angelegt oder bearbeitet.';
$string['entrybgc_descr'] = 'Hier kann die Hintergrundfarbe der Bereiche für die Einträge und Annotationen festgelegt werden.';
$string['entrybgc_title'] = 'Hintergrundfarbe für die Einträge und Annotationen';
$string['erraccessdenied'] = 'Zugang verweigert';
$string['errannotationareawidthinvalid'] = 'Breite ungültig (Minimum: {$a->minwidth}, Maximum: {$a->maxwidth}).';
$string['errfeedbacknotupdated'] = 'Rückmeldung und Note konnte nicht aktualisiert werden';
$string['errnofeedbackorratingdisabled'] = 'Keine Rückmeldung oder Bewertung ist deaktiviert.';
$string['errnograder'] = 'Kein Bewerter.';
$string['errnohexcolor'] = 'Kein hexadezimaler Farbwert.';
$string['errorsummary'] = 'Fehlerauswertung';
$string['errortypeadded'] = 'Fehlertyp angelegt';
$string['errortypecantbeedited'] = 'Fehlertyp konnte nicht geändert werden';
$string['errortypedeleted'] = 'Fehlertyp entfernt';
$string['errortypeedited'] = 'Fehlertyp bearbeitet';
$string['errortypeinvalid'] = 'Fehlertyp ungültig';
$string['errortypes'] = 'Fehlertypen';
$string['errortypesdeleted'] = 'Fehlertypen gelöscht';
$string['errortypetemplates'] = 'Fehlertyp-Vorlagen';
$string['errtypedeleted'] = 'Fehlertyp nicht vorhanden.';
$string['eventannotationcreated'] = 'Margic Annotation angelegt';
$string['eventannotationdeleted'] = 'Margic Annotation gelöscht';
$string['eventannotationupdated'] = 'Margic Annotation aktualisiert';
$string['eventdownloadentries'] = 'Margic Einträge herunterladen';
$string['evententrycreated'] = 'Margic Eintrag angelegt';
$string['evententryupdated'] = 'Margic Eintrag aktualisiert';
$string['eventfeedbackupdated'] = 'Feedback zu Margic Eintrag aktualisiert';
$string['eventinvalidaccess'] = 'Unberechtigter Zugriff';
$string['explanationhexcolor'] = 'Farbe des Fehlertyps';
$string['explanationhexcolor_help'] = 'Die Farbe des Fehlertypen als Hexadezimalwert. Dieser besteht aus genau 6 Zeichen (A-F sowie 0-9) und repräsentiert eine Farbe. Den Hexwert von beliebigen Farben kann man z. B. unter <a href="https://www.w3schools.com/colors/colors_picker.asp" target="_blank">https://www.w3schools.com/colors/colors_picker.asp</a> herausfinden.';
$string['explanationstandardtype'] = 'Hier kann ausgewählt werden, ob der Fehlertyp ein Standardtyp sein soll. In diesem Fall kann er von allen Lehrenden für ihre Margics ausgewählt und dann in diesen verwendet werden. Andernfalls kann er nur von Ihnen selbst in Ihren Margics verwendet werden.';
$string['explanationtypename'] = 'Name des Fehlertyps';
$string['explanationtypename_help'] = 'Der Name des Fehlertypen. Für folgende Standardfehlertypen sind bereits Übersetzungen in Moodle hinterlegt: "grammar_verb", "grammar_syntax", "grammar_congruence", "grammar_other", "expression", "orthography", "punctuation" und "other". Alle anderen Namen werden nicht übersetzt.';
$string['exportfilenameallentries'] = 'Alle_Margic_Eintraege';
$string['exportfilenamemargicentries'] = 'Margic_Eintraege';
$string['exportfilenamemyentries'] = 'Meine_Margic_Eintraege';
$string['expression'] = 'Ausdruck';
$string['feedback'] = 'Feedback zum Eintrag';
$string['feedbackingradebook'] = 'Aktuelles Feedback aus der Bewertungsübersicht';
$string['feedbackupdated'] = 'Rückmeldung und / oder Note aktualisiert';
$string['forallentries'] = 'für alle Einträge von';
$string['forallmyentries'] = 'für alle meine Einträge';
$string['format'] = 'Format';
$string['from'] = 'von';
$string['generalerrorinsert'] = 'Speichern des neuen Margic Eintrags fehlgeschlagen.';
$string['getallentriesofuser'] = 'Alle Margic Enträge dieses Benutzers anzeigen';
$string['gradeingradebook'] = 'Aktuelle Bewertung aus der Bewertungsübersicht';
$string['grader'] = 'Bewerter';
$string['gradingmailfullmessage'] = 'Hallo {$a->user},
{$a->teacher} hat eine Rückmeldung beziehungsweise Bewertung zu Ihrem Eintrag im Margic {$a->margic} veröffentlicht.
Hier können Sie diese ansehen: {$a->url}';
$string['gradingmailfullmessagehtml'] = 'Hallo {$a->user},<br>
{$a->teacher} hat eine Rückmeldung beziehungsweise Bewertung zu Ihrem Eintrag im Margic <strong>{$a->margic}</strong> veröffentlicht.<br><br>
<a href="{$a->url}"><strong>Hier</strong></a> können Sie diese ansehen.';
$string['gradingmailsubject'] = 'Feedback zu Margic-Eintrag erhalten';
$string['grammar_congruence'] = 'Grammatik: Kongruenz';
$string['grammar_other'] = 'Grammatik: Sonstiges';
$string['grammar_syntax'] = 'Grammatik: Satzbau';
$string['grammar_verb'] = 'Grammatik: Verbform';
$string['hideannotations'] = 'Annotationen verstecken';
$string['highestgradeentry'] = 'Am höchsten bewertete Einträge';
$string['highestgradetolowest'] = 'Zeige die Einträge vom am höchsten Bewerteten zum Niedrigsten';
$string['hoverannotation'] = 'Annotation hervorheben';
$string['id'] = 'ID';
$string['incorrectcourseid'] = 'Inkorrekte Kurs-ID';
$string['incorrectmodule'] = 'Inkorrekte Kurs-Modul-ID';
$string['lastedited'] = 'Zuletzt bearbeitet';
$string['lowestgradeentry'] = 'Am niedrigsten bewertete Einträge';
$string['lowestgradetohighest'] = 'Zeige die Einträge vom am niedrigsten Bewerteten zum Höchsten';
$string['mailfooter'] = 'Diese Nachricht bezieht sich auf ein Margic in {$a->systemname}. Unter dem folgenden Link finden Sie alle weiteren Informationen. <br> {$a->coursename} -> Margic -> {$a->name} <br> {$a->url}';
$string['manualtype'] = 'Manueller Fehlertyp';
$string['margic:addentries'] = 'Margic Einträge hinzufügen';
$string['margic:addinstance'] = 'Margic Instanzen hinzufügen';
$string['margic:deleteannotations'] = 'Annotationen löschen';
$string['margic:editdefaulterrortypes'] = 'Standardfehlertyp Vorlagen bearbeiten';
$string['margic:makeannotations'] = 'Annotationen anlegen';
$string['margic:manageentries'] = 'Margic Einträge verwalten';
$string['margic:manageerrortypes'] = 'Margic Fehlertypen verwalten';
$string['margic:rate'] = 'Margic Einträge bewerten';
$string['margic:receivegradingmessages'] = 'Nachrichten über die Bewertung von Einträgen erhalten';
$string['margic:viewannotations'] = 'Annotationen ansehen';
$string['margic:viewerrorsfromallparticipants'] = 'Fehler aller Teilnehmerinnen ansehen';
$string['margic:viewerrorsummary'] = 'Margic Fehlerauswertung ansehen';
$string['margic:viewotherusersannotationtimes'] = 'Zeitpunkt der Erstellung fremder Annotationen ansehen';
$string['margic:viewotherusersentrytimes'] = 'Zeitpunkt der Erstellung fremder Einträge ansehen';
$string['margic:viewotherusersfeedbacktimes'] = 'Zeitpunkt der Bewertung durch andere Lehrende ansehen';
$string['margicclosetime'] = 'Endzeitpunkt';
$string['margicclosetime_help'] = 'Wenn aktiviert können Sie ein Datum festlegen, bis zu dem Einträge im Margic anlegen oder bearbeitet werden können.';
$string['margicdescription'] = 'Beschreibung des Margics';
$string['margicentrydate'] = 'Datum für diesen Eintrag festlegen';
$string['margicerrortypes'] = 'Margic Fehlertypen';
$string['margicname'] = 'Name des Margic';
$string['margicopentime'] = 'Startzeit';
$string['margicopentime_help'] = 'Wenn aktiviert können Sie das Datum festlegen, ab dem Einträge im Margic erstellt werden können.';
$string['messageprovider:gradingmessages'] = 'Systemnachrichten bei der Bewertung von Einträgen';
$string['modulename'] = 'Margic';
$string['modulename_help'] = 'In der Aktivität Margic können Teilnehmerinnen und Teilnehmer unbeschränkt Einträge anlegen welche dann von Lehrenden bewertet und annotiert werden können.

Sinnvoll einsetzen lassen sich Margics zum Beispiel im Sprachunterricht. Dort können Teilnehmende etwa Einträge zur Beantwortung von variablen Aufgabenstellungen verfassen, eigene Texte und Geschichten schreiben oder aber Vokabeln üben.

Lehrende können diese Einträge dann auf einer individualisierbaren Übersichtsseite ansehen, korrigieren und bewerten. Dazu können sie konkrete Textstellen markieren und Annotationen zu diesen verfassen, wobei für jede Annotation ein Fehlertyp sowie ein kurzer Text hinterlegt werden kann. Es kann außerdem der komplette Eintrag benotet und mit einem textuellen oder akustischen Feedback versehen werden. Teilnehmende haben danach die Möglichkeit, ihren Ursprungseintrag zu überarbeiten und das erhaltene Feedback so zur Verbesserung zu nutzen.

Die verfügbaren Fehlertypen für die Annotationen können dabei flexibel angepasst werden. In einer Fehlerzusammenfassung können Lehrende zudem für jeden Teilnehmenden auswerten, wie viele und welche Fehler dieser in einem Margic gemacht hat. Schließlich besteht auch die Möglichkeit, die verfassten Einträge für die Weiterverwendung zu exportieren.

Kernfeatures des Plugins:

* Verfassen und Überarbeiten von multimedialen Einträgen
* Individuell anpassbare Übersichtsseite mit allen im Margic vorhandenen (eigenen) Einträgen
* Umfangreiche Möglichkeiten zur Annotierung und Bewertung von Einträgen für Lehrende
* Anpassbare Fehlertypen und genaue Fehlerauswertung';
$string['modulename_link'] = 'mod/margic/view';
$string['modulenameplural'] = 'Margics';
$string['moveback'] = 'Weiter hinten anzeigen';
$string['movefor'] = 'Weiter vorne anzeigen';
$string['myentries'] = 'Meine Einträge';
$string['nameoferrortype'] = 'Name des Fehlertyps';
$string['needsgrading'] = 'Dieser Eintrag hat noch keine Rückmeldung oder Bewertung erhalten.';
$string['needsregrading'] = 'Dieser Eintrag hat sich geändert, seit das Feedback oder die Bewertung abgegeben wurde.';
$string['newmargicentries'] = 'Neue Margic Einträge';
$string['newrating'] = 'Neue Bewertung für diesen Eintrag';
$string['noentriesfound'] = 'Keine Einträge gefunden';
$string['noentry'] = 'Kein Eintrag';
$string['norating'] = 'Bewertung deaktiviert.';
$string['notallowedtodothis'] = 'Vorgang nicht möglich.';
$string['notemplatetypes'] = 'Keine Fehlertyp-Vorlagen verfügbar';
$string['notstarted'] = 'Sie haben noch keine Margic Einträge angelegt';
$string['numwordsraw'] = '{$a->wordscount} Wörter mit {$a->charscount} Zeichen, einschließlich {$a->spacescount} Leerzeichen.';
$string['oldestentry'] = 'Älteste Einträge';
$string['oldesttocurrent'] = 'Zeige die Einträge vom Ältesten zum Aktuellsten';
$string['orthography'] = 'Orthographie';
$string['other'] = 'Sonstiges';
$string['overview'] = 'Übersicht';
$string['overwriteannotations'] = 'Annotationen überschreiben';
$string['overwriteannotations_help'] = 'Hier kann festgelegt werden, ob Lehrende die Annotationen anderer Lehrender überschreiben sowie löschen dürfen';
$string['pagesize'] = 'Einträge pro Seite';
$string['participant'] = 'TeilnehmerIn';
$string['pluginadministration'] = 'Margic Administration';
$string['pluginname'] = 'Margic';
$string['prioritychanged'] = 'Reihenfolge geändert';
$string['prioritynotchanged'] = 'Reihenfolge konnte nicht geändert werden';
$string['privacy:metadata:core_files'] = 'Es werden mit Margic Einträgen verknüpfte Dateien gespeichert.';
$string['privacy:metadata:core_message'] = 'Es werden Nachrichten über die Bewertung von Margic Einträgen an Benutzer versendet.';
$string['privacy:metadata:core_rating'] = 'Es werden zu Margic Einträgen hinzugefügte Bewertungen gespeichert.';
$string['privacy:metadata:margic_annotations'] = 'Enthält die in allen Margics gemacht Annotationen.';
$string['privacy:metadata:margic_annotations:entry'] = 'ID des Eintrags, zu dem die Annotation gehört.';
$string['privacy:metadata:margic_annotations:margic'] = 'ID des Margics, zu dem der annotierte Eintrag gehört.';
$string['privacy:metadata:margic_annotations:text'] = 'Inhalt der Annotation.';
$string['privacy:metadata:margic_annotations:timecreated'] = 'Datum, an dem die Annotation erstellt wurde.';
$string['privacy:metadata:margic_annotations:timemodified'] = 'Zeitpunkt der letzten Änderung der Annotation.';
$string['privacy:metadata:margic_annotations:type'] = 'ID des Typs der Annotation.';
$string['privacy:metadata:margic_annotations:userid'] = 'ID des Benutzers, der die Annotation angelegt hat.';
$string['privacy:metadata:margic_entries'] = 'Enthält die gespeicherten Benutzereinträge aller Margics.';
$string['privacy:metadata:margic_entries:baseentry'] = 'Die ID des Originaleintrags auf dem dieser überarbeitete Eintrag basiert.';
$string['privacy:metadata:margic_entries:feedback'] = 'Das Feedback des Lehrers zu diesem Eintrag.';
$string['privacy:metadata:margic_entries:margic'] = 'ID des Margic, zu dem der Eintrag gehört.';
$string['privacy:metadata:margic_entries:rating'] = 'Die Note, mit der der Eintrag bewertet wurde.';
$string['privacy:metadata:margic_entries:teacher'] = 'ID der Bewerterin oder des Bewerters.';
$string['privacy:metadata:margic_entries:text'] = 'Der Inhalt des Eintrags.';
$string['privacy:metadata:margic_entries:timecreated'] = 'Datum, an dem der Eintrag erstellt wurde.';
$string['privacy:metadata:margic_entries:timemarked'] = 'Zeitpunkt der Bewertung.';
$string['privacy:metadata:margic_entries:timemodified'] = 'Zeitpunkt der letzten Änderung des Eintrags.';
$string['privacy:metadata:margic_entries:userid'] = 'ID des Benutzers, zu dem der Eintrag gehört.';
$string['privacy:metadata:margic_errortype_templates'] = 'Enthält die von Lehrenden angelegten Fehlertyp-Vorlagen.';
$string['privacy:metadata:margic_errortype_templates:color'] = 'Farbe der Fehlertyp-Vorlage als Hex-Wert.';
$string['privacy:metadata:margic_errortype_templates:name'] = 'Name der Fehlertyp-Vorlage.';
$string['privacy:metadata:margic_errortype_templates:timecreated'] = 'Datum, an dem die Fehlertyp-Vorlage erstellt wurde.';
$string['privacy:metadata:margic_errortype_templates:timemodified'] = 'Zeitpunkt der letzten Änderung der Fehlertyp-Vorlage.';
$string['privacy:metadata:margic_errortype_templates:userid'] = 'ID des Benutzers, der die Fehlertyp-Vorlage erstellt hat.';
$string['privacy:metadata:preference:margic_activepage'] = 'Die Nummer der zuletzt geöffneten Seite im Margic.';
$string['privacy:metadata:preference:margic_pagecount'] = 'Die Anzahl der Einträge, die pro Seite in einem Margic angezeigt werden sollen.';
$string['privacy:metadata:preference:margic_sortoption'] = 'Die Präferenz für die Sortierung des Margics.';
$string['punctuation'] = 'Interpunktion';
$string['rating'] = 'Bewertung';
$string['reloadannotations'] = 'Annotationen neu laden';
$string['revision'] = 'Überarbeitung';
$string['savedrating'] = 'Gespeicherte Bewertung für diesen Eintrag';
$string['search'] = 'Suche';
$string['search:activity'] = 'Margic - Informationen zur Aktivität';
$string['search:entry'] = 'Margic Einträge';
$string['search:feedback'] = 'Feedback zum Margic Eintrag';
$string['sendgradingmessage'] = 'Ersteller/in des Eintrags sofort über die Bewertung benachrichtigen';
$string['sendgradingmessagedefault'] = 'Ersteller/innen von Einträgen über Bewertung informieren';
$string['sendgradingmessagedefault_help'] = 'Legt den Standardwert für die Bewertungs-Formulare in allen Margics fest. Bestimmt, ob die Ersteller/innen von Einträgen benachrichtigt werden sollen, wenn Lehrende einen Eintrag bewerten. Kann in jedem Margic oder im Bewertungsformular selbst geändert werden.';
$string['sorting'] = 'Sortierung';
$string['standard'] = 'Standard';
$string['standardtype'] = 'Standard Fehlertyp';
$string['startnewentry'] = 'Neuer Eintrag';
$string['switchtomargictypes'] = 'Zu den Fehlertypen des Margics wechseln';
$string['switchtotemplatetypes'] = 'Zu den Fehlertyp-Vorlagen wechseln';
$string['teacher'] = 'Trainer/in';
$string['template'] = 'Vorlage';
$string['text'] = 'Text';
$string['textbgc_descr'] = 'Hier kann die Hintergrundfarbe der Texte in den Einträgen und Annotationen festgelegt werden.';
$string['textbgc_title'] = 'Hintergrundfarbe der Texte';
$string['timecreated'] = 'Zeitpunkt der Erstellung';
$string['timecreatedinvalid'] = 'Änderung fehlgeschlagen. Es gibt bereits jüngere Versionen dieses Beitrags.';
$string['timemarked'] = 'Zeitpunkt der Bewertung';
$string['timemodified'] = 'Zeitpunkt der Bearbeitung';
$string['toggleallannotations'] = 'Alle Annotation aus- / einklappen';
$string['toggleannotation'] = 'Annotation aus- / einklappen';
$string['togglegradingform'] = 'Bewerten';
$string['toggleolderversions'] = 'Ältere Versionen ein- oder ausblenden';
$string['type'] = 'Art';
$string['userid'] = 'Nutzer-ID';
$string['viewallentries'] = 'Alle Einträge ansehen';
$string['viewannotations'] = 'Annotationen ansehen';
$string['viewentries'] = 'Einträge ansehen';
$string['warningeditdefaulterrortypetemplate'] = 'WARNUNG: Hierdurch wird die Fehlertyp-Vorlage systemweit geändert. Bei der Erstellung neuer Margics wird dann bei der Auswahl der konkreten Fehlertypen die geänderte Vorlage zur Verfügung stehen.';
