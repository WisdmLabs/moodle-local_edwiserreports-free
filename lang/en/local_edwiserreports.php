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

$string['pluginname'] = 'Edwiser Reports';
$string['reportsdashboard'] = 'Reports & Analytics Dashboard';
$string['reportsandanalytics'] = "Reports & Analytics";
$string['all'] = "All";
$string['refresh'] = "Refresh";
$string['noaccess'] = "Sorry. You don't have rights to access this page.";

/* Blocks Name */
$string['realtimeusers'] = 'Real Time Users';
$string['activeusersheader'] = 'Active Users, Course Enrolment and Course Completion Rate';
$string['courseprogress'] = 'Course Progress';
$string['courseprogressheader'] = 'Course Progress';
$string['courseengagement'] = 'Course Engagement';
$string['coursereports'] = 'Course Reports';
$string['coursereportsheader'] = 'Course Reports';
$string['more'] = '<i class="fa fa-angle-right"></i> More';
$string['activecoursesheader'] = 'Popular Courses';
$string['f2fsessionsheader'] = 'Instructor-Led Sessions';
$string['certificatestats'] = 'Certificates Stats';
$string['certificatestatsheader'] = 'Certificates Stats';
$string['certificatesheader'] = 'Certificates Stats';
$string['lpstatsheader'] = 'Learning Program Stats';
$string['accessinfo'] = 'Site Access Information';
$string['siteaccessheader'] = 'Site Access Information';
$string['inactiveusers'] = 'Inactive Users List';
$string['inactiveusersheader'] = 'Inactive Users List';
$string['liveusersheader'] = 'Live Users Block';
$string['todaysactivityheader'] = 'Daily Activities';
$string['overallengagementheader'] = 'Overall Engagement in Courses';
$string['inactiveusersexportheader'] = 'Inactive Users Report';
$string['inactiveusersblockexportheader'] = 'Inactive Users Report';
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
$string['unselectall'] = 'Unselect All';
$string['selectall'] = 'Select All';
$string['activity'] = 'Activity';
$string['cohorts'] = 'Cohorts';

// Breakdown the tooltip string to display in 2 lines.
$string['cpblocktooltip1'] = '{$a->per} course completed';
$string['cpblocktooltip2'] = 'by {$a->val} users';

$string['lpstatstooltip'] = '{$a->data} users completed {$a->label}.';
$string['fullname'] = "Full Name";
$string['onlinesince'] = "Online Since";
$string['status'] = "Status";
$string['todayslogin'] = "Login";
$string['learners'] = "Learners";
$string['teachers'] = "Teachers";
$string['eventtoday'] = "Events of the day";
$string['value'] = "Value";
$string['count'] = 'Count';
$string['enrollment'] = "Enrollments";
$string['activitycompletion'] = "Activity Completions";
$string['coursecompletion'] = "Course Completions";
$string['newregistration'] = "New Registrations";
$string['visits'] = "Visits";
$string['timespent'] = "Time Spent";
$string['sessions'] = "Sessions";
$string['totalusers'] = "Total Users";
$string['sitevisits'] = "Site Visits Per Hour";
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
$string['recipient'] = "Recipient";
$string['subject'] = "Subject";
$string['message'] = "Message";
$string['reset'] = "Reset";
$string['send'] = "Send Now";


