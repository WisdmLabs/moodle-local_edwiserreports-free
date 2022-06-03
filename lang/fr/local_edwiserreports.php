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

$string['pluginname'] = 'Edwiser Reports Libre';
$string['reportsdashboard'] = 'Edwiser Reports Libre';
$string['reportsandanalytics'] = 'Rapports et analyses gratuits';
$string['all'] = 'Toute';
$string['refresh'] = 'Rafraîchir';
$string['noaccess'] = 'Désolé.Vous n\'avez pas le droit d\'accéder à cette page.';
$string['showdatafor'] = 'Afficher les données pour';
$string['dashboard'] = 'Tableau de bord gratuit des rapports Edwiser';
$string['permissionwarning'] = 'Vous avez permis aux utilisateurs suivants de voir ce bloc qui n\'est pas recommandé.Veuillez masquer ce bloc à ces utilisateurs.Une fois que vous cachez ce bloc, il n\'apparaîtra plus.';
$string['showentries'] = 'Afficher les entrées';

/* Blocks Name */
$string['realtimeusers'] = 'Utilisateurs en temps réel';
$string['activeusersheader'] = 'Présentation du site Statut';
$string['courseprogress'] = 'Progrès du cours';
$string['courseprogressheader'] = 'Progrès du cours';
$string['studentengagementheader'] = 'Engagement étudiant';
$string['gradeheader'] = 'Notes';
$string['learnerheader'] = 'Bloc de l\'apprenant';
$string['courseengagement'] = 'Engagement de cours';
$string['coursereports'] = 'Rapports de cours';
$string['coursereportsheader'] = 'Rapports de cours';
$string['more'] = '<i class="fa fa-angle-right"></i> Suite';
$string['activecoursesheader'] = 'Cours populaires';
$string['f2fsessionsheader'] = 'Séances dirigées par l\'instructeur';
$string['certificatestats'] = 'Statistiques des certificats';
$string['certificatestatsheader'] = 'Statistiques des certificats';
$string['certificatesheader'] = 'Statistiques des certificats';
$string['lpstatsheader'] = 'Statistiques du programme d\'apprentissage';
$string['accessinfo'] = 'Informations d\'accès au site';
$string['siteaccessheader'] = 'Informations d\'accès au site';
$string['inactiveusers'] = 'Liste des utilisateurs inactifs';
$string['inactiveusersheader'] = 'Liste des utilisateurs inactifs';
$string['liveusersheader'] = 'Bloc des utilisateurs en direct';
$string['todaysactivityheader'] = 'Activités quotidiennes';
$string['overallengagementheader'] = 'Engagement global dans les cours';
$string['inactiveusersexportheader'] = 'Rapport des utilisateurs inactifs';
$string['inactiveusersblockexportheader'] = 'Rapport des utilisateurs inactifs';
$string['date'] = 'date';
$string['time'] = 'Temps';
$string['venue'] = 'Lieu';
$string['signups'] = 'Inscriptions';
$string['attendees'] = 'Participantes';
$string['name'] = 'Nom';
$string['course'] = 'Cours';
$string['issued'] = 'Publié';
$string['notissued'] = 'Non délivré';
$string['nof2fmodule'] = 'Il n\'y a pas de sessions face à face disponibles.';
$string['nof2fsessions'] = 'Il n\'y a pas de session face à face disponible pour ce module.';
$string['nocertificates'] = 'Aucun certificat n\'est créé';
$string['nocertificatesawarded'] = 'Aucun certificat n\'est attribué';
$string['unselectall'] = 'Tout déselectionner';
$string['selectall'] = 'Tout sélectionner';
$string['activity'] = 'Activité';
$string['cohorts'] = 'Cohortes';
$string['nographdata'] = 'Pas de données';

// Breakdown the tooltip string to display in 2 lines.
$string['cpblocktooltip1'] = '{$a->per} cours terminé';
$string['cpblocktooltip2'] = 'par {$a->val} utilisateurs';

$string['lpstatstooltip'] = '{$a->data} utilisateurs terminés {$a->label}.';
$string['fullname'] = 'Nom complet';
$string['onlinesince'] = 'En ligne depuis';
$string['status'] = 'Statut';
$string['todayslogin'] = 'Connexion';
$string['learners'] = 'Apprenants';
$string['teachers'] = 'Enseignantes';
$string['eventtoday'] = 'Événements de la journée';
$string['value'] = 'Valeur';
$string['count'] = 'Compter';
$string['enrollment'] = 'Inscription';
$string['activitycompletion'] = 'Complétion des activités';
$string['coursecompletion'] = 'Achèvement du cours';
$string['newregistration'] = 'Nouvelles inscriptions';
$string['timespent'] = 'Temps passé';
$string['sessions'] = 'Séances';
$string['totalusers'] = 'Total utilisateurs';
$string['sitevisits'] = 'Visites du site par heure';
$string['lastupdate'] = 'Dernière mise à jour <span class="minute"> 0 </span> il y a min';
$string['lastweek'] = 'La semaine dernière';
$string['lastmonth'] = 'Le mois dernier';
$string['lastyear'] = 'L\'année dernière';
$string['custom'] = 'Date de personnalité';
$string['rank'] = 'Rang';
$string['enrolments'] = 'Inscription';
$string['visits'] = 'Visites';
$string['totalvisits'] = 'Visites totales';
$string['averagevisits'] = 'Visites moyennes';
$string['completions'] = 'Achèvement';
$string['selectdate'] = 'Sélectionner une date';
$string['never'] = 'Jamais';
$string['before1month'] = 'Avant 1 mois';
$string['before3month'] = 'Avant 3 mois';
$string['before6month'] = 'Avant 6 mois';
$string['recipient'] = 'Destinataire';
$string['duplicateemail'] = 'E-mail en double';
$string['invalidemail'] = 'Email invalide';
$string['subject'] = 'Matière';
$string['message'] = 'message';
$string['reset'] = 'Réinitialiser';
$string['send'] = 'Envoyer maintenant';
$string['editblocksetting'] = 'Modifier le paramètre de bloc';
$string['editblockcapabilities'] = 'Modifier les capacités de bloc';
$string['hour'] = 'heure';
$string['hours'] = 'les heures';
$string['minute'] = 'minute';
$string['minutes'] = 'minutes';
$string['second'] = 'seconde';
$string['seconds'] = 'secondes';
$string['zerorecords'] = 'Aucun enregistrements correspondants trouvés';
$string['nodata'] = 'Pas de données';
$string['tableinfo'] = 'Affichage _START_ à _END_ de _TOTAL_ entrées';
$string['infoempty'] = 'Affichage des entrées 0 à 0';
$string['allgroups'] = 'Tous les groupes';
$string['nogroups'] = 'Pas de groupes';
$string['pleaseselectcourse'] = 'Veuillez sélectionner le cours pour voir les groupes';

