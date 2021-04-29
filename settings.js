define('local_edwiserreports/settings', ['jquery'], function($) {
    return {
        init: function() {
            // Preventing reload notification
            $(document).ready(function() {
                window.onbeforeunload = null;
            });
        }
    };
});