/* Block help tooltips */
$string['activeusersblocktitlehelp'] = "This block will show active users, course enrolment and course completion over the period in line chart.";
$string['activeusersblockhelp'] = "This block will show graph of active users over the period with course enrolment and course completion.";
$string['courseprogressblockhelp'] = "This block will show the pie chart of a course with percantage.";
$string['activecoursesblockhelp'] = "This block will show the most active courses based on the visits enrolment and completions.";
$string['certificatestatsblockhelp'] = "This block will show all created custom certificates and how many enrolled users awarded with this certificates.";
$string['realtimeusersblockhelp'] = "This block will show all logged in users in this site.";
$string['f2fsessionsblockhelp'] = "This block will show all created face to face sessions and count of all signups and attendees.";
$string['accessinfoblockhelp'] = "This block will show the average usage of the site in a week.";
$string['lpstatsblockhelp'] = "This block will show all the course completed by the users in a learning program.";
$string['todaysactivityblockhelp'] = "This block will show the daily activities performed in this site.";
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
$string['noofenrolledusers'] = "No. of enrollments";
$string['noofcompletedusers'] = "No. of completions";
$string['email'] = "Email";
$string['emailscheduled'] = "Email Scheduled";
$string['usersnotavailable'] = "No Users are available for this day";
$string['activeusersmodaltitle'] = 'Users active on {$a->date}';
$string['enrolmentsmodaltitle'] = 'Users enrolled into courses on {$a->date}';
$string['completionsmodaltitle'] = 'Users who have completed a course on {$a->date}';
$string['recordnotfound'] = 'Record not found';
$string['jsondecodefailed'] = 'Json decode failed';
$string['emaildataisnotasarray'] = 'Email data is not an array';
$string['sceduledemailnotexist'] = 'Schedule email not exist';

/* Course Progress Page */
$string['coursename'] = "Course Name";
$string['enrolled'] = "Enrolled";
$string['completed'] = "Completed";
$string['inprogress'] = "In Progress";
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
$string['searchlps'] = "Search Learning Programs";
$string['exportlpdetailedreport'] = "Export Detailed Report";

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

$string['activeusersblockexportheader'] = "Active Users, Course Enrolment and Course Completion Rate";
$string['activeusersblockexporthelp'] = "This report will show active users, course enrolment and course completion over the period.";
$string['courseprogressblockexportheader'] = "Course Progress Report";
$string['courseprogressblockexporthelp'] = "This report will show the course progress of a particular course by the users.";
$string['activecoursesblockexportheader'] = "Most active course report";
$string['activecoursesblockexporthelp'] = "This report will show the most active courses based on the enrolments, visits and completions.";
$string['certificatesblockexportheader'] = "Awarded certificates report";
$string['certificatesblockexporthelp'] = "This report will show the certificates who have issued or not issued to enrolled users.";
$string['f2fsessionblockexportheader'] = "Instructor-Led Sessions report";
$string['f2fsessionblockexporthelp'] = "This report will show the Instructor-Led Sessions details.";
$string['lpstatsblockexportheader'] = "Learning Program report";
$string['lpstatsblockexporthelp'] = "This report will show the Learning program details.";
$string['courseengageblockexportheader'] = "Course Engagement Report";
$string['courseengageblockexporthelp'] = "This report will show the course engagement by the users.";
$string['completionblockexportheader'] = "Course Completion Report";
$string['completionexportheader'] = "Course Completion Report";
$string['completionblockexporthelp'] = "This report will show the course completions by the users.";
$string['completionexporthelp'] = "This report will show the course completions by the users.";
$string['courseanalyticsblockexportheader'] = "Course Completion Report";
$string['courseanalyticsblockexporthelp'] = "This report will show the course completions by the users.";
$string['exportlpdetailedreports'] = 'Export Detailed Reports';
$string['inactiveusersblockexporthelp'] = "This report will show inactivity of users in the website";

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
$string['schedule'] = "Schedule Email";
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
$string['cachedef_edwiserReport'] = 'This is the caches of elucid site report';

/* Capabilties */
$string['edwiserReport:view'] = 'View Reports and analytics dashboard';

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
$string['enrolmentstartdate'] = 'Enrolment Start Date';
$string['enrolmentenddate'] = 'Enrolment End Date';
$string['enrolmentrangeselector'] = 'Enrolment Date Range Selector';
$string['category'] = 'Category';
$string['customreportselectfailed'] = '<h4><i class="fa fa-check" aria-hidden="true"></i> Failed!</h4>Select any of the checkboxes to get reports.';
$string['customreportdatefailed'] = '<h4><i class="fa fa-check" aria-hidden="true"></i> Failed!</h4>Select valid date for enrolment.';
$string['customreportsuccess'] = '<h4><i class="fa fa-check" aria-hidden="true"></i> Success!</h4>Notifications sent successfully.';
$string['customreportfailed'] = '<h4><i class="fa fa-check" aria-hidden="true"></i> Failed!</h4>Select any of the checkboxes to get reports.';
$string['duration'] = 'Duration';
$string['na'] = 'NA';
$string['activityname'] = 'Activity Name';

