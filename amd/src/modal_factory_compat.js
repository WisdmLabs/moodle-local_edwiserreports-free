/**
 * Compatibility shim for core/modal_factory (removed in Moodle 5.x).
 * Wraps core/modal to provide the same API.
 *
 * @package     local_edwiserreports
 * @copyright   2024 Edwiser Reports
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/modal', 'jquery'], function(Modal, $) {
    return {
        create: function(config) {
            var deferred = $.Deferred();
            Modal.create(config).then(function(modal) {
                deferred.resolve(modal);
                return modal;
            }).catch(function(e) {
                deferred.reject(e);
            });
            return deferred.promise();
        },
        types: Modal.types || {}
    };
});
