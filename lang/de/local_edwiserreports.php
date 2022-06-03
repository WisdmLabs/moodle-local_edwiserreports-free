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
 * Plugin strings are defined here.
 *
 * @package     local_edwiserreports
 * @category    string
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Edwiser Reports Frei';
$string['reportsdashboard'] = 'Edwiser Reports Frei';
$string['reportsandanalytics'] = 'Berichte und Analysen kostenlos';
$string['all'] = 'Alles';
$string['refresh'] = 'Aktualisierung';
$string['noaccess'] = 'Es tut uns leid.Sie haben kein Recht, auf diese Seite zuzugreifen.';
$string['showdatafor'] = 'Daten anzeigen für';
$string['dashboard'] = 'Edwiser Reports Kostenloses Dashboard';
$string['permissionwarning'] = 'Sie haben den folgenden Benutzern erlaubt, diesen Block zu sehen, der nicht empfohlen wird.Bitte verstecken Sie diesen Block vor diesen Benutzern.Sobald Sie diesen Block versteckt haben, wird er nicht wieder angezeigt.';
$string['showentries'] = 'Einträge zeigen';

/* Blocks Name */
$string['realtimeusers'] = 'Echtzeit -Benutzer';
$string['activeusersheader'] = 'Site -Übersichtstatus';
$string['courseprogress'] = 'Kurs Fortschritt';
$string['courseprogressheader'] = 'Kurs Fortschritt';
$string['studentengagementheader'] = 'Engagement der Schüler';
$string['gradeheader'] = 'Noten';
$string['learnerheader'] = 'Lernblock';
$string['courseengagement'] = 'Kurs Engagement';
$string['coursereports'] = 'Kursberichte';
$string['coursereportsheader'] = 'Kursberichte';
$string['more'] = '<i class = "fa fa-angle-right"> </i> mehr';
$string['activecoursesheader'] = 'Beliebte Kurse';
$string['f2fsessionsheader'] = 'Ausbilder geführte Sitzungen';
$string['certificatestats'] = 'Zertifikate Statistiken';
$string['certificatestatsheader'] = 'Zertifikate Statistiken';
$string['certificatesheader'] = 'Zertifikate Statistiken';
$string['lpstatsheader'] = 'Lernprogrammstatistiken';
$string['accessinfo'] = 'Site -Zugriffsinformationen';
$string['siteaccessheader'] = 'Site -Zugriffsinformationen';
$string['inactiveusers'] = 'Inaktive Benutzerliste';
$string['inactiveusersheader'] = 'Inaktive Benutzerliste';
$string['liveusersheader'] = 'Live -Benutzer blockieren';
$string['todaysactivityheader'] = 'Tägliche Aktivitäten';
$string['overallengagementheader'] = 'Gesamtbindung in Kurse';
$string['inactiveusersexportheader'] = 'Inaktive Benutzer melden';
$string['inactiveusersblockexportheader'] = 'Inaktive Benutzer melden';
$string['date'] = 'Datum';
$string['time'] = 'Zeit';
$string['venue'] = 'Veranstaltungsort';
$string['signups'] = 'Anmeldungen';
$string['attendees'] = 'Teilnehmerinnen';
$string['name'] = 'name';
$string['course'] = 'Kurs';
$string['issued'] = 'Problematisch';
$string['notissued'] = 'Nicht ausgegeben';
$string['nof2fmodule'] = 'Es gibt keine Angesicht zu Angesichtssitzungen.';
$string['nof2fsessions'] = 'Für dieses Modul steht keine Angesicht zu Angesichtssitzung zur Verfügung.';
$string['nocertificates'] = 'Es wird kein Zertifikat erstellt';
$string['nocertificatesawarded'] = 'Es werden keine Zertifikate vergeben';
$string['unselectall'] = 'Alles wiederufen';
$string['selectall'] = 'Wählen Sie Alle';
$string['activity'] = 'Aktivität';
$string['cohorts'] = 'Kohorten';
$string['nographdata'] = 'Keine Daten';

// Breakdown the tooltip string to display in 2 lines.
$string['cpblocktooltip1'] = '{$a->per} Kurs abgeschlossen';
$string['cpblocktooltip2'] = 'von {$a->val} Benutzern';

$string['lpstatstooltip'] = '{$a->data} Benutzer abgeschlossenhlossen {$a->label}.';
$string['fullname'] = 'Vollständiger Name';
$string['onlinesince'] = 'Online seit';
$string['status'] = 'status';
$string['todayslogin'] = 'Anmeldung';
$string['learners'] = 'Lernende';
$string['teachers'] = 'Lehrer';
$string['eventtoday'] = 'Ereignisse des Tages';
$string['value'] = 'Wert';
$string['count'] = 'Anzahl';
$string['enrollment'] = 'Einschreibungen';
$string['activitycompletion'] = 'Aktivitätsabschluss';
$string['coursecompletion'] = 'Kursabschluss';
$string['newregistration'] = 'Neue Registrierungen';
$string['timespent'] = 'Zeitaufwand';
$string['sessions'] = 'Sitzungen';
$string['totalusers'] = 'Gesamt Benutzer';
$string['sitevisits'] = 'Standortbesuche pro Stunde';
$string['lastupdate'] = 'Letzte aktualisiert <span class="minute"> 0 </span> min';
$string['lastweek'] = 'Letzte Woche';
$string['lastmonth'] = 'Letzten Monat';
$string['lastyear'] = 'Letztes Jahr';
$string['custom'] = 'Benutzerdefiniertes Datum';
$string['rank'] = 'Rang';
$string['enrolments'] = 'Einschreibungen';
$string['visits'] = 'Besuche';
$string['totalvisits'] = 'Gesamtbesuche';
$string['averagevisits'] = 'Durchschnittliche Besuche';
$string['completions'] = 'Fertigstellungen';
$string['selectdate'] = 'Datum auswählen';
$string['never'] = 'Niemals';
$string['before1month'] = 'Vor 1 Monat';
$string['before3month'] = 'Vor 3 Monaten';
$string['before6month'] = 'Vor 6 Monaten';
$string['recipient'] = 'Empfängerin';
$string['duplicateemail'] = 'Doppelte E-Mail';
$string['invalidemail'] = 'Ungültige E-Mail';
$string['subject'] = 'Gegenstand';
$string['message'] = 'Nachricht';
$string['reset'] = 'Zurücksetzen';
$string['send'] = 'Sende jetzt';
$string['editblocksetting'] = 'Blockeinstellung bearbeiten';
$string['editblockcapabilities'] = 'Blockfunktionen bearbeiten';
$string['hour'] = 'Stunde';
$string['hours'] = 'Std.';
$string['minute'] = 'Minute';
$string['minutes'] = 'Protokoll';
$string['second'] = 'zweite';
$string['seconds'] = 'Sekunden';
$string['zerorecords'] = 'Keine übereinstimmenden Aufzeichnungen gefunden';
$string['nodata'] = 'Keine Daten';
$string['tableinfo'] = 'Anzeigen _START_ to _END_ von _TOTAL_ Einträgen';
$string['infoempty'] = '0 bis 0 von 0 Einträgen anzeigen';
$string['allgroups'] = 'Alle Gruppen';
$string['nogroups'] = 'Keine Gruppen';
$string['pleaseselectcourse'] = 'Bitte wählen Sie Kurs, um Gruppen anzusehen';