// Setting.
$string['edwiserReport_settings'] = 'Reports & Analytics Dashboard Settings';
$string['selectblocks'] = 'Select blocks to show for Reporting Managers: ';
$string['rpmblocks'] = 'Reporting Manager Blocks';
$string['addblocks'] = 'Add Blocks';
$string['notselected'] = 'Not Selected';


// Custom Query Report.
$string['customqueryreport'] = 'Custom Query Report';
$string['completionrangeselector'] = 'Course Completion Date Range Selector';
$string['selectatleastonecvourse'] = 'Please, Select at least one course';
$string['reportfields'] = 'Report Fields';
$string['userfields'] = 'User Fields';
$string['coursefields'] = 'Course Fields';
$string['lpfields'] = 'Learning Program Fields';
$string['rpfields'] = 'Reporting Manager Fields';
$string['activityfields'] = 'Activity Fields';
$string['coursestartdate'] = 'Course Start Date';
$string['courseenddate'] = 'Course End Date';
$string['lpstartdate'] = 'Learning Program Start Date';
$string['lpenddate'] = 'Learning Program End Date';
$string['lpduration'] = 'Learning Program Duration';
$string['lpcompletion'] = 'Learning Program Completion Date';
$string['rpmname'] = 'Reporting Manager';
$string['totalactivities'] = 'Total Activities';
$string['completiontime'] = 'Course Completion Date';
$string['activitiescompleted'] = 'Activities Completed';
$string['incompletedactivities'] = 'Incompleted Activities';
$string['coursecategory'] = 'Course Category';
$string['lpenroldate'] = 'Learning Program Enrol Date';
$string['courseenroldate'] = 'Course Enrol Date';
$string['course_completion_status'] = 'Course Completion Status';
$string['learninghours'] = 'Learning Hours';

/* ERROR string */
$string['completiondatealert'] = "Select correct completion date range";
$string['enroldatealert'] = "Select correct enrolment date range";

/* Report name */
$string['reportname'] = 'custom_reports_{$a->date}.csv';
$string['totalgrade'] = 'Total Grade';
$string['attempt'] = 'Attempt';
$string['attemptstart'] = 'Attempt Start';
$string['attemptfinish'] = 'Attempt Finish';

$string['editblockview'] = 'Edit Block View';
$string['hide'] = 'Hide Block';
$string['unhide'] = 'Show Block';
$string['editcapability'] = 'Change Capability';

$string['desktopview'] = 'Desktop View';
$string['tabletview'] = 'Tablet View';
$string['mobileview'] = 'Mobile View';
$string['large'] = 'Large';
$string['medium'] = 'Medium';
$string['small'] = 'Small';
$string['position'] = 'Position';

$string['capabilties'] = 'Capabilities';
$string['activeusersblockview'] = 'Active Users Block View';
$string['activeusersblockedit'] = 'Active Users Block Edit';
$string['activeusersblockeditadvance'] = 'Active Users Block Advance Edit';
$string['activecoursesblockview'] = 'Popoler Courses Block View';
$string['activecoursesblockedit'] = 'Popoler Courses Block Edit';
$string['activecoursesblockeditadvance'] = 'Popoler Courses Block Advance Edit';
$string['courseprogressblockview'] = 'Course Progress Block View';
$string['courseprogressblockedit'] = 'Course Progress Block Edit';
$string['courseprogressblockeditadvance'] = 'Course Progress Block Advance Edit';
$string['certificatesblockview'] = 'Certificates Block View';
$string['certificatesblockedit'] = 'Certificates Block Edit';
$string['certificatesblockeditadvance'] = 'Certificates Block Advance Edit';
$string['liveusersblockview'] = 'Live Users Block View';
$string['liveusersblockedit'] = 'Live Users Block Edit';
$string['liveusersblockeditadvance'] = 'Live Users Block Advance Edit';
$string['siteaccessblockview'] = 'Site Access Block View';
$string['siteaccessblockedit'] = 'Site Access Block Edit';
$string['siteaccessblockeditadvance'] = 'Site Access Block Advance Edit';
$string['todaysactivityblockview'] = 'Todays Activity Block View';
$string['todaysactivityblockedit'] = 'Todays Activity Block Edit';
$string['todaysactivityblockeditadvance'] = 'Todays Activity Block Advance Edit';
$string['inactiveusersblockview'] = 'Inactive Users Block View';
$string['inactiveusersblockedit'] = 'Inactive Users Block Edit';
$string['inactiveusersblockeditadvance'] = 'Inactive Users Block Advance Edit';

