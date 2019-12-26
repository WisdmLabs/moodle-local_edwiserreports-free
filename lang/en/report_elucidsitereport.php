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

$string['pluginname'] = 'Reports & Analytics Dashboard';
$string['reportsandanalytics'] = "Reports & Analytics";
$string['all'] = "All";
$string['refresh'] = "Refresh";

/* Blocks Name */
$string['realtimeusers'] = 'Real Time Users';
$string['activeusersheader'] = 'Active Users, Course Enrolment and Course Completion Rate';
$string['courseprogress'] = 'Course Progress';
$string['courseengagement'] = 'Course Engagement';
$string['coursereports'] = 'Course Reports';
$string['coursereportsheader'] = 'Course Reports';
$string['more'] = '<i class="fa fa-angle-right"></i> More';
$string['activecoursesheader'] = 'Popular Courses';
$string['f2fsessionsheader'] = 'Instructor-Led Sessions';
$string['certificatestats'] = 'Certificates Stats';
$string['certificatestatsheader'] = 'Certificates Stats';
$string['lpstatsheader'] = 'Learning Program Stats';
$string['accessinfo'] = 'Site Access Information';
$string['inactiveusers'] = 'Inactive Users List';
$string['todaysactivityheader'] = 'Daily Activities';
$string['overallengagementheader'] = 'Overall Engagement in Courses';
$string['inactiveusersexportheader'] = 'Inactive Users Report';
$string['date'] = "Date";
$string['time'] = "Time";
$string['venue'] = "Venue";
$string['signups'] = "Signups";
$string['attendees'] = "Attendees";
$string['name'] = "Name";
$string['course'] = "Course";
$string['issued'] = "Issued";
$string['notissued'] = "Not Issued";
$string['nof2fmodule'] = "There is no face to face sessions are available.";
$string['nof2fsessions'] = "There is no face to face session available for this module.";
$string['nocertificates'] = "There is no certificate created";

// Breakdown the tooltip string to display in 2 lines
$string['cpblocktooltip1'] = '{$a->per} course completed';
$string['cpblocktooltip2'] = 'by {$a->val} users';

$string['lpstatstooltip'] = '{$a->data} users completed {$a->label}.';
$string['fullname'] = "Full Name";
$string['onlinesince'] = "Online Since";
$string['status'] = "Status";
$string['todayslogin'] = "Daily Login";
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
$string['custom'] = "Custom Date";
$string['rank'] = "Rank";
$string['enrolments'] = "Enrolments";
$string['visits'] = "Visits";
$string['completions'] = "Completions";
$string['selectdate'] = "Select Date";
$string['never'] = "Never";

/* Block help tooltips */
$string['activeusersblocktitlehelp'] = "Active Users Block";
$string['activeusersblockhelp'] = "This block will show graph of active users over the period with course enrolment and course completion.";
$string['courseprogressblockhelp'] = "This block will show the pie chart of a course with percantage.";
$string['activecoursesblockhelp'] = "This block will show the most active courses based on the visits enrolment and completions.";
$string['certificatestatsblockhelp'] = "This block will show all created custom certificates and how many enrolled users awarded with this certificates.";
$string['realtimeusersblockhelp'] = "This block will show all logged in users in this site.";
$string['f2fsessionsblockhelp'] = "This block will show all created face to face sessions and count of all signups and attendees.";
$string['accessinfoblockhelp'] = "This block will show the avg usage of the site in a week.";
$string['lpstatsblockhelp'] = "This block will show all the course completed by the users in a learning program.";
$string['todaysactivityblockhelp'] = "This block will show the todays activities performed by in this website.";
$string['inactiveusersblockhelp'] = "This block will show list of users inactive in this site.";
$string['inactiveusersexporthelp'] = "This report will show inactivity of users in the website";
$string['none'] = "None";

/* Block Course Progress */
$string['nocourses'] = "No Courses Found";

/* Block Learning Program */
$string['nolearningprograms'] = "No Learning Programs Found";

/* Block Inactive Users */
$string['siteaccess'] = "Site Access:";
$string['before1month'] = "Before 1 Month";
$string['before3month'] = "Before 3 Month";
$string['before6month'] = "Before 6 Month";