/* Block help tooltips */
$string['activeusersblocktitlehelp'] = 'Un aperçu de l\'activité quotidienne sur votre site.Essentiel pour que les gestionnaires vérifient l\'activité globale sur le site.';
$string['activeusersblockhelp'] = 'Ce bloc affichera le graphique des utilisateurs actifs sur la période avec l\'inscription des cours et l\'achèvement du cours.';
$string['courseprogressblockhelp'] = 'Ce bloc montrera le graphique à secteurs d\'un cours avec pourcentage.';
$string['activecoursesblockhelp'] = 'Ce bloc montrera les cours les plus actifs en fonction de l\'inscription et des achèvements des visites.';
$string['studentengagementblockhelp'] = 'Les rapports d\'engagement des étudiants affichent un délai des étudiants sur des sites, des cours et le total des visites sur le cours.';
$string['gradeblockhelp'] = 'Ce bloc montre les notes.';
$string['learnerblockhelp'] = 'Suivez vos progrès de cours et votre séjour sur place.';
$string['certificatestatsblockhelp'] = 'Ce bloc affichera tous les certificats personnalisés créés et le nombre d\'utilisateurs inscrits attribués avec ces certificats.';
$string['realtimeusersblockhelp'] = 'Ce bloc affichera tous les utilisateurs connectés dans ce site.';
$string['f2fsessionsblockhelp'] = 'Ce bloc montrera toutes les séances en face à face créées et compter toutes les inscriptions et les participants.';
$string['accessinfoblockhelp'] = 'Ce bloc montrera l\'utilisation moyenne du site en une semaine.';
$string['lpstatsblockhelp'] = 'Ce bloc affichera tout le cours suivi par les utilisateurs dans un programme d\'apprentissage.';
$string['todaysactivityblockhelp'] = 'Ce bloc montrera les activités quotidiennes effectuées sur ce site.';
$string['inactiveusersblockhelp'] = 'Ce bloc affichera la liste des utilisateurs inactifs dans ce site.';
$string['inactiveusersexporthelp'] = 'Ce rapport montrera l\'inactivité des utilisateurs sur le site Web';
$string['none'] = 'Rien';

/* Block Course Progress */
$string['averagecourseprogress'] = 'Progrès du cours moyen';
$string['nocourses'] = 'Aucun cours trouvé';
$string['activity'] = 'Activité';
$string['activities'] = 'Activités';
$string['student'] = 'Élève';
$string['students'] = 'Étudiantes';

/* Block Learning Program */
$string['nolearningprograms'] = 'Aucun programme d\'apprentissage trouvé';

/* Block Site access information */
$string['siteaccessinformationtask'] = 'Calculer les informations d\'accès au site';
$string['siteaccessrecalculate'] = 'Le plugin est juste mis à niveau.S\'il vous plaît <a target="_blank" href="{$a}"> exécuter </a> <strong> Calculer les informations d\'accès au site </strong> tâche pour voir le résultat.';
$string['siteaccessinformationcronwarning'] = '<strong> Calculer les informations d\'accès au site </strong> La tâche doit s\'exécuter toutes les 24 heures. S\'il vous plaît <a target="_blank" href="{$a}"> exécuter maintenant </a> pour voir un résultat précis.';
$string['busiest'] = 'Le plus occupé';
$string['quietest'] = 'Le moins occupé';

/* Block Inactive Users */
$string['siteaccess'] = 'Accès au site:';
$string['before1month'] = 'Avant 1 mois';
$string['before3month'] = 'Avant 3 mois';
$string['before6month'] = 'Avant 6 mois';
$string['noinactiveusers'] = 'Aucun utilisateur inactif n\'est disponible';

/* Active users block */
$string['activeuserstask'] = 'Calculer les données de blocage des utilisateurs actifs';
$string['averageactiveusers'] = 'Utilisateurs actifs moyens';
$string['totalactiveusers'] = 'Utilisateurs actifs totaux';
$string['searchdate'] = 'Date de recherche';
$string['noactiveusers'] = 'Il n\'y a pas d\'utilisateurs actifs';
$string['nousers'] = 'Il n\'y a pas d\'utilisateurs';
$string['courseenrolment'] = 'Inscription au cours';
$string['coursecompletionrate'] = 'Taux d\'achèvement du cours';
$string['totalcourseenrolments'] = 'Inscriptions totales de cours';
$string['totalcoursecompletions'] = 'Achèvement total des cours';

/* Student Engagement block */
$string['studentengagementexportheader'] = 'Rapport d\'engagement des étudiants';
$string['studentengagementreportheader'] = 'Rapport d\'engagement des étudiants';
$string['visitsonlms'] = 'Visites sur place';
$string['timespentonlms'] = 'Temps passé sur place';
$string['timespentonsite'] = 'Temps passé sur place';
$string['timespentoncourse'] = 'Temps passé sur le parcours';
$string['assignmentsubmitted'] = 'Affectations soumises';
$string['visitsoncourse'] = 'Visites en cours';
$string['studentengagementtask'] = 'Données d\'engagement des étudiants';
$string['searchuser'] = 'Rechercher un utilisateur';
$string['emptytable'] = 'Aucun enregistrement à montrer';
$string['courseactivitystatus'] = 'Affectation soumise, activités terminées';
$string['courseactivitystatus-submissions'] = 'Affectation soumise';
$string['courseactivitystatus-completions'] = 'Activités terminées';

/* Learner block */
$string['learnerreportexportheader'] = 'Rapport de l\'apprenant';
$string['learnerreportheader'] = 'Rapport de l\'apprenant';
$string['searchcourse'] = 'Cours de recherche';
$string['own'] = 'Propres';

/* Active Users Page */
$string['noofactiveusers'] = 'Nombre d\'utilisateurs actifs';
$string['noofenrolledusers'] = 'Nombre d\'inscriptions';
$string['noofcompletedusers'] = 'Nombre de compléments';
$string['email'] = 'E-mail';
$string['emailscheduled'] = 'Courriel planifié';
$string['usersnotavailable'] = 'Aucun utilisateur n\'est disponible pour cette journée';
$string['activeusersmodaltitle'] = 'Les utilisateurs actifs sur {$a->date}';
$string['enrolmentsmodaltitle'] = 'Les utilisateurs sont inscrits aux cours à {$a->date}';
$string['completionsmodaltitle'] = 'Les utilisateurs qui ont terminé un cours sur {$a->date}';
$string['recordnotfound'] = 'Enregistrement non trouvé';
$string['jsondecodefailed'] = 'JSON Decode a échoué';
$string['emaildataisnotasarray'] = 'Les données par e-mail ne sont pas un tableau';
$string['sceduledemailnotexist'] = 'Planifier le courrier électronique n\'existe pas';
$string['searchdate'] = 'Date de recherche';

/* Active courses block */
$string['activecoursestask'] = 'Calculer les données de cours actifs';