$string['manageedwiserreportss'] = 'Manage Site Reports Dashboard';
$string['activeusersrolesetting'] = 'Active Users Block Roles';
$string['activeusersrolesettinghelp'] = 'Define view capability for active users block';
$string['courseprogressrolesetting'] = 'Course Progress Block Roles';
$string['courseprogressrolesettinghelp'] = 'Define view capability for Course Progress Block';
$string['activecoursesrolesetting'] = 'Popular Courses Block Roles';
$string['activecoursesrolesettinghelp'] = 'Define view capability for Popular Courses Block';
$string['certificatesrolesetting'] = 'Certificates Block Roles';
$string['certificatesrolesettinghelp'] = 'Define view capability for Certificates Block';
$string['liveusersrolesetting'] = 'Live Users Block Roles';
$string['liveusersrolesettinghelp'] = 'Define view capability for Live Users Block';
$string['siteaccessrolesetting'] = 'Site Access Information Block Roles';
$string['siteaccessrolesettinghelp'] = 'Define view capability for Site Access Information Block';
$string['todaysactivityrolesetting'] = 'Todays Activity Block Roles';
$string['todaysactivityrolesettinghelp'] = 'Define view capability for Todays Activity Block';
$string['inactiveusersrolesetting'] = 'Inactive Users Block Roles';
$string['inactiveusersrolesettinghelp'] = 'Define view capability for inactive users block';
$string['confignotfound'] = 'Configuration not found for this plugin';

$string['activeusersdesktopsize'] = 'Active Users Block in Desktop';
$string['activeusersdesktopsizehelp'] = 'Desktop view of active users block';
$string['courseprogressdesktopsize'] = 'Course Progress Block in Desktop';
$string['courseprogressdesktopsizehelp'] = 'Desktop view of course progress block';
$string['activecoursesdesktopsize'] = 'Popular Courses Block in Desktop';
$string['activecoursesdesktopsizehelp'] = 'Desktop view of popular courses block';
$string['certificatesdesktopsize'] = 'Certificates Block in Desktop';
$string['certificatesdesktopsizehelp'] = 'Desktop view of certificates block';
$string['liveusersdesktopsize'] = 'Live Users Block in Desktop';
$string['liveusersdesktopsizehelp'] = 'Desktop view of live users block';
$string['siteaccessdesktopsize'] = 'Site Access Information Block in Desktop';
$string['siteaccessdesktopsizehelp'] = 'Desktop view of active site access information block';
$string['todaysactivitydesktopsize'] = 'Todays Activity Block in Desktop';
$string['todaysactivitydesktopsizehelp'] = 'Desktop view of todays activity block';
$string['inactiveusersdesktopsize'] = 'Inactive Users Block  in Desktop';
$string['inactiveusersdesktopsizehelp'] = 'Desktop view of inactive users block';