/* Active Users Page */
$string['noofactiveusers'] = "No. of active users";
$string['noofenrolledusers'] = "No. of enrolled users";
$string['noofcompletedusers'] = "No. of completed users";
$string['email'] = "Email";
$string['emailscheduled'] = "Email Scheduled";
$string['usersnotavailable'] = "No Users are available for this day";
$string['activeusersmodaltitle'] = 'Users active on {$a->date}';
$string['enrolmentsmodaltitle'] = 'Users enrolled into courses on {$a->date}';
$string['completionsmodaltitle'] = 'Users who have completed a course on {$a->date}';

/* Course Progress Page */
$string['coursename'] = "Course Name";
$string['enrolled'] = "Enrolled";
$string['completed'] = "Completed";
$string['per40-20'] = "20% - 40%";
$string['per60-40'] = "40% - 60%";
$string['per80-60'] = "60% - 80%";
$string['per100-80'] = "80% - 100%";
$string['per20-0'] = "0% - 20%";
$string['per100'] = "100%";

/* Certificates Page */
$string['username'] = 'User Name';
$string['useremail'] = 'User Email';
$string['dateofissue'] = 'Date of Issue';
$string['dateofenrol'] = 'Date of Enrolment';
$string['grade'] = 'Grade';
$string['courseprogress'] = 'Course Progress';
$string['notenrolled'] = 'User Not Enrolled';

/* f2f Sessions Block */
$string['attended'] = "Attended";
$string['requested'] = "Requested";
$string['canceled'] = "Cancelled";
$string['approved'] = "Approved";
$string['booked'] = "Booked";
$string['f2fmore'] = "More";
$string['download'] = "Download";

/* Site Access Block*/
$string['sun'] = "SUN";
$string['mon'] = "MON";
$string['tue'] = "TUE";
$string['wed'] = "WED";
$string['thu'] = "THU";
$string['fri'] = "FRI";
$string['sat'] = "SAT";
$string['time00'] = "12:00 AM";
$string['time01'] = "01:00 AM";
$string['time02'] = "02:00 AM";
$string['time03'] = "03:00 AM";
$string['time04'] = "04:00 AM";
$string['time05'] = "05:00 AM";
$string['time06'] = "06:00 AM";
$string['time07'] = "07:00 AM";
$string['time08'] = "08:00 AM";
$string['time09'] = "09:00 AM";
$string['time10'] = "10:00 AM";
$string['time11'] = "11:00 AM";
$string['time12'] = "12:00 PM";
$string['time13'] = "01:00 PM";
$string['time14'] = "02:00 PM";
$string['time15'] = "03:00 PM";
$string['time16'] = "04:00 PM";
$string['time17'] = "05:00 PM";
$string['time18'] = "06:00 PM";
$string['time19'] = "07:00 PM";
$string['time20'] = "08:00 PM";
$string['time21'] = "09:00 PM";
$string['time22'] = "10:00 PM";
$string['time23'] = "11:00 PM";
$string['siteaccessinfo'] = "Avg users access";

/* f2f Sessions Page*/
$string['waitlist'] = 'Waitlist';
$string['declined'] = 'Declined';
$string['reason'] = '(If Cancelled) Reason';
$string['confirmed'] = 'Confirmed';
$string['nosignups'] = 'No Signups are available';
$string['nosessions'] = 'There is no face to face sessions';

/* Learning Program Page */
$string['lastaccess'] = "Last Access";
$string['progress'] = "Progress";
$string['avgprogress'] = "Avg Progress";
$string['notyet'] = "Not Yet";
$string['lpname'] = "Learning Program Name";
$string['lpdetailedreport'] = "Download Learning Programs Detailed Report";

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
$string['reportemailhelp'] = "Report will be send to this email address.";
$string['emailnotsent'] = "Failed to send email";
$string['subject'] = "Subject";
$string['content'] = "Content";
$string['emailexample'] = "example1.mail.com; example2.mail.com;";

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
$string['courseengageexportheader'] = "Course Engagement Report";
$string['courseengageexporthelp'] = "This report will show the course engagement by the users.";
$string['completionexportheader'] = "Course Completion Report";
$string['completionexporthelp'] = "This report will show the course completions by the users.";
$string['courseanalyticsexportheader'] = "Course Completion Report";
$string['courseanalyticsexporthelp'] = "This report will show the course completions by the users.";
$string['exportlpdetailedreports'] = 'Export Detailed Reports';