/* Grades block */
$string['gradeblockview'] = 'Vue de blocs de qualité';
$string['gradeblockeditadvance'] = 'Bloc de note Modifier';
$string['coursegrades'] = 'Notes de cours';
$string['studentgrades'] = 'Notes étudiants';
$string['activitygrades'] = 'Notes d\'activité';
$string['averagegrade'] = 'La note moyenne';
// Export header strings.

/* Course Progress Page */
$string['coursename'] = 'Nom du cours';
$string['enrolled'] = 'Inscrite';
$string['completed'] = 'Terminé';
$string['inprogress'] = 'En cours';
$string['per40-20'] = '20% - 40%';
$string['per60-40'] = '40% - 60%';
$string['per80-60'] = '60% - 80%';
$string['per100-80'] = '80% - 100%';
$string['per20-0'] = '0% - 20%';
$string['per100'] = '100%';

/* Certificates Page */
$string['username'] = 'Nom d\'utilisateur';
$string['useremail'] = 'E-mail utilisateur';
$string['dateofissue'] = 'Date d\'émission';
$string['dateofenrol'] = 'Date d\'inscription';
$string['grade'] = 'grade';
$string['courseprogress'] = 'Progrès du cours';
$string['notenrolled'] = 'Utilisateur non inscrit';
$string['searchcertificates'] = 'Certificats de recherche';

/* f2f Sessions Block */
$string['attended'] = 'Assistée';
$string['requested'] = 'Demandée';
$string['canceled'] = 'Annulé';
$string['approved'] = 'A approuvé';
$string['booked'] = 'Réservée';
$string['f2fmore'] = 'Suite';
$string['download'] = 'Télécharger';

/* Site Access Block*/
$string['sun'] = 'SOLEIL';
$string['mon'] = 'LUN';
$string['tue'] = 'MAR';
$string['wed'] = 'MER';
$string['thu'] = 'JEU';
$string['fri'] = 'VEN';
$string['sat'] = 'SAM';
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
$string['siteaccessinfo'] = 'Accès aux utilisateurs AVG';

/* f2f Sessions Page*/
$string['waitlist'] = 'Liste d\'attente';
$string['declined'] = 'Diminué';
$string['reason'] = '(Si annulé) Raison';
$string['confirmed'] = 'Confirmé';
$string['nosignups'] = 'Aucune inscription n\'est disponible';
$string['nosessions'] = 'Il n\'y a pas de sessions face à face';

/* Learning Program Page */
$string['lastaccess'] = 'Dernier accès';
$string['progress'] = 'Le progrès';
$string['avgprogress'] = 'Progrès AVG';
$string['notyet'] = 'Pas encore';
$string['lpname'] = 'Nom du programme d\'apprentissage';
$string['lpdetailedreport'] = 'Télécharger le rapport détaillé des programmes d\'apprentissage';
$string['searchlps'] = 'Programmes d\'apprentissage de recherche';
$string['exportlpdetailedreport'] = 'Exporter le rapport détaillé';

/* Export Strings */
$string['csv'] = 'csv';
$string['excel'] = 'Exceller';
$string['pdf'] = 'Pdf';
$string['email'] = 'E-mail';
$string['exporttocsv'] = 'Exporter vers CSV';
$string['exporttoexcel'] = 'Exporter vers Excel';
$string['exporttopdf'] = 'Exporter vers PDF';
$string['sendoveremail'] = 'Envoyer par e-mail';
$string['copy'] = 'Copie';
$string['activeusers_status'] = 'Utilisateur actif';
$string['enrolments_status'] = 'Utilisateur inscrit';
$string['completions_status'] = 'Cours terminé';
$string['completedactivity'] = 'Activité terminée';
$string['coursecompletedusers'] = 'Cours terminé par les utilisateurs';
$string['emailsent'] = 'Un e-mail a été envoyé à votre compte de messagerie';
$string['reportemailhelp'] = 'Le rapport sera envoyé à cette adresse e-mail.';
$string['emailnotsent'] = 'Échec de l\'envoi par e-mail';
$string['subject'] = 'Matière';
$string['content'] = 'Teneur';
$string['emailexample'] = 'example1.mail.com, example2.mail.com;';

$string['activeusersblockexportheader'] = 'Présentation de l\'activité du site';
$string['activeusersblockexporthelp'] = 'Ce rapport affichera les utilisateurs actifs, l\'inscription des cours et l\'achèvement du cours au cours de la période.';
$string['courseprogressblockexportheader'] = 'Rapport d\'avancement du cours';
$string['courseprogressblockexporthelp'] = 'Ce rapport montrera les progrès du cours d\'un cours particulier par les utilisateurs.';
$string['activecoursesblockexportheader'] = 'Rapport de cours le plus actif';
$string['activecoursesblockexporthelp'] = 'Ce rapport montrera les cours les plus actifs en fonction des inscriptions, des visites et des achèvements.';
$string['certificatesblockexportheader'] = 'Rapport de certificats attribués';
$string['certificatesblockexporthelp'] = 'Ce rapport affichera les certificats qui ont délivré ou non délivré aux utilisateurs inscrits.';
$string['f2fsessionblockexportheader'] = 'Rapport de sessions dirigées par l\'instructeur';
$string['f2fsessionblockexporthelp'] = 'Ce rapport montrera les détails des sessions dirigés par l\'instructeur.';
$string['lpstatsblockexportheader'] = 'Rapport du programme d\'apprentissage';
$string['lpstatsblockexporthelp'] = 'Ce rapport montrera les détails du programme d\'apprentissage.';
$string['courseengageblockexportheader'] = 'Rapport d\'engagement du cours';
$string['courseengageblockexporthelp'] = 'Ce rapport montrera l\'engagement du cours par les utilisateurs.';
$string['completionblockexportheader'] = 'Rapport d\'achèvement du cours';
$string['completionexportheader'] = 'Rapport d\'achèvement du cours';
$string['completionblockexporthelp'] = 'Ce rapport affichera les achèvements du cours par les utilisateurs.';
$string['completionexporthelp'] = 'Ce rapport affichera les achèvements du cours par les utilisateurs.';
$string['courseanalyticsblockexportheader'] = 'Rapport d\'achèvement du cours';
$string['courseanalyticsblockexporthelp'] = 'Ce rapport affichera les achèvements du cours par les utilisateurs.';
$string['studentengagementblockexportheader'] = 'Rapport d\'engagement des étudiants';
$string['gradeblockexportheader'] = 'Rapport de note';
$string['gradeblockexporthelp'] = 'Ce rapport montrera les notes des étudiants.';
$string['studentengagementexporthelp'] = 'Ce rapport montrera les engagements des étudiants.';
$string['exportlpdetailedreports'] = 'Exporter des rapports détaillés';
$string['inactiveusersblockexporthelp'] = 'Ce rapport montrera l\'inactivité des utilisateurs sur le site Web';