/* Block help tooltips */
$string['activeusersblocktitlehelp'] = 'Ein Überblick über die täglichen Aktivitäten auf Ihrer Website.Es ist wichtig, dass Manager die Gesamtaktivität auf der Website überprüfen.';
$string['activeusersblockhelp'] = 'In diesem Block wird das Diagramm der aktiven Benutzer über den Zeitraum mit Kursregistrierung und Kursabschluss angezeigt.';
$string['courseprogressblockhelp'] = 'Dieser Block zeigt das Kreisdiagramm eines Kurses mit Prozentsatz.';
$string['activecoursesblockhelp'] = 'Dieser Block zeigt die aktivsten Kurse auf der Grundlage der Anmeldungen und Abschlüsse der Besuche.';
$string['studentengagementblockhelp'] = 'Student Engagement Reports zeigt eine Zeitspanne von Studenten auf Websites, Kursen und den Gesamtbesuchen im Kurs.';
$string['gradeblockhelp'] = 'Dieser Block zeigt Noten.';
$string['learnerblockhelp'] = 'Verfolgen Sie Ihren Kursfortschritt und Ihren Zeitpunkt vor Ort.';
$string['certificatestatsblockhelp'] = 'Dieser Block zeigt alle erstellten benutzerdefinierten Zertifikate und wie viele eingeschriebene Benutzer mit diesen Zertifikaten vergeben werden.';
$string['realtimeusersblockhelp'] = 'Dieser Block zeigt alle angemeldeten Benutzer auf dieser Website.';
$string['f2fsessionsblockhelp'] = 'In diesem Block werden alle von Angesicht zu Angesicht und Zählung aller Anmeldungen und Teilnehmer erstellt.';
$string['accessinfoblockhelp'] = 'Dieser Block zeigt die durchschnittliche Nutzung der Website in einer Woche.';
$string['lpstatsblockhelp'] = 'Dieser Block zeigt alle von den Benutzern in einem Lernprogramm abgeschlossenen Kurs.';
$string['todaysactivityblockhelp'] = 'Dieser Block zeigt die täglichen Aktivitäten, die auf dieser Website durchgeführt werden.';
$string['inactiveusersblockhelp'] = 'In diesem Block wird die Liste der Benutzer inaktiv auf dieser Website angezeigt.';
$string['inactiveusersexporthelp'] = 'In diesem Bericht wird die Inaktivität der Benutzer auf der Website angezeigt';
$string['none'] = 'Keiner';

/* Block Course Progress */
$string['averagecourseprogress'] = 'Durchschnittlicher Kursfortschritt';
$string['nocourses'] = 'Keine Kurse gefunden';
$string['activity'] = 'Aktivität';
$string['activities'] = 'Aktivitäten';
$string['student'] = 'Studentin';
$string['students'] = 'Studentinnen';

/* Block Learning Program */
$string['nolearningprograms'] = 'Keine Lernprogramme gefunden';

/* Block Site access information */
$string['siteaccessinformationtask'] = 'Berechnen Sie Informationen zu Site -Zugriffsinformationen';
$string['siteaccessrecalculate'] = 'Plugin wird gerade aktualisiert.Bitte <a target="_blank" href="{$a}"> rennen </a> <strong> Berechnen Sie die Informationen zur Site -Zugriffszusatz </strong>, um das Ergebnis anzuzeigen.';
$string['siteaccessinformationcronwarning'] = '<strong> Information von Site Access </strong> Aufgabe sollte alle 24 Stunden ausgeführt werden.Bitte <a target="_blank" href="{$a}"> Jetzt ausführen </a>, um genaues Ergebnis zu sehen.';
$string['busiest'] = 'Am meisten beschäftigt';
$string['quietest'] = 'Am wenigsten beschäftigt';

/* Block Inactive Users */
$string['siteaccess'] = 'Website-Zugang:';
$string['before1month'] = 'Vor 1 Monat';
$string['before3month'] = 'Vor 3 Monaten';
$string['before6month'] = 'Vor 6 Monaten';
$string['noinactiveusers'] = 'Es sind keine inaktiven Benutzer verfügbar';

/* Active users block */
$string['activeuserstask'] = 'Berechnen Sie aktive Benutzer blockieren Daten';
$string['averageactiveusers'] = 'Durchschnittliche aktive Benutzer';
$string['totalactiveusers'] = 'Gesamt aktive Benutzer';
$string['searchdate'] = 'Suchdatum';
$string['noactiveusers'] = 'Es gibt keine aktiven Benutzer';
$string['nousers'] = 'Es gibt keine Benutzer';
$string['courseenrolment'] = 'Kursregistrierung';
$string['coursecompletionrate'] = 'Kursabschlussrate';
$string['totalcourseenrolments'] = 'Gesamtkursanmeldungen';
$string['totalcoursecompletions'] = 'Gesamtkursabschluss';

/* Student Engagement block */
$string['studentengagementexportheader'] = 'Student Engagement Report';
$string['studentengagementreportheader'] = 'Student Engagement Report';
$string['visitsonlms'] = 'Besuche vor Ort';
$string['timespentonlms'] = 'Zeit, die vor Ort aufgewendet wurde';
$string['timespentonsite'] = 'Zeit, die vor Ort aufgewendet wurde';
$string['timespentoncourse'] = 'Zeit auf Kurs verbracht';
$string['assignmentsubmitted'] = 'Aufträge eingereicht';
$string['visitsoncourse'] = 'Besuche auf Kurs';
$string['studentengagementtask'] = 'Daten der Schüler Engagement';
$string['searchuser'] = 'Benutzer suchen';
$string['emptytable'] = 'Keine Datensätze zu zeigen';
$string['courseactivitystatus'] = 'Einreichung eingereicht, Aktivitäten abgeschlossen';
$string['courseactivitystatus-submissions'] = 'Aufgabe eingereicht';
$string['courseactivitystatus-completions'] = 'Aktivitäten abgeschlossen';

/* Learner block */
$string['learnerreportexportheader'] = 'Lernbericht';
$string['learnerreportheader'] = 'Lernbericht';
$string['searchcourse'] = 'Suchkurs';
$string['own'] = 'Besitzen';

/* Active Users Page */
$string['noofactiveusers'] = 'Anzahl aktiver Benutzer';
$string['noofenrolledusers'] = 'Anzahl der Einschreibungen';
$string['noofcompletedusers'] = 'Anzahl der Abschlüsse';
$string['email'] = 'email';
$string['emailscheduled'] = 'E -Mail geplant';
$string['usersnotavailable'] = 'Für diesen Tag sind keine Benutzer verfügbar';
$string['activeusersmodaltitle'] = 'Benutzer aktiv auf {$a->date}';
$string['enrolmentsmodaltitle'] = 'Benutzer, die in Kurse unter {$a->date} eingeschrieben sind';
$string['completionsmodaltitle'] = 'Benutzer, die einen Kurs auf {$a->date} abgeschlossen haben';
$string['recordnotfound'] = 'Aufnahme nicht gefunden';
$string['jsondecodefailed'] = 'JSON -Decode ist fehlgeschlagen';
$string['emaildataisnotasarray'] = 'E -Mail -Daten sind kein Array';
$string['sceduledemailnotexist'] = 'Planen Sie E -Mails nicht existieren';
$string['searchdate'] = 'Suchdatum';

/* Active courses block */
$string['activecoursestask'] = 'Berechnen Sie die Daten der aktiven Kurse';

/* Grades block */
$string['gradeblockview'] = 'Blockansicht';
$string['gradeblockeditadvance'] = 'Notenblock bearbeiten';
$string['coursegrades'] = 'Kursnoten';
$string['studentgrades'] = 'Studentennoten';
$string['activitygrades'] = 'Aktivitätsnoten';
$string['averagegrade'] = 'Durchschnittsnote';
// Export header strings.

