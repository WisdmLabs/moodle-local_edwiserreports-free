/**
 * Compatibility shim for core/modal_factory (removed in Moodle 5.x).
 * Wraps core/modal to provide the same API.
 *
 * @package     local_edwiserreports
 * @copyright   2024 Edwiser Reports
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/modal', 'core/modal_save_cancel', 'jquery'], function(Modal, ModalSaveCancel, $) {
    // SAVE_CANCEL type constant
    var SAVE_CANCEL = 'save_cancel';

    return {
        create: function(config) {
            var deferred = $.Deferred();
            var isSaveCancel = (config.type === SAVE_CANCEL || config.type === 'SAVE_CANCEL');

            // core/modal is the generic modal and does not understand a "buttons"
            // config option, so a plain Modal.create() renders an empty footer -
            // the Save/Cancel buttons never appear. core/modal_save_cancel is the
            // real Moodle core class (still shipped in 5.x) whose own template
            // already contains a footer with working Save/Cancel buttons and wires
            // up the save/cancel modal_events for us.
            var ModalClass = isSaveCancel ? ModalSaveCancel : Modal;

            ModalClass.create(config).then(function(modal) {
                if (isSaveCancel) {
                    modal.getRoot().addClass('modal-save-cancel');
                }
                deferred.resolve(modal);
                return modal;
            }).catch(function(e) {
                deferred.reject(e);
            });
            return deferred.promise();
        },
        types: {
            SAVE_CANCEL: SAVE_CANCEL
        }
    };
});
