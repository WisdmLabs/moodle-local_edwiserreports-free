define('local_sitereport/settings', ['jquery'], function($){
    return {
        init: function() {
            // Preventing reload notification
            $(document).ready(function() {
                window.onbeforeunload = null;
            });
        }
    };
});
