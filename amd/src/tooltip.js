define(['jquery', 'core/tooltip'], function($) {
    return {
        init: function() {
            // Copy data-title to title if title is not present
            $('[data-title]').each(function () {
                if (!$(this).attr('title')) {
                    $(this).attr('title', $(this).data('title'));
                }
            });

            // Init Bootstrap 4 tooltips
            if (typeof $.fn.tooltip !== 'undefined') {
                $('[data-toggle="tooltip"]').tooltip();
            }
            // BS5 handled automatically by core/tooltip
        }
    };
});