/* Course Progress Page */
$string['coursename'] = 'Kursname';
$string['enrolled'] = 'Eingeschrieben';
$string['completed'] = 'Abgeschlossen';
$string['inprogress'] = 'In Bearbeitung';
$string['per40-20'] = '20% - 40%';
$string['per60-40'] = '40% - 60%';
$string['per80-60'] = '60% - 80%';
$string['per100-80'] = '80% - 100%';
$string['per20-0'] = '0% - 20%';
$string['per100'] = '100%';

/* Certificates Page */
$string['username'] = 'Nutzername';
$string['useremail'] = 'Benutzer Email';
$string['dateofissue'] = 'Ausgabedatum';
$string['dateofenrol'] = 'Datum der Einschreibung';
$string['grade'] = 'Klasse';
$string['courseprogress'] = 'Kurs Fortschritt';
$string['notenrolled'] = 'Benutzer nicht eingeschrieben';
$string['searchcertificates'] = 'Suchzertifikate';

/* f2f Sessions Block */
$string['attended'] = 'Besucht';
$string['requested'] = 'Angefordert';
$string['canceled'] = 'Annulliert';
$string['approved'] = 'Zugelassen';
$string['booked'] = 'Gebucht';
$string['f2fmore'] = 'Mehr';
$string['download'] = 'download';

/* Site Access Block*/
$string['sun'] = 'SONNE';
$string['mon'] = 'Mon';
$string['tue'] = 'Di';
$string['wed'] = 'HEIRATEN';
$string['thu'] = 'Thu';
$string['fri'] = 'Fr';
$string['sat'] = 'Sa';
$string['time00'] = '12:00 AM';
$string['time01'] = '01:00 AM';
$string['time02'] = '02:00 AM';
$string['time03'] = '03:00 AM';
$string['time04'] = '04:00 AM';
$string['time05'] = '05:00 AM';
$string['time06'] = '06:00 AM';
$string['time07'] = '07:00 AM';
$string['time08'] = '08:00 AM';
$string['time09'] = '09:00 AM';
$string['time10'] = '10:00 AM';
$string['time11'] = '11:00 AM';
$string['time12'] = '12:00 PM';
$string['time13'] = '01:00 PM';
$string['time14'] = '02:00 PM';
$string['time15'] = '03:00 PM';
$string['time16'] = '04:00 PM';
$string['time17'] = '05:00 PM';
$string['time18'] = '06:00 PM';
$string['time19'] = '07:00 PM';
$string['time20'] = '08:00 PM';
$string['time21'] = '09:00 PM';
$string['time22'] = '10:00 PM';
$string['time23'] = '11:00 PM';
$string['siteaccessinfo'] = 'AVG -Benutzer zugreifen';

/* f2f Sessions Page*/
$string['waitlist'] = 'Warteliste';
$string['declined'] = 'Abgelehnt';
$string['reason'] = '(Wenn abgesagt) Grund';
$string['confirmed'] = 'Bestätigt';
$string['nosignups'] = 'Es sind keine Anmeldungen verfügbar';
$string['nosessions'] = 'Es gibt keine Angesicht zu Angesichtssitzungen';

/* Learning Program Page */
$string['lastaccess'] = 'Letzter Zugriff';
$string['progress'] = 'Fortschritt';
$string['avgprogress'] = 'AVG Fortschritt';
$string['notyet'] = 'Noch nicht';
$string['lpname'] = 'Name des Lernprogramms';
$string['lpdetailedreport'] = 'Download Lernprogramme Detaillierter Bericht';
$string['searchlps'] = 'Suche Lernprogramme';
$string['exportlpdetailedreport'] = 'Detaillierter Bericht exportieren';

/* Export Strings */
$string['csv'] = 'csv';
$string['excel'] = 'excel';
$string['pdf'] = 'pdf';
$string['email'] = 'email';
$string['exporttocsv'] = 'Export in CSV';
$string['exporttoexcel'] = 'Nach Excel exportieren';
$string['exporttopdf'] = 'Export in PDF';
$string['sendoveremail'] = 'Per E -Mail senden';
$string['copy'] = 'Kopieren';
$string['activeusers_status'] = 'Benutzer aktiv';
$string['enrolments_status'] = 'Benutzer eingeschrieben';
$string['completions_status'] = 'Kurs abgeschlossen';
$string['completedactivity'] = 'Abgeschlossene Aktivität';
$string['coursecompletedusers'] = 'Kurs von Benutzern abgeschlossen';
$string['emailsent'] = 'E -Mail wurde an Ihr Mail -Konto gesendet';
$string['reportemailhelp'] = 'Der Bericht wird an diese E -Mail -Adresse gesendet.';
$string['emailnotsent'] = 'Versäumnis, E -Mails zu senden';
$string['subject'] = 'Gegenstand';
$string['content'] = 'Inhalt';
$string['emailexample'] = 'example1.mail.com, example2.mail.com;';

$string['activeusersblockexportheader'] = 'Site -Aktivitätsübersicht';
$string['activeusersblockexporthelp'] = 'In diesem Bericht werden aktive Benutzer, Kurseinschreibungen und Kursabschluss im Zeitraum angezeigt.';
$string['courseprogressblockexportheader'] = 'Kursfortschrittsbericht';
$string['courseprogressblockexporthelp'] = 'Dieser Bericht zeigt den Kursfortschritt eines bestimmten Kurses durch die Benutzer.';
$string['activecoursesblockexportheader'] = 'Der aktive Kursbericht';
$string['activecoursesblockexporthelp'] = 'In diesem Bericht werden die aktivsten Kurse auf der Grundlage der Einschreibungen, Besuche und Abschlüsse angezeigt.';
$string['certificatesblockexportheader'] = 'Vergebene Zertifikatbericht';
$string['certificatesblockexporthelp'] = 'In diesem Bericht werden die Zertifikate angezeigt, die an eingeschriebene Benutzer ausgestellt oder nicht ausgestellt wurden.';
$string['f2fsessionblockexportheader'] = 'Lehrer-Sessions-Bericht';
$string['f2fsessionblockexporthelp'] = 'In diesem Bericht werden die von Instruktors geführten Sessions-Details angezeigt.';
$string['lpstatsblockexportheader'] = 'Lernprogrammbericht';
$string['lpstatsblockexporthelp'] = 'In diesem Bericht werden die Details des Lernprogramms angezeigt.';
$string['courseengageblockexportheader'] = 'Kurs -Engagement -Bericht';
$string['courseengageblockexporthelp'] = 'In diesem Bericht wird das Kursbindung durch die Benutzer angezeigt.';
$string['completionblockexportheader'] = 'Kursabschlussbericht';
$string['completionexportheader'] = 'Kursabschlussbericht';
$string['completionblockexporthelp'] = 'In diesem Bericht werden die Kursabschlüsse der Benutzer angezeigt.';
$string['completionexporthelp'] = 'In diesem Bericht werden die Kursabschlüsse der Benutzer angezeigt.';
$string['courseanalyticsblockexportheader'] = 'Kursabschlussbericht';
$string['courseanalyticsblockexporthelp'] = 'In diesem Bericht werden die Kursabschlüsse der Benutzer angezeigt.';
$string['studentengagementblockexportheader'] = 'studentEngagementReport';
$string['gradeblockexportheader'] = 'Zeugnis';
$string['gradeblockexporthelp'] = 'Dieser Bericht zeigt den Schülern Noten.';
$string['studentengagementexporthelp'] = 'Dieser Bericht zeigt die Engagements von Studenten.';
$string['exportlpdetailedreports'] = 'Exportieren Sie detaillierte Berichte';
$string['inactiveusersblockexporthelp'] = 'In diesem Bericht wird die Inaktivität der Benutzer auf der Website angezeigt';