$string['activeuserstabletsize'] = 'Active Users Block in Tablet';
$string['activeuserstabletsizehelp'] = 'Tablet view of active users block';
$string['courseprogresstabletsize'] = 'Course Progress Block in Tablet';
$string['courseprogresstabletsizehelp'] = 'Tablet view of course progress block';
$string['activecoursestabletsize'] = 'Popular Courses Block in Tablet';
$string['activecoursestabletsizehelp'] = 'Tablet view of popular courses block';
$string['certificatestabletsize'] = 'Certificates Block in Tablet';
$string['certificatestabletsizehelp'] = 'Tablet view of certificates block';
$string['liveuserstabletsize'] = 'Live Users Block in Tablet';
$string['liveuserstabletsizehelp'] = 'Tablet view of live users block';
$string['siteaccesstabletsize'] = 'Site Access Information Block in Tablet';
$string['siteaccesstabletsizehelp'] = 'Tablet view of active site access information block';
$string['todaysactivitytabletsize'] = 'Todays Activity Block in Tablet';
$string['todaysactivitytabletsizehelp'] = 'Tablet view of todays activity block';
$string['inactiveuserstabletsize'] = 'Inactive Users Block  in Tablet';
$string['inactiveuserstabletsizehelp'] = 'Tablet view of inactive users block';

$string['activeusersmobilesize'] = 'Active Users Block in Mobile';
$string['activeusersmobilesizehelp'] = 'Mobile view of active users block';
$string['courseprogressmobilesize'] = 'Course Progress Block in Mobile';
$string['courseprogressmobilesizehelp'] = 'Mobile view of course progress block';
$string['activecoursesmobilesize'] = 'Popular Courses Block in Mobile';
$string['activecoursesmobilesizehelp'] = 'Mobile view of popular courses block';
$string['certificatesmobilesize'] = 'Certificates Block in Mobile';
$string['certificatesmobilesizehelp'] = 'Mobile view of certificates block';
$string['liveusersmobilesize'] = 'Live Users Block in Mobile';
$string['liveusersmobilesizehelp'] = 'Mobile view of live users block';
$string['siteaccessmobilesize'] = 'Site Access Information Block in Mobile';
$string['siteaccessmobilesizehelp'] = 'Mobile view of active site access information block';
$string['todaysactivitymobilesize'] = 'Todays Activity Block in Mobile';
$string['todaysactivitymobilesizehelp'] = 'Mobile view of todays activity block';
$string['inactiveusersmobilesize'] = 'Inactive Users Block  in Mobile';
$string['inactiveusersmobilesizehelp'] = 'Mobile view of inactive users block';

$string['activeusersposition'] = 'Active Users Block in Position';
$string['activeuserspositionhelp'] = 'Position active users block';
$string['courseprogressposition'] = 'Course Progress Block in Position';
$string['courseprogresspositionhelp'] = 'Position course progress block';
$string['activecoursesposition'] = 'Popular Courses Block in Position';
$string['activecoursespositionhelp'] = 'Position popular courses block';
$string['certificatesposition'] = 'Certificates Block in Position';
$string['certificatespositionhelp'] = 'Position certificates block';
$string['liveusersposition'] = 'Live Users Block in Position';
$string['liveuserspositionhelp'] = 'Position live users block';
$string['siteaccessposition'] = 'Site Access Information Block in Position';
$string['siteaccesspositionhelp'] = 'Position active site access information block';
$string['todaysactivityposition'] = 'Todays Activity Block in Position';
$string['todaysactivitypositionhelp'] = 'Position todays activity block';
$string['inactiveusersposition'] = 'Inactive Users Block  in Position';
$string['inactiveuserspositionhelp'] = 'Position inactive users block';

/* Course progress manager strings */
$string['update_course_progress_data'] = 'Update Course Progress Data';

/* Course Completion Event */
$string['coursecompletionevent'] = 'Course Completion Event';
$string['courseprogessupdated'] = 'Course Progress Updated';

/* Error Strings */
$string['invalidparam'] = 'Invalid Parameter Found';
$string['moduleidnotdefined'] = 'Module id is not defined';

$string['clicktogetuserslist'] = 'Click in numbers in order to get the users list';