$string['times_0'] = '06:30 AM';
$string['times_1'] = '10:00 AM';
$string['times_2'] = '04:30 PM';
$string['times_3'] = '10:30 PM';
$string['week_0'] = 'Dimanche';
$string['week_1'] = 'Lundi';
$string['week_2'] = 'Mardi';
$string['week_3'] = 'Mercredi';
$string['week_4'] = 'Jeudi';
$string['week_5'] = 'Vendredi';
$string['week_6'] = 'Samedi';
$string['monthly_0'] = 'Début du mois';
$string['monthly_1'] = 'Mois entre';
$string['monthly_2'] = 'La fin du mois';
$string['weeks_on'] = 'Des semaines';
$string['emailthisreport'] = 'Envoyez un courriel à ce rapport';
$string['onevery'] = 'Sur tout';
$string['duration_0'] = 'du quotidien';
$string['duration_1'] = 'Hebdomadaire';
$string['duration_2'] = 'Mensuelle';
$string['everydays'] = 'Tous les jours {$a->time}';
$string['everyweeks'] = 'Tous {$a->day}';
$string['everymonths'] = 'Chaque mois à {$a->time}';
$string['schedule'] = 'Planifier le courrier électronique';
$string['downloadreport'] = 'Rapport de téléchargement';
$string['scheduledlist'] = 'Tous les rapports prévus';
$string['reset'] = 'Réinitialiser';
$string['confirmemailremovaltitle'] = 'Supprimer le courrier électronique planifié';
$string['confirmemailremovalquestion'] = '<p class="px-20">Voulez-vous vraiment supprimer cet e-mail planifié</p>';

/* Course Engagement Block */
$string['activitystart'] = 'Au moins une activité a commencé';
$string['completedhalf'] = 'Terminé 50% des cours';
$string['coursecompleted'] = 'Cours terminé';
$string['nousersavailable'] = 'Aucun utilisateur disponible';

/* Course Completion Page */
$string['nostudentsenrolled'] = 'Aucun utilisateur n\'est inscrit comme étudiant';
$string['completionheader'] = 'Rapports d\'achèvement du cours: {$a->coursename}';
$string['completionreports'] = 'Rapports d\'achèvement';
$string['completionpercantage'] = 'Pourcentage d\'achèvement';
$string['activitycompleted'] = '{$a->completed} sur {$a->total}';

/* Course Analytics Page */
$string['courseanalytics'] = 'Analyse de cours';
$string['courseanalyticsheader'] = 'Analyse de cours: {$a->coursename}';
$string['recentvisits'] = 'Visites récentes';
$string['lastvisit'] = 'Derniere visite';
$string['enrolledon'] = 'Inscrit sur';
$string['enrolltype'] = 'Type d\'inscription';
$string['noofvisits'] = 'Nombre de visites';
$string['completiontime'] = 'Le temps d\'achèvement';
$string['spenttime'] = 'Temps passé';
$string['completedon'] = 'Complété sur';
$string['recentcompletion'] = 'Achèvement récent';
$string['recentenrolment'] = 'Inscriptions récentes';
$string['recentvisits'] = 'Visites récentes';
$string['nousersincourse'] = 'Aucun utilisateur ne s\'est inscrit à ce cours';
$string['nouserscompleted'] = 'Aucun utilisateur n\'a terminé ce cours';
$string['nousersvisited'] = 'Aucun utilisateur n\'a visité ce cours';

/* Cron Task Strings */
$string['updatetables'] = 'MISE À JOUR RAPPORTS ET Tableau d\'analyse';
$string['updatingrecordstarted'] = 'La mise à jour des rapports et de l\'enregistrement d\'analyse est créé ...';
$string['updatingrecordended'] = 'La mise à jour des rapports et des enregistrements d\'analyse est terminée ...';
$string['updatinguserrecord'] = 'Mise à jour de l\'utilisateur {$a->userid} dans CourseId {$a->courseid}';
$string['deletingguserrecord'] = 'Suppression de l\'utilisateur {$a->userid} dans CourseId {$a->courseid}';
$string['gettinguserrecord'] = 'Obtenir userId {$a->userid} dans CourseId {$a->courseid}';
$string['creatinguserrecord'] = 'Créer des enregistrements pour les fins des utilisateurs';
$string['sendscheduledemails'] = 'Envoyer des e-mails planifiés';
$string['sendingscheduledemails'] = 'Envoi de courriels planifiés ...';
$string['sending'] = 'Envoi en cours';

/* Cache Strings */
$string['cachedef_edwiserReport'] = 'Ce sont les caches des rapports d\'Edwiser';

/* Capabilties */
$string['edwiserReport:view'] = 'Afficher les rapports et le tableau de bord analytique';

/* Custom report block */
$string['downloadcustomtreport'] = 'Télécharger les utilisateurs Rapport de progression';
$string['selectdaterange'] = 'Sélectionner la plage de dates';
$string['learningprograms'] = 'Programmes d\'apprentissage';
$string['courses'] = 'Cours';
$string['shortname'] = 'Nom court';
$string['downloadreportincsv'] = 'Télécharger les rapports dans CSV';
$string['startdate'] = 'Date de début';
$string['enddate'] = 'Date de fin';
$string['select'] = 'Sélectionner';
$string['selectreporttype'] = 'Sélectionner le type de rapport';
$string['completedactivities'] = 'Activité terminée';
$string['completionspercentagecentage'] = 'Achèvement(%)';
$string['firstname'] = 'Prénom';
$string['lastname'] = 'Nom de famille';
$string['average'] = 'Moyenne(%)';
$string['enrolmentstartdate'] = 'Date de début d\'inscription';
$string['enrolmentenddate'] = 'Date de fin d\'inscription';
$string['enrolmentrangeselector'] = 'Sélecteur de plage de dates d\'inscription';
$string['category'] = 'Catégorie';
$string['customreportselectfailed'] = '<h4><i class="fa fa-check" aria-hidden="true"></i> Manquée!</h4>Sélectionnez l\'une des cases à cocher pour obtenir des rapports.';
$string['customreportdatefailed'] = '<h4><i class="fa fa-check" aria-hidden="true"></i> Manquée!</h4>Sélectionnez la date valide pour l\'inscription.';
$string['customreportsuccess'] = '<h4><i class="fa fa-check" aria-hidden="true"></i> Succès!</h4>Notifications envoyées avec succès.';
$string['customreportfailed'] = '<h4><i class="fa fa-check" aria-hidden="true"></i> Manquée!</h4>Sélectionnez l\'une des cases à cocher pour obtenir des rapports.';
$string['duration'] = 'Durée';
$string['na'] = 'NA';
$string['activityname'] = 'Nom d\'activité';
$string['searchtitle'] = 'Titre de recherche';

// Setting.
$string['edwiserReport_settings'] = 'Paramètres du tableau de bord Edwiser Reports & Analytics';
$string['selectblocks'] = 'Sélectionnez des blocs à afficher pour les gestionnaires de rapports:';
$string['rpmblocks'] = 'Blocs de gestionnaire de reportage';
$string['addblocks'] = 'Ajouter des blocs';
$string['notselected'] = 'Non séléctionné';
$string['colortheme'] = 'Thème de la couleur';
$string['colorthemehelp'] = 'Choisissez le thème de la couleur pour le tableau de bord.';
$string['theme'] = 'Thème';

