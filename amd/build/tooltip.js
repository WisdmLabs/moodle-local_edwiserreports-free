define(['jquery', 'core/tooltip'], function($, Tooltip) {
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
            
            // Init Bootstrap 5 tooltips using core/tooltip
            if (typeof Tooltip !== 'undefined') {
                $('[data-bs-toggle="tooltip"]').each(function() {
                    new Tooltip(this);
                });
            }
        }
    };
});
