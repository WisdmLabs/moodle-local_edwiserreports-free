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
 * @package     report_elucidsitereport
 * @category    string
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'eLucid Site Report';
$string['reportsandanalytics'] = "Reports & Analytics Dashboard";
$string['all'] = "All";

/* Blocks Name */
$string['realtimeusers'] = 'Real Time Users';
$string['activeusersheader'] = 'Active Users, Course Enrolment and Course Completion Rate';
$string['courseprogress'] = 'Course Progress';
$string['courseengagement'] = 'Course Engagement';
$string['coursereports'] = 'Course Reports';
$string['more'] = '<i class="fa fa-angle-right"></i> More';
$string['mostactiveusersheader'] = 'Popular Courses';
$string['f2fsessionsheader'] = 'Instructor-Led Sessions';
$string['certificatestats'] = 'Certificates Stats';
$string['lpstatsheader'] = 'Learning Program Stats';
$string['accessinfo'] = 'Site Access Information';
$string['inactiveusers'] = 'Inactive Users List';
$string['todaysactivityheader'] = 'Today\'s Activities';
$string['overallengagementheader'] = 'Overall Engagement in Courses';
$string['date'] = "Date";
$string['time'] = "Time";
$string['venue'] = "Venue";
$string['signups'] = "Signups";
$string['attendees'] = "Attendees";
$string['name'] = "Name";
$string['course'] = "Course";
$string['issued'] = "Issued";
$string['notissued'] = "Not Issued";
$string['nof2fmodule'] = "There is no previous face to face session available.";
$string['nof2fsessions'] = "There is no face to face session available for this module.";
$string['nocertificates'] = "There is no certificate created";
$string['courseprogresstooltip'] = '{$a->label} courses are completed by {$a->data} users.';
$string['fullname'] = "Full Name";
$string['onlinesince'] = "Online Since";
$string['status'] = "Status";
$string['todayslogin'] = "Today's Login";
$string['learners'] = "Learners";
$string['teachers'] = "Teachers";
$string['eventtoday'] = "Event Today";
$string['value'] = "Value";
$string['enrollment'] = "Enrollments";
$string['activitycompletion'] = "Activity Completions";
$string['coursecompletion'] = "Course Completions";
$string['newregistration'] = "New Registrations";
$string['visits'] = "Visits";
$string['timespent'] = "Time Spent";
$string['sessions'] = "Sessions";
$string['totalusers'] = "Total Users";
$string['pageview'] = "Page Views per Hour";
$string['lastupdate'] = "Last updated <span class='minute'>0</span> min ago";
$string['lastweek'] = "Last Week";
$string['lastmonth'] = "Last Month";
$string['lastyear'] = "Last Year";
$string['custom'] = "Custom";
$string['rank'] = "Rank";
$string['enrolments'] = "Enrolments";
$string['visits'] = "Visits";
$string['completions'] = "Completions";

/* Block help tooltips */
$string['activeusersblocktitlehelp'] = "Active Users Block";
$string['activeusersblockhelp'] = "This block will show graph of active users over the period with course enrolment and course completion.";

/* Active Users Page */
$string['noofactiveusers'] = "# Active Users";
$string['noofenrolledusers'] = "# Enrolled Users";
$string['noofcompletedusers'] = "# Completed Users";
$string['email'] = "Email";
$string['usersnotavailable'] = "No Users are available for this day";

/* Course Progress Page */
$string['coursename'] = "Course Name";
$string['noofenrolled'] = "Enrolled Users";
$string['noofcompleted'] = "Completed Users";
$string['noofcompleted20'] = "Atleast 20% Completed";
$string['noofcompleted40'] = "Atleast 40% Completed";
$string['noofcompleted60'] = "Atleast 60% Completed";
$string['noofcompleted80'] = "Atleast 80% Completed";
$string['noofincompleted'] = "Not Completed";

/* Certificates Page */
$string['username'] = 'User Name';
$string['useremail'] = 'User Email';
$string['dateofissue'] = 'Date of Issue';
$string['dateofenrol'] = 'Date of Enrolment';
$string['grade'] = 'Grade';
$string['courseprogress'] = 'Course Progress';
$string['notenrolled'] = 'User Not Enrolled';

/* f2f Sessions Page*/
$string['waitlist'] = 'Waitlist';
$string['declined'] = 'Declined';
$string['reason'] = 'Reason';
$string['confirmed'] = 'Confirmed';
$string['nosignups'] = 'No Signups are available';
$string['nosessions'] = 'There is no face to face sessions';

/* Learning Program Page */
$string['enrolled'] = "Enrolled On";
$string['lastaccess'] = "Last Access";
$string['progress'] = "Progress";
$string['avgprogress'] = "Avg Progress";
$string['notyet'] = "Not Yet";
$string['lpname'] = "Learning Program Name";

/* Export Strings */
$string['csv'] = "CSV";
$string['excel'] = "Excel";
$string['pdf'] = "PDF";
$string['email'] = "Email";
$string['copy'] = "Copy";
$string['activeusers_status'] = "User Active";
$string['enrolments_status'] = "User Enrolled";
$string['completions_status'] = "Course Completed";
$string['completedactivity'] = "Completed Activity";
$string['coursecompletedusers'] = "Course Completed By Users";
$string['emailsent'] = "Email has been sent to your mail account";

$string['activeusersexportheader'] = "Active Users, Course Enrolment and Course Completion Rate";
$string['activeusersexporthelp'] = "This report will show active users, course enrolment and course completion over the period.";
$string['courseprogressexportheader'] = "Course Progress Report";
$string['courseprogressexporthelp'] = "This report will show the course progress of a perticuler course by the users.";
$string['activecoursesexportheader'] = "Most active course report";
$string['activecoursesexporthelp'] = "This report will show the most active courses based on the enrolments, visits and completions.";
$string['certificatesexportheader'] = "Awarded certificates report";
$string['certificatesexporthelp'] = "This report will show the certificates who have issued or not issue issue to enrolled users.";
$string['f2fsessionexportheader'] = "Instructor-Led Sessions report";
$string['f2fsessionexporthelp'] = "This report will show the Instructor-Led Sessions details.";
$string['lpstatsexportheader'] = "Learning Program report";
$string['lpstatsexporthelp'] = "This report will show the Learning program details.";

/* Course Engagement Block */
$string['activitystart'] = "At least one Activity Started";
$string['completedhalf'] = "Completed 50% of Courses";
$string['coursecompleted'] = "Course Completed";
$string['nousersavailable'] = "No Users Available";

/* Course Completion Page */
$string['completionheader'] = "Course Completion Reports";
$string['completionreports'] = "Completion Reports";
$string['activitycompletion'] = "Activity Completion";
$string['completionpercantage'] = "Completion Percentage";
$string['activitycompleted'] = '{$a->completed} out of {$a->total}';

/* Course Analytics Page */
$string['courseanalyticsheader'] = "Course Analytics";
$string['recentvisits'] = "Recent Visits";
$string['lastvisit'] = "Last Visit";
$string['enrolledon'] = "Enrolled On";
$string['enrolltype'] = "Enrolment Type";
$string['noofvisits'] = "Number of visits";
$string['completiontime'] = "Completion Time";
$string['spenttime'] = "Spent Time";
$string['completedon'] = "Completed On";
$string['recentcompletion'] = "Recent Completion";
$string['recentenrolment'] = "Recent Enrolments";
$string['recentvisits'] = "Recent Visits";