// Custom Query Report.
$string['customqueryreport'] = 'Rapport de requête personnalisée';
$string['completionrangeselector'] = 'Sélecteur de plage de date d\'achèvement du cours';
$string['selectatleastonecvourse'] = 'S\'il vous plaît, sélectionnez au moins un cours';
$string['reportfields'] = 'Rapport des champs';
$string['userfields'] = 'Champs d\'utilisateur';
$string['coursefields'] = 'Champs de cours';
$string['lpfields'] = 'Champs de programme d\'apprentissage';
$string['rpfields'] = 'Champs de gestionnaire de reportage';
$string['activityfields'] = 'Champs d\'activité';
$string['coursestartdate'] = 'Date de début du cours';
$string['courseenddate'] = 'Date de fin du cours';
$string['lpstartdate'] = 'Date de début du programme d\'apprentissage';
$string['lpenddate'] = 'Date de fin du programme d\'apprentissage';
$string['lpduration'] = 'Durée du programme d\'apprentissage';
$string['lpcompletion'] = 'Date d\'achèvement du programme d\'apprentissage';
$string['rpmname'] = 'Responsable des rapports';
$string['totalactivities'] = 'Activités totales';
$string['completiontime'] = 'Date d\'achèvement du cours';
$string['activitiescompleted'] = 'Activités terminées';
$string['incompletedactivities'] = 'Activités incompilées';
$string['coursecategory'] = 'Catégorie de cours';
$string['lpenroldate'] = 'Date d\'inscription du programme d\'apprentissage';
$string['courseenroldate'] = 'Date d\'inscription du cours';
$string['course_completion_status'] = 'Statut d\'achèvement du cours';
$string['learninghours'] = 'Heures d\'apprentissage';

/* ERROR string */
$string['completiondatealert'] = 'Sélectionnez Correct Date d\'achèvement Range';
$string['enroldatealert'] = 'Sélectionnez la plage de date d\'inscription correcte';

/* Report name */
$string['reportname'] = 'custom_reports_{$a->date}.csv';
$string['totalgrade'] = 'Note totale';
$string['attempt'] = 'Tenter';
$string['attemptstart'] = 'Tenter de commencer';
$string['attemptfinish'] = 'Tentative de finition';

$string['editblockview'] = 'Modifier la vue du bloc';
$string['hide'] = 'Masquer le bloc';
$string['unhide'] = 'Bloc d\'exposition';
$string['editcapability'] = 'Capacité de changement';

$string['desktopview'] = 'Vue de bureau';
$string['tabletview'] = 'Vue de la tablette';
$string['large'] = 'Grande';
$string['medium'] = 'Moyen';
$string['small'] = 'Petite';
$string['position'] = 'position';

$string['capabilties'] = 'Capacités';
$string['activeusersblockview'] = 'Vue d\'état d\'aperçu du site';
$string['activeusersblockedit'] = 'Présentation du site Statut Modifier';
$string['activeusersblockeditadvance'] = 'Présentation du site Statut Advance Modifier';
$string['activecoursesblockview'] = 'Vue de blocs de cours populaires';
$string['activecoursesblockedit'] = 'Cours populaires Bloc Modifier';
$string['activecoursesblockeditadvance'] = 'Les cours populaires bloquent la modification avancée';
$string['studentengagementblockview'] = 'Vue de blocs d\'engagement des étudiants';
$string['studentengagementblockedit'] = 'Bloc de fiançailles des étudiants Modifier';
$string['studentengagementblockeditadvance'] = 'Bloc de fiançailles des étudiants Modifier';
$string['learnerblockview'] = 'Vue de blocs d\'apprenant';
$string['learnerblockedit'] = 'Bloc de l\'apprenant Modifier';
$string['learnerblockeditadvance'] = 'Modifier d\'avance du bloc d\'apprenant';
$string['courseprogressblockview'] = 'Vue de blocs de progrès du cours';
$string['courseprogressblockedit'] = 'Cours Progress Block Modifier';
$string['courseprogressblockeditadvance'] = 'Cours Progress Block Advance Modifier';
$string['certificatesblockview'] = 'Certificats Vue de blocage';
$string['certificatesblockedit'] = 'Certificats Bloc Modifier';
$string['certificatesblockeditadvance'] = 'Certificats Bloquer Advance Modifier';
$string['liveusersblockview'] = 'Les utilisateurs en temps réel bloquent la vue';
$string['liveusersblockedit'] = 'Les utilisateurs en temps réel bloquent l\'édition';
$string['liveusersblockeditadvance'] = 'Les utilisateurs en temps réel bloquent la modification à l\'avance';
$string['siteaccessblockview'] = 'Affichage du bloc d\'accès du site';
$string['siteaccessblockedit'] = 'Édition du bloc d\'accès du site Modifier';
$string['siteaccessblockeditadvance'] = 'Le bloc d\'accès du site Modifier';
$string['todaysactivityblockview'] = 'Vue de blocs d\'activité d\'aujourd\'hui';
$string['todaysactivityblockedit'] = 'Bloc d\'activité d\'aujourd\'hui Modifier';
$string['todaysactivityblockeditadvance'] = 'Bloc d\'activité d\'aujourd\'hui Modifier';
$string['inactiveusersblockview'] = 'Vue de blocage des utilisateurs inactifs';
$string['inactiveusersblockedit'] = 'Les utilisateurs inactifs bloquent l\'édition';
$string['inactiveusersblockeditadvance'] = 'Les utilisateurs inactifs bloquent l\'avance Modifier';

/* Course progress manager strings */
$string['update_course_progress_data'] = 'Mettre à jour les données de progression du cours';

/* Course Completion Event */
$string['coursecompletionevent'] = 'Événement d\'achèvement du cours';
$string['courseprogessupdated'] = 'Progrès du cours Mis à jour';

/* Error Strings */
$string['invalidparam'] = 'Paramètre non valide trouvé';
$string['moduleidnotdefined'] = 'L\'ID du module n\'est pas défini';

$string['clicktogetuserslist'] = 'Cliquez sur les numéros afin d\'obtenir la liste des utilisateurs';

/* Email Schedule Strings */
$string['enabledisableemail'] = 'Activer / désactiver l\'e-mail';
$string['scheduleerrormsg'] = '<div class="alert alert-danger"><b>ERREUR:</b>Erreur lors de la planification de l\'e-mail</div>';
$string['schedulesuccessmsg'] = '<div class="alert alert-success"><b>SUCCÈS:</b>E-mail planifié avec succès</div>';
$string['deletesuccessmsg'] = '<div class="alert alert-success"><b>SUCCÈS:</b>Email supprimé avec succès</div>';
$string['deleteerrormsg'] = '<div class="alert alert-danger"><b>ERREUR:</b>La suppression par e-mail a échoué</div>';
$string['emptyerrormsg'] = '<div class="alert alert-danger"><b>ERREUR:</b>Les champs de noms et de récepteur ne peuvent pas être vides</div>';
$string['emailinvaliderrormsg'] = '<div class="alert alert-danger"><b>ERREUR:</b>Adderesses par e-mail non valides (espace non autorisé)</div>';
$string['scheduledemaildisbled'] = '<div class="alert alert-success"><b>SUCCÈS:</b>Courriel planifié désactivé</div>';
$string['scheduledemailenabled'] = '<div class="alert alert-success"><b>SUCCÈS:</b>Email planifié activé</div>';
$string['noscheduleemails'] = 'Il n\'y a pas de courriels planifiés';