$string['times_0'] = "06:30 AM";
$string['times_1'] = "10:00 AM";
$string['times_2'] = "04:30 PM";
$string['times_3'] = "10:30 PM";
$string['week_0'] = "Sunday";
$string['week_1'] = "Monday";
$string['week_2'] = "Tuesday";
$string['week_3'] = "Wednesday";
$string['week_4'] = "Thursday";
$string['week_5'] = "Friday";
$string['week_6'] = "Saturday";
$string['monthly_0'] = "Month Start";
$string['monthly_1'] = "Month Between";
$string['monthly_2'] = "Month End";
$string['weeks_on'] = "Weeks on";
$string['emailthisreport'] = "Email this report";
$string['onevery'] = "on every";
$string['duration_0'] = "Daily";
$string['duration_1'] = "Weekly";
$string['duration_2'] = "Monthly";
$string['everydays'] = 'Everyday {$a->time}';
$string['everyweeks'] = 'Every {$a->day}';
$string['everymonths'] = 'Every month at {$a->time}';
$string['schedule'] = "Schedule";
$string['scheduledlist'] = "All Scheduled Reports";
$string['reset'] = "Reset";
$string['confirmemailremovaltitle'] = "Delete Scheduled Email";
$string['confirmemailremovalquestion'] = "<p class='px-20'>Do you really want to delete this sheduled email</p>";

/* Course Engagement Block */
$string['activitystart'] = "At least one Activity Started";
$string['completedhalf'] = "Completed 50% of Courses";
$string['coursecompleted'] = "Course Completed";
$string['nousersavailable'] = "No Users Available";

/* Course Completion Page */
$string['completionheader'] = 'Course Completion Reports: {$a->coursename}';
$string['completionreports'] = "Completion Reports";
$string['activitycompletion'] = "Activity Completion";
$string['completionpercantage'] = "Completion Percentage";
$string['activitycompleted'] = '{$a->completed} out of {$a->total}';

/* Course Analytics Page */
$string['courseanalytics'] = 'Course Analytics';
$string['courseanalyticsheader'] = 'Course Analytics: {$a->coursename}';
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

/* Cron Task Strings */
$string['updatetables'] = "Updating Reports and Analytics Table";
$string['updatingrecordstarted'] = "Updating reports and analytics record is srated...";
$string['updatingrecordended'] = "Updating reports and analytics record is ended...";
$string['updatinguserrecord'] = 'Updating userid {$a->userid} in courseid {$a->courseid}';
$string['deletingguserrecord'] = 'Deleting userid {$a->userid} in courseid {$a->courseid}';
$string['gettinguserrecord'] = 'Getting userid {$a->userid} in courseid {$a->courseid}';
$string['creatinguserrecord'] = 'Create records for users completions';
$string['sendscheduledemails'] = 'Send Scheduled Emails';
$string['sendingscheduledemails'] = 'Sending Scheduled Emails...';

/* Cache Strings */
$string['cachedef_elucidsitereport'] = 'This is the caches of elucid site report';

/* Capabilties */
$string['elucidsitereport:view'] = 'View Reports and analytics dashboard';

/* Custom report block */
$string['downloadcustomtreport'] = 'Download Users Progress Report';
$string['selectdaterange'] = 'Select Date Range';
$string['learningprograms'] = 'Learning Programs';
$string['courses'] = 'Courses';
$string['shortname'] = 'Shortname';
$string['downloadreportincsv'] = 'Download Reports in CSV';
$string['startdate'] = 'Start Date';
$string['enddate'] = 'End Date';
$string['select'] = 'Select';
$string['selectreporttype'] = 'Select Report Type';
$string['completedactivities'] = 'Activity Completed';
$string['completionsper'] = 'Completion(%)';
$string['firstname'] = 'First Name';
$string['lastname'] = 'Last Name';
$string['average'] = 'Average(%)';
$string['enrolmentrangeselector'] = 'Enrolment Range Selector';
$string['category'] = 'Category';
$string['customreportsuccess'] = '<h4><i class="fa fa-check" aria-hidden="true"></i> Success!</h4>Notifications sent successfully.';
$string['customreportfailed'] = '<h4><i class="fa fa-check" aria-hidden="true"></i> Failed!</h4>Select any of the checkboxes to get reports.';
$string['duration'] = 'Duration';
$string['na'] = 'NA';