$string['times_0'] = '06:30 AM';
$string['times_1'] = '10:00 AM';
$string['times_2'] = '04:30 PM';
$string['times_3'] = '10:30 PM';
$string['week_0'] = 'Sonntag';
$string['week_1'] = 'Montag';
$string['week_2'] = 'Dienstag';
$string['week_3'] = 'Mittwoch';
$string['week_4'] = 'Donnerstag';
$string['week_5'] = 'Freitag';
$string['week_6'] = 'Samstag';
$string['monthly_0'] = 'Monat Start';
$string['monthly_1'] = 'Monat zwischen';
$string['monthly_2'] = 'Monatsende';
$string['weeks_on'] = 'Wochen später';
$string['emailthisreport'] = 'E -Mail diesen Bericht';
$string['onevery'] = 'auf jeder';
$string['duration_0'] = 'Täglich';
$string['duration_1'] = 'Wöchentlich';
$string['duration_2'] = 'Monatlich';
$string['everydays'] = 'Jeden Tag {$a->time}';
$string['everyweeks'] = 'Jeder {$a->day}';
$string['everymonths'] = 'Jeden Monat bei {$a->time}';
$string['schedule'] = 'E -Mail planen';
$string['downloadreport'] = 'Bericht herunterladen';
$string['scheduledlist'] = 'Alle geplanten Berichte';
$string['reset'] = 'Zurücksetzen';
$string['confirmemailremovaltitle'] = 'Löschen Sie geplante E -Mails';
$string['confirmemailremovalquestion'] = '<p class="px-20">Möchten Sie diese geplante E -Mail wirklich löschen?</p>';

/* Course Engagement Block */
$string['activitystart'] = 'Mindestens eine Aktivität begann';
$string['completedhalf'] = '50% der Kurse abgeschlossen';
$string['coursecompleted'] = 'Kurs abgeschlossen';
$string['nousersavailable'] = 'Keine Benutzer verfügbar';

/* Course Completion Page */
$string['nostudentsenrolled'] = 'Keine Benutzer sind als Schüler eingeschrieben';
$string['completionheader'] = 'Kursabschlussberichte: {$a->coursename}';
$string['completionreports'] = 'Abschlussberichte';
$string['completionpercantage'] = 'Abschlussanteil';
$string['activitycompleted'] = '{$a->completed} aus {$a->total}';

/* Course Analytics Page */
$string['courseanalytics'] = 'Kursanalyse';
$string['courseanalyticsheader'] = 'Kursanalytik: {$a->coursename}';
$string['recentvisits'] = 'Jüngste Besuche';
$string['lastvisit'] = 'Letzter Besuch';
$string['enrolledon'] = 'Eingeschrieben auf';
$string['enrolltype'] = 'Registrierungstyp';
$string['noofvisits'] = 'Anzahl der Besuche';
$string['completiontime'] = 'Vervollständigungszeit';
$string['spenttime'] = 'Verbrachte Zeit';
$string['completedon'] = 'Vervollständigt am';
$string['recentcompletion'] = 'Jüngste Fertigstellung';
$string['recentenrolment'] = 'Jüngste Einschreibungen';
$string['recentvisits'] = 'Jüngste Besuche';
$string['nousersincourse'] = 'Keine Benutzer haben sich für diesen Kurs eingeschrieben';
$string['nouserscompleted'] = 'Keine Benutzer haben diesen Kurs abgeschlossen';
$string['nousersvisited'] = 'Keine Benutzer haben diesen Kurs besucht';

/* Cron Task Strings */
$string['updatetables'] = 'Aktualisieren von Berichten und Analyseetabelle';
$string['updatingrecordstarted'] = 'Aktualisieren von Berichten und Analysedatensatz wird erstellt ...';
$string['updatingrecordended'] = 'Aktualisieren von Berichten und Analysedatensatz wird beendet ...';
$string['updatinguserrecord'] = 'Aktualisieren von userid {$a->userid} in courseid {$a->couseid}';
$string['deletingguserrecord'] = 'Löschen von userid {$a->userid} in coursid {$a->couseid}';
$string['gettinguserrecord'] = 'Userid {$a->userid} in coursid {$a->couseid}';
$string['creatinguserrecord'] = 'Erstellen Sie Datensätze für Benutzerabschlüsse';
$string['sendscheduledemails'] = 'Senden Sie geplante E -Mails';
$string['sendingscheduledemails'] = 'Senden geplanter E -Mails ...';
$string['sending'] = 'Senden';

/* Cache Strings */
$string['cachedef_edwiserReport'] = 'Dies sind die Caches of Edwiser -Berichte';

/* Capabilties */
$string['edwiserReport:view'] = 'Berichte und Analytics Dashboard anzeigen';

/* Custom report block */
$string['downloadcustomtreport'] = 'Download des Benutzers Fortschrittsbericht';
$string['selectdaterange'] = 'Wählen Sie Datumsbereich';
$string['learningprograms'] = 'Lernprogramme';
$string['courses'] = 'Kurse';
$string['shortname'] = 'Kurzer Name';
$string['downloadreportincsv'] = 'Laden Sie Berichte in CSV herunter';
$string['startdate'] = 'Startdatum';
$string['enddate'] = 'Endtermin';
$string['select'] = 'Auswählen';
$string['selectreporttype'] = 'Wählen Sie den Berichtstyp';
$string['completedactivities'] = 'Aktivität abgeschlossen';
$string['completionspercentagecentage'] = 'Fertigstellung(%)';
$string['firstname'] = 'Vorname';
$string['lastname'] = 'Familienname, Nachname';
$string['average'] = 'Durchschnitt(%)';
$string['enrolmentstartdate'] = 'Anmeldung Startdatum';
$string['enrolmentenddate'] = 'Einschreibung Enddatum';
$string['enrolmentrangeselector'] = 'Registrierungsdatum -Reichwahlen ausgewählt';
$string['category'] = 'Kategorie';
$string['customreportselectfailed'] = '<h4><i class="fa fa-check" aria-hidden="true"></i> Gescheitert!</h4>Wählen Sie eines der Kontrollkästchen aus, um Berichte zu erhalten.';
$string['customreportdatefailed'] = '<h4><i class="fa fa-check" aria-hidden="true"></i> Gescheitert!</h4>Wählen Sie gültiges Datum für die Registrierung.';
$string['customreportsuccess'] = '<h4><i class="fa fa-check" aria-hidden="true"></i> Erfolg!</h4>Benachrichtigungen erfolgreich gesendet.';
$string['customreportfailed'] = '<h4><i class="fa fa-check" aria-hidden="true"></i> Gescheitert!</h4>Wählen Sie eines der Kontrollkästchen aus, um Berichte zu erhalten.';
$string['duration'] = 'Dauer';
$string['na'] = 'NA';
$string['activityname'] = 'Aktivitätsname';
$string['searchtitle'] = 'Suchtitel';