$string['nextrun'] = 'Prochain';
$string['frequency'] = 'La fréquence';
$string['manage'] = 'Faire en sorte';
$string['scheduleemailfor'] = 'Planifiez les e-mails pour';
$string['edit'] = 'Modifier';
$string['delete'] = 'Supprimer';

$string['report/edwiserreports_activeusersblock:editadvance'] = 'Modifier';

/* Custom Reports block related strings */
$string['customreport'] = 'Rapport personnalisé';
$string['customreportedit'] = 'Rapports personnalisés';
$string['customreportexportpdfnote'] = 'Si toutes les colonnes ne sont pas visibles, vous pouvez utiliser l\'option CSV ou Excel pour exporter le rapport.';
$string['reportspreview'] = 'Rapports Aperçu';
$string['reportsfilter'] = 'Rapports Filtre';
$string['noreportspreview'] = 'Aucun aperçu disponible';
$string['userfields'] = 'Champs d\'utilisateur';
$string['coursefields'] = 'Champs de cours';
$string['activityfields'] = 'Champs d\'activité';
$string['reportslist'] = 'Liste des rapports personnalisés';
$string['noreportslist'] = 'Pas de rapports personnalisés';
$string['allcohorts'] = 'Toutes les cohortes';
$string['allstudents'] = 'Tous les étudiants';
$string['allactivities'] = 'L\'ensemble des Activités';
$string['save'] = 'Sauver';
$string['reportname'] = 'Nom de rapport';
$string['reportshortname'] = 'Nom court';
$string['savecustomreport'] = 'Enregistrer le rapport personnalisé';
$string['downloadenable'] = 'Activer le téléchargement';
$string['emptyfullname'] = 'Le champ de nom de rapport est requis';
$string['emptyshortname'] = 'Signaler le champ de nom court est requis';
$string['nospecialchar'] = 'Signaler le champ de nom court ne permet pas de laisser un caractère spécial';
$string['reportssavesuccess'] = 'Rapports personnalisés Sauvés avec succès';
$string['reportssaveerror'] = 'Les rapports personnalisés n\'ont pas économisé';
$string['shortnameexist'] = 'Le nom court existe déjà';
$string['createdby'] = 'Auteure';
$string['sno'] = 'S. Non';
$string['datecreated'] = 'date créée';
$string['datemodified'] = 'Date modifiée';
$string['enabledesktop'] = 'Activé de bureau';
$string['noresult'] = 'Aucun résultat trouvé';
$string['enabledesktop'] = 'Ajouter au tableau de bord des rapports';
$string['disabledesktop'] = 'Supprimer du tableau de bord des rapports';
$string['editreports'] = 'Modifier les rapports';
$string['deletereports'] = 'Supprimer les rapports';
$string['deletesuccess'] = 'Rapports Supprimer avec succès';
$string['deletefailed'] = 'Les rapports de suppression ont échoué';
$string['deletecustomreportstitle'] = 'Supprimer le titre des rapports personnalisés';
$string['deletecustomreportsquestion'] = 'Voulez-vous vraiment supprimer ces rapports personnalisés?';
$string['createcustomreports'] = 'Créer / gérer le bloc de rapports personnalisés';
$string['searchreports'] = 'Rapports de recherche';
$string['title'] = 'Titre';
$string['createreports'] = 'Créer un nouveau rapport';
$string['updatereports'] = 'Mettre à jour les rapports';
$string['courseformat'] = 'Format de cours';
$string['completionenable'] = 'Activer l\'achèvement du cours';
$string['guestaccess'] = 'Accès au cours des clients';
$string['selectcourses'] = 'Sélectionnez des cours';
$string['selectcohorts'] = 'Sélectionner des cohortes';
$string['createnewcustomreports'] = 'Créer un nouveau rapport';

$string['invalidsecretkey'] = 'Clé secrète non valide.Veuillez vous connecter et vous connecter à nouveau.';
$string['unlockthisfeature'] = 'Disponible en version PRO';
$string['availableinpro'] = 'Disponible en version <br>PRO';
$string['upgradetopro'] = 'Passer à Pro';
$string['okaygotit'] = 'OK, j\'ai compris!';
$string['imponotice'] = 'AVIS IMPORTANT';
$string['csvprowarning'] = '<strong>L\'exportation vers CSV ne sera plus disponible dans la version 1.4.0</strong> (prochaine mise à jour GRATUITE d\'Edwiser Reports) car nous apportons des améliorations au niveau du code et des fonctionnalités de cette fonctionnalité. <br><strong>Veuillez noter :</strong> Il continuera à faire partie d\'Edwiser Reports PRO.';
$string['excelprowarning'] = '<strong>L\'exportation vers Excel ne sera plus disponible dans la version 1.4.0</strong> (prochaine mise à jour d\'Edwiser Reports GRATUITE) car nous apportons des améliorations au niveau du code et des fonctionnalités à cette fonctionnalité. <br><strong>Veuillez noter :</strong> Il continuera à faire partie d\'Edwiser Reports PRO.';
$string['emailprowarning'] = '<strong>La planification des e-mails ne sera plus disponible dans la version 1.4.0</strong> (prochaine mise à jour GRATUITE d\'Edwiser Reports) car nous apportons des améliorations au niveau du code et des fonctionnalités de cette fonctionnalité. <br><strong>Veuillez noter :</strong> Il continuera à faire partie d\'Edwiser Reports PRO.';
$string['courseengagementprowarning'] = '<strong>Le rapport d\'engagement de cours ne sera plus disponible dans la version 1.4.0 </strong> (Prochaine mise à jour GRATUITE des rapports Edwiser) car nous apportons des améliorations au niveau du code et des fonctionnalités à cette fonctionnalité. <br><strong>Veuillez noter : </strong> il continuera à faire partie d\'Edwiser Reports PRO.';

$string['time'] = 'Temps';

// Settings.
$string['generalsettings'] = 'réglages généraux';
$string['blockssettings'] = 'Paramètres de blocs';
$string['trackfrequency'] = 'Fréquence de mise à jour du journal temporel';
$string['trackfrequencyhelp'] = 'Ce paramètre vous aide à définir la fréquence de mise à jour du journal de temps utilisateur (séquence détaillée des activités utilisateur avec un horodatage) dans les donnéese.
';
$string['precalculated'] = 'Afficher les données pré-calculées <strong>(PRO)</strong>';
$string['precalculatedhelp'] = 'S\'il est activé, il se charge par les rapports hebdomadaires, mensuels et annuels plus rapidement.Ils sont en continu pré-calculé, traité et stocké en arrière-plan pour un chargement plus rapide des rapports.

