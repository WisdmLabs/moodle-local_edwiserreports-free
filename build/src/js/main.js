define([
	'report_elucidsitereport/block_accessinfo',
	'report_elucidsitereport/block_activecourses',
	'report_elucidsitereport/block_activeusers',
	'report_elucidsitereport/block_courseprogress',
	'report_elucidsitereport/block_inactiveusers',
	'report_elucidsitereport/block_lpstats',
	'report_elucidsitereport/block_realtimeusers',
	'report_elucidsitereport/block_todaysactivity',
	'report_elucidsitereport/common'
], function (
	accessInfo,
	activeCourses,
	activeUsers,
	courseProgress,
	inActiveUsers,
	lpStatsBlock,
	realTimeUsers,
	todaysActivity
) {
	// Must return the init function
	return {
	    init: function() {
	    	courseProgress.init();
	    	accessInfo.init();
			activeCourses.init();
			activeUsers.init();
			courseProgress.init();
			inActiveUsers.init();
			lpStatsBlock.init();
			realTimeUsers.init();
			todaysActivity.init();
	    }
	};
});