// Setting.
$string['edwiserReport_settings'] = 'Edwiser Reports & Analytics Dashboard -Einstellungen';
$string['selectblocks'] = 'Wählen Sie Blöcke aus, die für Berichtsmanager angezeigt werden sollen:';
$string['rpmblocks'] = 'Reporting Manager -Blöcke';
$string['addblocks'] = 'Fügen Sie Blöcke hinzu';
$string['notselected'] = 'Nicht ausgewählt';
$string['colortheme'] = 'Farbthema';
$string['colorthemehelp'] = 'Wählen Sie das Farbthema für Dashboard.';
$string['theme'] = 'Thema';

// Custom Query Report.
$string['customqueryreport'] = 'Benutzerdefinierter Abfragebericht';
$string['completionrangeselector'] = 'Kursabschlussdatumbereichswahlauswahl';
$string['selectatleastonecvourse'] = 'Bitte wählen Sie mindestens einen Kurs aus';
$string['reportfields'] = 'Meldefelder';
$string['userfields'] = 'Benutzerfelder';
$string['coursefields'] = 'Kursfelder';
$string['lpfields'] = 'Felder für Lernprogramme';
$string['rpfields'] = 'Berichterstattungsverwalter Fields';
$string['activityfields'] = 'Aktivitätsfelder';
$string['coursestartdate'] = 'Kursstartdatum';
$string['courseenddate'] = 'Kursenddatum';
$string['lpstartdate'] = 'Lernprogramm Startdatum';
$string['lpenddate'] = 'Enddatum des Lernprogramms';
$string['lpduration'] = 'Lernprogrammdauer';
$string['lpcompletion'] = 'Abschlussdatum des Lernprogramms';
$string['rpmname'] = 'Berichtsleiter';
$string['totalactivities'] = 'Gesamtaktivitäten';
$string['completiontime'] = 'Kursabschlussdatum';
$string['activitiescompleted'] = 'Aktivitäten abgeschlossen';
$string['incompletedactivities'] = 'Unvollständige Aktivitäten';
$string['coursecategory'] = 'Kurskategorie';
$string['lpenroldate'] = 'Lernprogramm Anmeldedatum';
$string['courseenroldate'] = 'Kursanmeldedatum';
$string['course_completion_status'] = 'Kursabschlussstatus';
$string['learninghours'] = 'Lernstunden';

/* ERROR string */
$string['completiondatealert'] = 'Wählen Sie den richtigen Abschlussdatumbereich aus';
$string['enroldatealert'] = 'Wählen Sie die korrekte Registrierungsdatumbereiche';

/* Report name */
$string['reportname'] = 'custom_reports_{$a->date}.csv';
$string['totalgrade'] = 'Gesamtnote';
$string['attempt'] = 'Versuch';
$string['attemptstart'] = 'Versuch beginnen';
$string['attemptfinish'] = 'Versuchen Sie fertig';

$string['editblockview'] = 'Blockansicht bearbeiten';
$string['hide'] = 'Block verbergen';
$string['unhide'] = 'Block zeigen';
$string['editcapability'] = 'Änderungsfähigkeit';

$string['desktopview'] = 'Desktop -Ansicht';
$string['tabletview'] = 'Tablet -Ansicht';
$string['large'] = 'Groß';
$string['medium'] = 'Mittel';
$string['small'] = 'Klein';
$string['position'] = 'position';

$string['capabilties'] = 'Fähigkeiten';
$string['activeusersblockview'] = 'Standortübersichtsstatusansicht';
$string['activeusersblockedit'] = 'Site -Übersicht Status bearbeiten';
$string['activeusersblockeditadvance'] = 'Site -Übersicht Status Fortschritt bearbeiten';
$string['activecoursesblockview'] = 'Beliebte Kurse Blockansicht';
$string['activecoursesblockedit'] = 'Beliebte Kurse Block Edit';
$string['activecoursesblockeditadvance'] = 'Beliebte Kurse blockieren Advanced Edit';
$string['studentengagementblockview'] = 'Blockansicht der Schüler Engagement';
$string['studentengagementblockedit'] = 'Blockbearbeitungsblock für Schüler des Schülers';
$string['studentengagementblockeditadvance'] = 'Studentenblockabwachungsblock Bearbeiten';
$string['learnerblockview'] = 'Blockansicht der Lernenden';
$string['learnerblockedit'] = 'Lernende Blockbearbeitung';
$string['learnerblockeditadvance'] = 'Lernende Blockvorschriftenbearbeitung';
$string['courseprogressblockview'] = 'Kursfortschrittsblockansicht';
$string['courseprogressblockedit'] = 'Kursfortschrittsblockbearbeitung';
$string['courseprogressblockeditadvance'] = 'Kurs Fortschritt Block Advance Edit';
$string['certificatesblockview'] = 'Zertifikate Blockansicht';
$string['certificatesblockedit'] = 'Zertifikate blockieren bearbeiten';
$string['certificatesblockeditadvance'] = 'Zertifikate Block Advance Bearbeiten';
$string['liveusersblockview'] = 'Echtzeit -Benutzer Blockansicht blockieren';
$string['liveusersblockedit'] = 'Echtzeit -Benutzer blockieren Bearbeiten';
$string['liveusersblockeditadvance'] = 'Echtzeit -Benutzer blockieren die Vorabbearbeitung';
$string['siteaccessblockview'] = 'Blockansicht der Website zugreifen';
$string['siteaccessblockedit'] = 'Site Access Block Bearbeiten';
$string['siteaccessblockeditadvance'] = 'Site Access Block Advance Bearbeiten';
$string['todaysactivityblockview'] = 'Die heutige Aktivitätsblockansicht';
$string['todaysactivityblockedit'] = 'Heute Aktivitätsblock -Bearbeitung';
$string['todaysactivityblockeditadvance'] = 'Der heutige Aktivitätsblock erweiterte Bearbeitung';
$string['inactiveusersblockview'] = 'Inaktive Benutzer blockieren die Ansicht';
$string['inactiveusersblockedit'] = 'Inaktive Benutzer blockieren Bearbeiten';
$string['inactiveusersblockeditadvance'] = 'Inaktive Benutzer blockieren die Vorabbearbeitung';

/* Course progress manager strings */
$string['update_course_progress_data'] = 'Aktualisieren Sie den Kursfortschrittsdaten';

/* Course Completion Event */
$string['coursecompletionevent'] = 'Kursabschlussveranstaltung';
$string['courseprogessupdated'] = 'Kursfortschritt aktualisiert';

/* Error Strings */
$string['invalidparam'] = 'Ungültiger Parameter gefunden';
$string['moduleidnotdefined'] = 'Die Modul -ID ist nicht definiert';

$string['clicktogetuserslist'] = 'Klicken Sie auf Zahlen, um die Benutzerliste zu erhalten';

/* Email Schedule Strings */
$string['enabledisableemail'] = 'E -Mail aktivieren/deaktivieren';
$string['scheduleerrormsg'] = '<div class="alert alert-danger"><b>FEHLER:</b>Fehler beim Planen von E -Mails</div>';
$string['schedulesuccessmsg'] = '<div class="alert alert-success"><b>ERFOLG:</b>E -Mail erfolgreich geplant</div>';
$string['deletesuccessmsg'] = '<div class="alert alert-success"><b>ERFOLG:</b>E -Mail erfolgreich gelöscht</div>';
$string['deleteerrormsg'] = '<div class="alert alert-danger"><b>FEHLER:</b>E -Mail -Löschung fehlgeschlagen</div>';
$string['emptyerrormsg'] = '<div class="alert alert-danger"><b>FEHLER:</b>Name und Empfangsfelder können nicht leer sein</div>';
$string['emailinvaliderrormsg'] = '<div class="alert alert-danger"><b>FEHLER:</b>Ungültige E -Mail -Adderinnen (Speicherplatz nicht erlaubt)</div>';
$string['scheduledemaildisbled'] = '<div class="alert alert-success"><b>ERFOLG:</b>Geplante E -Mail deaktiviert</div>';
$string['scheduledemailenabled'] = '<div class="alert alert-success"><b>ERFOLG:</b>Geplante E -Mail aktiviert</div>';
$string['noscheduleemails'] = 'Es gibt keine geplanten E -Mails';