Si elle est désactivée, ce processus de génération de rapports cesse de fonctionner en arrière-plan.De cette façon, le tableau de bord de rapport tirera, traitera et calculera les données requises à cet instant, uniquement lorsque vous demandez, c\'est-à-dire lorsque vous filtrez les rapports, augmentant le temps de chargement des rapports.

Nous vous recommandons d\'activer cette fonctionnalité.

<strong> Remarque: </strong> La tâche CRON doit être planifiée pour chaque heure pour obtenir des données précises.Éteignez ce paramètre si la tâche CRON n\'est pas définie pour s\'exécuter fréquemment.
';
$string['positionhelp'] = 'Définir la position du bloc sur le tableau de bord.';
$string['positionhelpupgrade'] = '<br> <strong> Remarque: Ne modifiez pas ce paramètre sur la page de mise à niveau.Vous pouvez réorganiser les blocs sur la page des paramètres du tableau de bord et de l\'administration. </ Strong>';
$string['desktopsize'] = 'Taille dans le bureau';
$string['desktopsizehelp'] = 'Taille du bloc dans les appareils de bureau';
$string['tabletsize'] = 'Taille dans la tablette';
$string['tabletsizehelp'] = 'Taille du bloc dans les tablettes';
$string['rolesetting'] = 'Rôles autorisés';
$string['rolesettinghelp'] = 'Définir quels utilisateurs peuvent afficher ce bloc';
$string['confignotfound'] = 'Configuration introuvable pour ce plugin';

// Settings for plugin upgrade.
$string['activeusersrolesetting'] = 'Présentation du site Bloc d\'état des rôles autorisés';
$string['courseprogressrolesetting'] = 'Le bloc de progrès du cours a autorisé les rôles';
$string['studentengagementrolesetting'] = 'Bloc de fiançailles des étudiants';
$string['learnerrolesetting'] = 'Le bloc d\'apprenant est autorisé les rôles';
$string['activecoursesrolesetting'] = 'Bloc de cours populaires autorisé les rôles';
$string['certificatesrolesetting'] = 'Certificats bloquer les rôles autorisés';
$string['liveusersrolesetting'] = 'Les utilisateurs en direct bloquent les rôles autorisés';
$string['siteaccessrolesetting'] = 'Bloc d\'information d\'accès du site Rôles autorisés';
$string['todaysactivityrolesetting'] = 'Le bloc d\'activités d\'aujourd\'hui a autorisé les rôles';
$string['inactiveusersrolesetting'] = 'Les utilisateurs inactifs bloquent les rôles autorisés';
$string['graderolesetting'] = 'Rôles autorisés au bloc';

$string['activeusersdesktopsize'] = 'Présentation du site Taille du bloc d\'état dans le bureau';
$string['courseprogressdesktopsize'] = 'Taille du bloc de progression du cours dans le bureau';
$string['studentengagementdesktopsize'] = 'Taille du bloc de fiançailles des étudiants dans le bureau';
$string['learnerdesktopsize'] = 'Taille du blocage de l\'apprenant dans le bureau';
$string['activecoursesdesktopsize'] = 'Cours populaires bloquer la taille du bureau';
$string['certificatesdesktopsize'] = 'Certificats Taille de blocs dans le bureau';
$string['liveusersdesktopsize'] = 'Les utilisateurs en direct sont de la taille du bureau dans le bureau';
$string['siteaccessdesktopsize'] = 'Taille du bloc d\'informations d\'accès du site dans le bureau';
$string['todaysactivitydesktopsize'] = 'Taille du bloc d\'activité d\'aujourd\'hui dans le bureau';
$string['inactiveusersdesktopsize'] = 'Les utilisateurs inactifs bloquent la taille du bureau';
$string['gradedesktopsize'] = 'Taille de blocs de qualité dans le bureau';

$string['activeuserstabletsize'] = 'Présentation du site Taille du bloc d\'état dans la tablette';
$string['courseprogresstabletsize'] = 'Taille du bloc de progression du cours dans la tablette';
$string['studentengagementtabletsize'] = 'Taille du bloc de fiançailles des étudiants dans la tablette';
$string['learnertabletsize'] = 'Taille du blocage de l\'apprenant dans la tablette';
$string['activecoursestabletsize'] = 'Cours populaires bloquer la taille de la tablette';
$string['certificatestabletsize'] = 'Certificats Taille de blocs dans la tablette';
$string['liveuserstabletsize'] = 'Les utilisateurs en direct sont de taille dans la tablette';
$string['siteaccesstabletsize'] = 'Taille du bloc d\'informations d\'accès du site dans la tablette';
$string['todaysactivitytabletsize'] = 'Taille du bloc d\'activité d\'aujourd\'hui dans la tablette';
$string['inactiveuserstabletsize'] = 'Les utilisateurs inactifs bloquent la taille de la tablette';
$string['gradetabletsize'] = 'Taille de blocage de qualité dans la tablette';

$string['activeusersposition'] = 'Présentation du site Bloc d\'état';
$string['courseprogressposition'] = 'Position du bloc de progrès du cours';
$string['studentengagementposition'] = 'Position du bloc d\'engagement des étudiants';
$string['learnerposition'] = 'Position de l\'apprenant';
$string['activecoursesposition'] = 'Les cours populaires bloquent la position de';
$string['certificatesposition'] = 'Certificats Bloquer la position';
$string['liveusersposition'] = 'Les utilisateurs vivants bloquent la position';
$string['siteaccessposition'] = 'Position du bloc d\'accès au site';
$string['todaysactivityposition'] = 'Position du bloc d\'activité d\'aujourd\'hui';
$string['inactiveusersposition'] = 'Les utilisateurs inactifs bloquent la position de';
$string['gradeposition'] = 'Position du bloc de qualité';

// Visits On Site block.
$string['visitsonsiteheader'] = 'Visites sur place';
$string['visitsonsiteblockhelp'] = 'Le nombre de visites Les utilisateurs ont eu sur votre site dans une session utilisateur donnée.La durée de la session est définie dans les paramètres d\'Edwiser Rapports.';
$string['visitsonsiteblockview'] = 'Visites sur le site View';
$string['visitsonsiteblockedit'] = 'Visites sur place Modifier';
$string['visitsonsiterolesetting'] = 'Visites sur place Rôles autorisés';
$string['visitsonsitedesktopsize'] = 'Visites sur la taille du site dans le bureau';
$string['visitsonsitetabletsize'] = 'Visites sur la taille du site dans la tablette';
$string['visitsonsiteposition'] = 'Visites sur le site de la position';
$string['visitsonsiteblockexportheader'] = 'Visites sur le rapport du site';
$string['visitsonsiteblockexporthelp'] = 'Ce rapport affichera les visites sur les données exportées du site.';
$string['visitsonsiteblockeditadvance'] = 'Visites sur le site Bloc Advance Modifier';
$string['averagesitevisits'] = 'Visites moyennes du site';
$string['totalsitevisits'] = 'Visites totales du site';