/* Email Schedule Strings */
$string['scheduleerrormsg'] = '<div class="alert alert-danger"><b>ERROR:</b> Error while scheduling email</div>';
$string['schedulesuccessmsg'] = '<div class="alert alert-success"><b>SUCCESS:</b> Email scheduled successfully</div>';
$string['deletesuccessmsg'] = '<div class="alert alert-success"><b>SUCCESS:</b> Email deleted successfully</div>';
$string['deleteerrormsg'] = '<div class="alert alert-danger"><b>ERROR:</b> Email deletion failed</div>';
$string['emptyerrormsg'] = '<div class="alert alert-danger"><b>ERROR:</b> Name and Recepient Fields can not be empty</div>';
$string['emailinvaliderrormsg'] = '<div class="alert alert-danger"><b>ERROR:</b> Invalid email adderesses (space not allowed)</div>';
$string['scheduledemaildisbled'] = '<div class="alert alert-success"><b>SUCCESS:</b> Scheduled Email Disabled</div>';
$string['scheduledemailenabled'] = '<div class="alert alert-success"><b>SUCCESS:</b> Scheduled Email Enabled</div>';

$string['nextrun'] = 'Next Run';
$string['frequency'] = 'Frequency';
$string['manage'] = 'Manage';
$string['scheduleemailfor'] = 'Schedule Emails for';
$string['edit'] = 'Edit';
$string['delete'] = 'Delete';

$string['report/edwiserreports_activeusersblock:editadvance'] = 'Edit Advance';

/* Custom Reports block related strings */
$string['customreportedit'] = 'Custom Reports Block Manage';
$string['reportspreview'] = 'Reports Preview';
$string['reportsfilter'] = 'Reports Filter';
$string['noreportspreview'] = 'No Preview Available';
$string['userfields'] = 'User Fields';
$string['coursefields'] = 'Course Fields';
$string['activityfields'] = 'Activity Fields';
$string['reportslist'] = 'Custom Reports List';
$string['noreportslist'] = 'No Custom Reports';
$string['allcohorts'] = 'All Cohorts';
$string['allcourses'] = 'All Courses';
$string['save'] = 'Save';
$string['reportname'] = 'Report Name';
$string['reportshortname'] = 'Short Name';
$string['savecustomreport'] = 'Save Custom Report';
$string['downloadenable'] = 'Enable Download';
$string['emptyfullname'] = 'Report Name field is required';
$string['emptyshortname'] = 'Report Short Name field is required';
$string['nospecialchar'] = 'Report Short Name field doesn\'t allow special character';
$string['reportssavesuccess'] = 'Custom Reports Successfully saved';
$string['reportssaveerror'] = 'Custom Reports Failed to save';
$string['shortnameexist'] = 'Shortname Already Exist';
$string['createdby'] = 'Author';
$string['sno'] = 'S. No.';
$string['datecreated'] = 'Date Created';
$string['datemodified'] = 'Date Modified';
$string['enabledesktop'] = 'Desktop Enabled';
$string['noresult'] = 'No Result Found';
$string['enabledesktop'] = 'Add to Reports Dashboard';
$string['disabledesktop'] = 'Remove from Reports Dashboard';
$string['editreports'] = 'Edit Reports';
$string['deletereports'] = 'Delete Reports';
$string['deletesuccess'] = 'Reports Delete Successfully';
$string['deletefailed'] = 'Reports Delete Failed';
$string['deletecustomreportstitle'] = 'Delete Custom Reports Title';
$string['deletecustomreportsquestion'] = 'Do you really want to delete this custom reports?';
$string['createcustomreports'] = 'Create/Manage Custom Reports Block';
$string['searchreports'] = 'Search Reports';
$string['title'] = 'Title';
$string['createreports'] = 'Create New Report';
$string['updatereports'] = 'Update Reports';
$string['courseformat'] = 'Course Format';
$string['completionenable'] = 'Course Completion Enable';
$string['guestaccess'] = 'Course Guest Access';
$string['selectcourses'] = 'Select Courses';
$string['selectcohorts'] = 'Select Cohorts';
$string['createnewcustomreports'] = 'Create new Report';