$string['nextrun'] = 'Nächster Lauf';
$string['frequency'] = 'Frequenz';
$string['manage'] = 'Verwalten';
$string['scheduleemailfor'] = 'Planen Sie E -Mails für';
$string['edit'] = 'Bearbeiten';
$string['delete'] = 'Löschen';

$string['report/edwiserreports_activeusersblock:editadvance'] = 'Voraus bearbeiten';

/* Custom Reports block related strings */
$string['customreport'] = 'Benutzerdefinierter Bericht';
$string['customreportedit'] = 'Benutzerdefinierte Berichte';
$string['customreportexportpdfnote'] = 'Wenn nicht alle Spalten sichtbar sind, können Sie die CSV- oder Excel -Option verwenden, um den Bericht zu exportieren.';
$string['reportspreview'] = 'Berichte Vorschau';
$string['reportsfilter'] = 'Berichte Filter';
$string['noreportspreview'] = 'Keine Vorschau vorhanden';
$string['userfields'] = 'Benutzerfelder';
$string['coursefields'] = 'Kursfelder';
$string['activityfields'] = 'Aktivitätsfelder';
$string['reportslist'] = 'Benutzerdefinierte Berichte';
$string['noreportslist'] = 'Keine benutzerdefinierten Berichte';
$string['allcohorts'] = 'Alle Kohorten';
$string['allstudents'] = 'Alle Schüler';
$string['allactivities'] = 'Alle Aktivitäten';
$string['save'] = 'Speichern';
$string['reportname'] = 'Berichtsname';
$string['reportshortname'] = 'Kurzer Name';
$string['savecustomreport'] = 'Benutzerdefinierte Bericht speichern';
$string['downloadenable'] = 'Download aktivieren';
$string['emptyfullname'] = 'Das Feld des Berichtsnamens ist erforderlich';
$string['emptyshortname'] = 'Das Feld kurzer Name ist erforderlich';
$string['nospecialchar'] = 'Das Feld kurzer Name zulässt nicht ein spezielles Zeichen';
$string['reportssavesuccess'] = 'Benutzerdefinierte Berichte erfolgreich gespeichert';
$string['reportssaveerror'] = 'Benutzerdefinierte Berichte konnten nicht speichern';
$string['shortnameexist'] = 'Shortname existiert bereits';
$string['createdby'] = 'Autorin';
$string['sno'] = 'S. Nr.';
$string['datecreated'] = 'Datum erstellt';
$string['datemodified'] = 'Datum geändert';
$string['enabledesktop'] = 'Desktop aktiviert';
$string['noresult'] = 'Keine Einträge gefunden';
$string['enabledesktop'] = 'Hinzufügen zu Berichten Das Dashboard';
$string['disabledesktop'] = 'Entfernen Sie das Dashboard von Berichten';
$string['editreports'] = 'Berichte bearbeiten';
$string['deletereports'] = 'Berichte löschen';
$string['deletesuccess'] = 'Berichte erfolgreich löschen';
$string['deletefailed'] = 'Berichte löschen fehl';
$string['deletecustomreportstitle'] = 'Benutzerdefinierte Berichte titieren löschen';
$string['deletecustomreportsquestion'] = 'Möchten Sie diese benutzerdefinierten Berichte wirklich löschen?';
$string['createcustomreports'] = 'Benutzerdefinierte Berichte erstellen/verwalten Block';
$string['searchreports'] = 'Suchberichte';
$string['title'] = 'Titel';
$string['createreports'] = 'Neuen Bericht erstellen';
$string['updatereports'] = 'Aktualisieren Sie Berichte';
$string['courseformat'] = 'Kursformat';
$string['completionenable'] = 'Kursabschluss ermöglicht';
$string['guestaccess'] = 'Kursgastzugang';
$string['selectcourses'] = 'Wählen Sie Kurse';
$string['selectcohorts'] = 'Wählen Sie Kohorten';
$string['createnewcustomreports'] = 'Neuen Bericht erstellen';
$string['unlockthisfeature'] = 'Erhältlich in der Pro -Versionsion';
$string['availableinpro'] = 'Verfügbar in der PRO<br>Version';
$string['upgradetopro'] = 'Upgrade auf PRO';
$string['okaygotit'] = 'Okay, verstanden!';
$string['imponotice'] = 'WICHTIGER HINWEIS';
$string['csvprowarning'] = '<strong>Der Export in CSV ist in Version 1.4.0 nicht mehr verfügbar</strong> (Nächstes Update von Edwiser Reports KOSTENLOS), da wir Code- und Feature-Level-Verbesserungen an diesem Feature vornehmen. <br><strong>Bitte beachten Sie:</strong> Es wird weiterhin Teil von Edwiser Reports PRO sein.';
$string['excelprowarning'] = '<strong>Der Export nach Excel ist in Version 1.4.0 nicht mehr verfügbar</strong> (Nächstes Update von Edwiser Reports KOSTENLOS), da wir Code- und Feature-Level-Verbesserungen an diesem Feature vornehmen. <br><strong>Bitte beachten Sie:</strong> Es wird weiterhin Teil von Edwiser Reports PRO sein.';
$string['emailprowarning'] = '<strong>Die E-Mail-Planung ist in Version 1.4.0 nicht mehr verfügbar</strong> (Nächstes Update von Edwiser Reports KOSTENLOS), da wir Code- und Feature-Level-Verbesserungen an diesem Feature vornehmen. <br><strong>Bitte beachten Sie:</strong> Es wird weiterhin Teil von Edwiser Reports PRO sein.';
$string['courseengagementprowarning'] = '<strong>Der Kursbeteiligungsbericht ist in Version 1.4.0 nicht mehr verfügbar </strong> (Nächstes Update von Edwiser Reports KOSTENLOS), da wir Code- und Feature-Level-Verbesserungen an diesem Feature vornehmen. <br><strong>Bitte beachten Sie: </strong> Es wird weiterhin Teil von Edwiser Reports PRO sein.';

$string['invalidsecretkey'] = 'Ungültiger geheimer Schlüssel. Bitte abmelden und erneut anmelden.';

$string['time'] = 'Zeit';

// Settings.
$string['generalsettings'] = 'Allgemeine Einstellungen';
$string['blockssettings'] = 'Einstellungen von Block';
$string['trackfrequency'] = 'Aktualisierungshäufigkeit des Zeitprotokolls <strong>(PRO)</strong>';
$string['trackfrequencyhelp'] = 'Diese Einstellung hilft Ihnen dabei, die Häufigkeit der Aktualisierung des Benutzerzeitprotokolls (detaillierte Folge von Benutzeraktivitäten mit einem Zeitstempel) in den Datenbanks festzulegene.
';
$string['precalculated'] = 'Vorberechnete Daten anzeigen <strong>(PRO)</strong>';
$string['precalculatedhelp'] = 'Wenn es aktiviert ist, wird wöchentlich, monatlich und jährlich berichtet.Sie werden kontinuierlich berechnet, verarbeitet und im Hintergrund gespeichert, um eine schnellere Belastung von Berichten zu erhalten.