// Time spent on site block.
$string['timespentonsiteheader'] = 'Temps passé sur place';
$string['timespentonsiteblockhelp'] = 'Temps passé par les utilisateurs sur votre site en une journée.';
$string['timespentonsiteblockview'] = 'Temps passé sur la vue du site';
$string['timespentonsiteblockedit'] = 'Temps passé sur le site Modifier';
$string['timespentonsiterolesetting'] = 'Le temps passé sur place autorisé les rôles';
$string['timespentonsitedesktopsize'] = 'Temps passé sur la taille du site dans le bureau';
$string['timespentonsitetabletsize'] = 'Temps passé sur la taille du site dans la tablette';
$string['timespentonsiteposition'] = 'Temps passé sur le site de la position';
$string['timespentonsiteblockexportheader'] = 'Temps passé sur le rapport';
$string['timespentonsiteblockexporthelp'] = 'Ce rapport affichera le temps passé sur les données exportées sur le site.';
$string['timespentonsiteblockeditadvance'] = 'Temps passé sur le site Bloc Advance Modifier';
$string['averagetimespent'] = 'Temps moyen passé';
$string['totaltimespent'] = 'Total des temps passés';

// Time spent on course block.
$string['timespentoncourseheader'] = 'Temps passé sur le parcours';
$string['timespentoncourseblockhelp'] = 'Temps passé par les apprenants dans des cours particuliers en une journée.';
$string['timespentoncourseblockview'] = 'Temps passé sur la vue du cours';
$string['timespentoncourseblockedit'] = 'Temps passé sur le cours Modifier';
$string['timespentoncourserolesetting'] = 'Le temps passé sur les caps a autorisé les rôles';
$string['timespentoncoursedesktopsize'] = 'Temps passé sur la taille du cours dans le bureau';
$string['timespentoncoursetabletsize'] = 'Temps passé sur la taille du cours dans la tablette';
$string['timespentoncourseposition'] = 'Temps passé sur la position du cours';
$string['timespentoncourseblockexportheader'] = 'Temps passé sur le rapport de cours';
$string['timespentoncourseblockexporthelp'] = 'Ce rapport montrera le temps passé sur les données exportées par cours.';
$string['timespentoncourseblockeditadvance'] = 'Temps passé sur le bloc de cours Advance modifier';

// Course activity block.
$string['courseactivitystatusheader'] = 'État d\'activité du cours';
$string['courseactivitystatusblockhelp'] = 'Activités de cours effectuées par les apprenants.Il s\'agit d\'une combinaison d\'activités terminées et de missions soumises graphiques en ligne.';
$string['courseactivitystatusblockview'] = 'Vue d\'activité du cours';
$string['courseactivitystatusblockedit'] = 'État de l\'activité du cours Modifier';
$string['courseactivitystatusrolesetting'] = 'Le statut d\'activité du cours a permis des rôles';
$string['courseactivitystatusdesktopsize'] = 'Taille de l\'état de l\'activité du cours dans le bureau';
$string['courseactivitystatustabletsize'] = 'Taille de l\'état de l\'activité du cours dans la tablette';
$string['courseactivitystatusposition'] = 'Position de l\'état de l\'activité du cours';
$string['courseactivitystatusblockexportheader'] = 'Rapport d\'état d\'activité du cours';
$string['courseactivitystatusblockexporthelp'] = 'Ce rapport affichera les données exportées de l\'état de l\'activité du cours.';
$string['courseactivitystatusblockeditadvance'] = 'Statut d\'activité du cours Bloc Advance Modifier';
$string['averagecompletion'] = 'Activité moyenne terminée';
$string['totalassignment'] = 'Affectation totale soumise';
$string['totalcompletion'] = 'Activité totale terminée';

// Learner Course Progress block.
$string['learnercourseprogressheader'] = 'Mon cours progressive';
$string['learnercourseprogressblockhelp'] = 'Votre cours progresse dans un cours particulier.';
$string['learnercourseprogressblockview'] = 'Ma vue de progrès du cours';
$string['learnercourseprogressblockedit'] = 'Mon cours Progress Modifier';
$string['learnercourseprogressrolesetting'] = 'Mes progrès de cours ont permis des rôles';
$string['learnercourseprogressdesktopsize'] = 'Mon cours de progression de la taille dans le bureau';
$string['learnercourseprogresstabletsize'] = 'Mon cours de progrès dans la tablette';
$string['learnercourseprogressposition'] = 'La position de mon cours progressait';
$string['learnercourseprogressblockexportheader'] = 'Mon rapport d\'étape de cours';
$string['learnercourseprogressblockexporthelp'] = 'Ce rapport montrera les données exportées par mes progrès de cours.';
$string['learnercourseprogressblockeditadvance'] = 'Mon cours Progress Block Advance Modifier';

// Learner Time spent on site block.
$string['learnertimespentonsiteheader'] = 'Mon temps passé sur place';
$string['learnertimespentonsiteblockhelp'] = 'Votre temps passé sur le site en une journée.';
$string['learnertimespentonsiteblockview'] = 'Mon temps passé sur la vue du site';
$string['learnertimespentonsiteblockedit'] = 'Mon temps passé sur le site Modifier';
$string['learnertimespentonsiterolesetting'] = 'Mon temps passé sur place a permis des rôles';
$string['learnertimespentonsitedesktopsize'] = 'Mon temps passé sur la taille du site dans le bureau';
$string['learnertimespentonsitetabletsize'] = 'Mon temps passé sur la taille du site dans la tablette';
$string['learnertimespentonsiteposition'] = 'Mon temps passé sur le site de la position';
$string['learnertimespentonsiteblockexportheader'] = 'Mon temps passé sur le site';
$string['learnertimespentonsiteblockexporthelp'] = 'Ce rapport affichera le temps passé sur le site exporté de données.';
$string['learnertimespentonsiteblockeditadvance'] = 'Mon temps passé sur le site Block Advance Modifier';
$string['site'] = 'Site';
$string['completed-y'] = 'Terminé';
$string['completed-n'] = 'Pas achevé';

// Top page insights.
$string['newregistrations'] = 'Nouvelles inscriptions';
$string['courseenrolments'] = 'Inscription au cours';
$string['coursecompletions'] = 'Achèvement du cours';
$string['activeusers'] = 'Utilisateurs actifs';
$string['activitycompletions'] = 'Complétion des activités';
$string['timespentoncourses'] = 'Temps passé sur les cours';
$string['totalcoursesenrolled'] = 'Total des cours inscrits';
$string['coursecompleted'] = 'Cours terminé';
$string['activitiescompleted'] = 'Activités terminées';