Bei deaktivierter Behinderung läuft dieser Prozess der Berichtserzeugung im Hintergrund nicht mehr.Auf diese Weise wird das Berichts -Dashboard in diesem Moment nur dann, wenn Sie Berichte filtern, die erforderlichen Daten in diesem Moment anziehen, verarbeitet und berechnet und die Ladezeit der Berichte erhöht.

Wir empfehlen, diese Funktion zu aktivieren.

<strong> Hinweis: </strong> Cron -Aufgabe sollte für jede Stunde geplant werden, um genaue Daten zu erhalten.Schalten Sie diese Einstellung aus, wenn die Cron -Aufgabe nicht so eingestellt ist, dass sie häufig ausgeführt werden.
';
$string['positionhelp'] = 'Stellen Sie die Blockposition auf dem Dashboard ein.';
$string['positionhelpupgrade'] = '<br> <strong> Hinweis: Ändern Sie diese Einstellung auf der Upgrade -Seite nicht.Sie können Blöcke auf der Seite Dashboard- und Admin -Einstellungen neu ordnen. </strong>';
$string['desktopsize'] = 'Größe im Desktop';
$string['desktopsizehelp'] = 'Größe des Blocks in Desktop -Geräten';
$string['tabletsize'] = 'Größe im Tablet';
$string['tabletsizehelp'] = 'Größe des Blocks in Tablet -Geräten';
$string['rolesetting'] = 'Erlaubte Rollen';
$string['rolesettinghelp'] = 'Definieren Sie, welche Benutzer diesen Block anzeigen können';
$string['confignotfound'] = 'Konfiguration für dieses Plugin nicht gefunden';

// Settings for plugin upgrade.
$string['activeusersrolesetting'] = 'Site -Überblick -Statusblock zulässigen Rollen';
$string['courseprogressrolesetting'] = 'Kursfortschrittsblock erlaubte Rollen';
$string['studentengagementrolesetting'] = 'Ein -Verlobungsblock mit Studenten erlaubte Rollen';
$string['learnerrolesetting'] = 'Lernende Blocks erlaubte Rollen';
$string['activecoursesrolesetting'] = 'Beliebte Kurse Block erlaubte Rollen';
$string['certificatesrolesetting'] = 'Zertifikate Block zulässigen Rollen';
$string['liveusersrolesetting'] = 'Live -Benutzer blockieren zulässigen Rollen';
$string['siteaccessrolesetting'] = 'Site -Zugriffs -Informationen Block zulässigen Rollen';
$string['todaysactivityrolesetting'] = 'Der heutige Aktivitätsblock erlaubte Rollen';
$string['inactiveusersrolesetting'] = 'Inaktive Benutzer blockieren zulässigen Rollen';
$string['graderolesetting'] = 'Blockblock erlaubte Rollen';

$string['activeusersdesktopsize'] = 'Site -Übersichtstatusblockgröße auf dem Desktop';
$string['courseprogressdesktopsize'] = 'Kursfortschrittsblockgröße im Desktop';
$string['studentengagementdesktopsize'] = 'Blockgröße für Schülerblockgröße im Desktop';
$string['learnerdesktopsize'] = 'Blockgröße der Lernenden im Desktop';
$string['activecoursesdesktopsize'] = 'Beliebte Kurse blockieren die Größe im Desktop';
$string['certificatesdesktopsize'] = 'Zertifikate blockieren die Größe im Desktop';
$string['liveusersdesktopsize'] = 'Live -Benutzer blockieren die Größe im Desktop';
$string['siteaccessdesktopsize'] = 'Site -Zugriffsinformationen Blockgröße auf dem Desktop';
$string['todaysactivitydesktopsize'] = 'Die heutige Aktivitätsblockgröße im Desktop';
$string['inactiveusersdesktopsize'] = 'Inaktive Benutzer blockieren die Größe im Desktop';
$string['gradedesktopsize'] = 'Blockgröße der Klasse im Desktop';

$string['activeuserstabletsize'] = 'Site -Übersichtstatusblockgröße im Tablet';
$string['courseprogresstabletsize'] = 'Kursfortschrittsblockgröße in Tablet';
$string['studentengagementtabletsize'] = 'Blockgröße der Schüler Engagement in Tablet';
$string['learnertabletsize'] = 'Lernende Blockgröße in Tablet';
$string['activecoursestabletsize'] = 'Beliebte Kurse Blockgröße in Tablet';
$string['certificatestabletsize'] = 'Zertifikate Blockgröße in Tablet';
$string['liveuserstabletsize'] = 'Live -Benutzer blockieren die Größe im Tablet';
$string['siteaccesstabletsize'] = 'Site -Zugriffsinformationsblockgröße in Tablet';
$string['todaysactivitytabletsize'] = 'Die heutige Aktivitätsblockgröße in Tablet';
$string['inactiveuserstabletsize'] = 'Inaktive Benutzer blockieren die Größe im Tablet';
$string['gradetabletsize'] = 'Blockgröße in Tablet';

$string['activeusersposition'] = 'Standortübersicht der Statusblock';
$string['courseprogressposition'] = 'Position des Kursfortschritts';
$string['studentengagementposition'] = 'Position des Schülerblocks der Schüler';
$string['learnerposition'] = 'Position des Lernenden Blocks';
$string['activecoursesposition'] = 'Beliebte Kurse blockieren die Position von Block';
$string['certificatesposition'] = 'Die Position von Zertifikaten Block';
$string['liveusersposition'] = 'Live -Benutzer blockieren die Position von';
$string['siteaccessposition'] = 'Die Position des Site -Zugriffs Informationen blockiert';
$string['todaysactivityposition'] = 'Die Position des heutigen Aktivitätsblocks';
$string['inactiveusersposition'] = 'Inaktive Benutzer blockieren die Position von';
$string['gradeposition'] = 'Position des Gradblocks';

// Visits On Site block.
$string['visitsonsiteheader'] = 'Besuche vor Ort';
$string['visitsonsiteblockhelp'] = 'Die Anzahl der Besuche, die Benutzer auf Ihrer Website in einer bestimmten Benutzersitzung hatten.Die Sitzungsdauer ist in den Einstellungen von Edwiser Reports definiert.';
$string['visitsonsiteblockview'] = 'Besuche vor Ort Ansicht';
$string['visitsonsiteblockedit'] = 'Besuche vor Ort bearbeiten';
$string['visitsonsiterolesetting'] = 'Besuche vor Ort erlaubten Rollen';
$string['visitsonsitedesktopsize'] = 'Besuche vor Ort in Desktop';
$string['visitsonsitetabletsize'] = 'Besuche vor Ort in Tablet';
$string['visitsonsiteposition'] = 'Besuche vor Ort Position';
$string['visitsonsiteblockexportheader'] = 'Besuche vor Ortberichten';
$string['visitsonsiteblockexporthelp'] = 'In diesem Bericht werden die exportierten Daten vor Ort exportierten Daten angezeigt.';
$string['visitsonsiteblockeditadvance'] = 'Besuche vor Site Block Advance Bearbeiten';
$string['averagesitevisits'] = 'Durchschnittliche Site -Besuche';
$string['totalsitevisits'] = 'Gesamtbesuche';

// Time spent on site block.
$string['timespentonsiteheader'] = 'Zeit, die vor Ort aufgewendet wurde';
$string['timespentonsiteblockhelp'] = 'Zeit, die die Benutzer auf Ihrer Website an einem Tag verbracht haben.';
$string['timespentonsiteblockview'] = 'Zeit, die auf der Site -Ansicht aufgewendet wird';
$string['timespentonsiteblockedit'] = 'Zeit auf der Website verbringen Bearbeiten';
$string['timespentonsiterolesetting'] = 'Die Zeit, die auf der Website verbracht wurde, erlaubte Rollen';
$string['timespentonsitedesktopsize'] = 'Zeit, die auf der Stelle auf der Größe des Desktops aufgewendet wird';
$string['timespentonsitetabletsize'] = 'Zeit, die vor Ort in Tablet aufgewendet wird';
$string['timespentonsiteposition'] = 'Zeit, die vor Ort aufgewendet wird,';
$string['timespentonsiteblockexportheader'] = 'Zeit, die auf dem Standortbericht aufgewendet wird';
$string['timespentonsiteblockexporthelp'] = 'In diesem Bericht wird die Zeit für exportierte Daten auf dem Standort aufgewendet.';
$string['timespentonsiteblockeditadvance'] = 'Zeit, die auf Site Block Advance Bearbeiten aufgewendet wird';
$string['averagetimespent'] = 'Durchschnittliche Zeit verbracht';
$string['totaltimespent'] = 'Gesamtzeiten verbracht';

// Time spent on course block.
$string['timespentoncourseheader'] = 'Zeit auf Kurs verbracht';
$string['timespentoncourseblockhelp'] = 'Zeit, die die Lernenden in einem bestimmten Kurse an einem Tag verbracht haben.';
$string['timespentoncourseblockview'] = 'Zeit, die für den Kursansicht aufgewendet wird';
$string['timespentoncourseblockedit'] = 'Zeit für den Kursbearbeitung';
$string['timespentoncourserolesetting'] = 'Zeit für den Kurs erlaubte Rollen';
$string['timespentoncoursedesktopsize'] = 'Zeit für die Kursgröße im Desktop aufgewendet';
$string['timespentoncoursetabletsize'] = 'Zeit für die Kursgröße in Tablet';
$string['timespentoncourseposition'] = 'Zeit für den Kursposition aufgewendet';
$string['timespentoncourseblockexportheader'] = 'Zeit für den Kursbericht aufgewendet';
$string['timespentoncourseblockexporthelp'] = 'Dieser Bericht zeigt die Zeit, die für exportierte Daten für den Kurs aufgewendet wird.';
$string['timespentoncourseblockeditadvance'] = 'Zeit, die für den Kursblock -Advance -Bearbeiten aufgewendet wird';

// Course activity block.
$string['courseactivitystatusheader'] = 'Kursaktivitätsstatus';
$string['courseactivitystatusblockhelp'] = 'Kursaktivitäten von den Lernenden.Es handelt sich um eine Kombination von Aktivitäten, die ausgeführt werden, und Aufgaben übermittelte Zeilendiagramme.';
$string['courseactivitystatusblockview'] = 'Kursaktivitätsstatusansicht';
$string['courseactivitystatusblockedit'] = 'Kursaktivitätsstatus bearbeiten';
$string['courseactivitystatusrolesetting'] = 'Kursaktivitätsstatus erlaubte Rollen';
$string['courseactivitystatusdesktopsize'] = 'Kursaktivitätsstatusgröße im Desktop';
$string['courseactivitystatustabletsize'] = 'Kursaktivitätsstatusgröße in Tablet';
$string['courseactivitystatusposition'] = 'Kursaktivitätsstatus von\'s Position';
$string['courseactivitystatusblockexportheader'] = 'Kursaktivitätsstatusbericht';
$string['courseactivitystatusblockexporthelp'] = 'In diesem Bericht werden die exportierten Daten der Kursaktivität angezeigt.';
$string['courseactivitystatusblockeditadvance'] = 'Kursaktivitätsstatus Block Advance Bearbeiten';
$string['averagecompletion'] = 'Durchschnittliche Aktivität abgeschlossen';
$string['totalassignment'] = 'Gesamtaufgabe eingereicht';
$string['totalcompletion'] = 'Gesamtaktivität abgeschlossen';

// Learner Course Progress block.
$string['learnercourseprogressheader'] = 'Mein Kurs Fortschritt';
$string['learnercourseprogressblockhelp'] = 'Ihr Kursabschluss Fortschritte in einem bestimmten Kurs.';
$string['learnercourseprogressblockview'] = 'Meine Kursfortschrittsansicht';
$string['learnercourseprogressblockedit'] = 'Mein Kursfortschritt bearbeiten';
$string['learnercourseprogressrolesetting'] = 'Mein Kursfortschritt erlaubte Rollen';
$string['learnercourseprogressdesktopsize'] = 'Mein Kursfortschrittsgröße auf dem Desktop';
$string['learnercourseprogresstabletsize'] = 'Mein Kursschrittsgröße in Tablet';
$string['learnercourseprogressposition'] = 'Die Position meines Kursfortschritts';
$string['learnercourseprogressblockexportheader'] = 'Mein Kursfortschrittsbericht';
$string['learnercourseprogressblockexporthelp'] = 'In diesem Bericht werden die exportierten Daten von My Course Progress angezeigt.';
$string['learnercourseprogressblockeditadvance'] = 'Mein Kurs -Fortschritt Block Advance Edit';

// Learner Time spent on site block.
$string['learnertimespentonsiteheader'] = 'Meine Zeit, die ich vor Ort verbracht habe';
$string['learnertimespentonsiteblockhelp'] = 'Ihre Zeit auf der Website an einem Tag.';
$string['learnertimespentonsiteblockview'] = 'Meine Zeit, die ich auf der Site -Ansicht verbracht habe';
$string['learnertimespentonsiteblockedit'] = 'Meine Zeit, die ich auf der Website verbracht habe, bearbeiten';
$string['learnertimespentonsiterolesetting'] = 'Meine auf der Website verbrachte Zeit erlaubte Rollen';
$string['learnertimespentonsitedesktopsize'] = 'Meine Zeit, die ich auf der Größe des Desktops auf dem Standort aufgewendet habe';
$string['learnertimespentonsitetabletsize'] = 'Meine Zeit, die sie auf der Größe in Tablet aufgewendet haben';
$string['learnertimespentonsiteposition'] = 'Meine Zeit, die ich vor Ort aufgewendet habe,';
$string['learnertimespentonsiteblockexportheader'] = 'Meine Zeit, die auf dem Standortbericht aufgewendet wurde';
$string['learnertimespentonsiteblockexporthelp'] = 'In diesem Bericht werden die von exportierten Daten auf dem Standort aufgewendeten Zeiten angezeigt.';
$string['learnertimespentonsiteblockeditadvance'] = 'Meine Zeit, die ich auf dem Blockverfahren auf dem Site aufgewendet habe, bearbeiten';
$string['site'] = 'Seite';
$string['completed-y'] = 'Abgeschlossen';
$string['completed-n'] = 'Nicht vollständig';

// Top page insights.
$string['newregistrations'] = 'Neue Registrierungen';
$string['courseenrolments'] = 'Kurseinschreibungen';
$string['coursecompletions'] = 'Kursabschluss';
$string['activeusers'] = 'Aktive Benutzer';
$string['activitycompletions'] = 'Aktivitätsabschluss';
$string['timespentoncourses'] = 'Zeit für Kurse verbracht';
$string['totalcoursesenrolled'] = 'Gesamtkurse eingeschrieben';
$string['coursecompleted'] = 'Kurs abgeschlossen';
$string['activitiescompleted'] = 'Aktivitäten abgeschlossen';
