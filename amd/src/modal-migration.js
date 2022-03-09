define([
    'core/ajax',
    'core/modal',
    'core/modal_events',
    'core/modal_registry',
    'core/custom_interaction_events',
], function(
    Ajax,
    Modal,
    ModalEvents,
    ModalRegistry,
    CustomEvents
) {
    var registered = false;

    /**
     * Selectors
     */
    var SELECTORS = {
        CONTINUE_BUTTON: '[data-action="continue"]',
        LATER_BUTTON: '[data-action="later"]',
        NEVER_BUTTON: '[data-action="never"]'
    };

    /**
     * Promises
     */
    var PROMISES = {
        /**
         * Show migration modal after 7 days.
         */
        LATER: function() {
            Ajax.call([{
                methodname: 'local_edwiserreports_set_plugin_config',
                args: {
                    'pluginname': 'local_edwiserreports',
                    'configname': 'fetchlater',
                    'value': Math.ceil((new Date()).getTime() / 1000) + (86400 * 7)
                }
            }]);
        },
        /**
         * Never show migration modal.
         */
        NEVER: function() {
            Ajax.call([{
                methodname: 'local_edwiserreports_set_plugin_config',
                args: {
                    'pluginname': 'local_edwiserreports',
                    'configname': 'fetch',
                    'value': 'never'
                }
            }]);
        }
    };

    /**
     * Constructor for the Modal.
     *
     * @param {object} root The root jQuery element for the modal
     */
    var MIGRATION = function(root) {
        Modal.call(this, root);
        this.show();
    };

    MIGRATION.TYPE = 'local_edwiserreports-migration';
    MIGRATION.prototype = Object.create(Modal.prototype);
    MIGRATION.prototype.constructor = MIGRATION;

    /**
     * Set up all of the event handling for the modal.
     *
     * @method registerEventListeners
     */
    MIGRATION.prototype.registerEventListeners = function() {
        // Apply parent event listeners.
        Modal.prototype.registerEventListeners.call(this);

        // Continue button clicked.
        this.getModal().on(CustomEvents.events.activate, SELECTORS.CONTINUE_BUTTON, function(e, data) {
            this.hide();
            window.location.href = M.cfg.wwwroot + '/local/edwiserreports/old_logs.php';
        }.bind(this));

        // Later button clicked.
        this.getModal().on(CustomEvents.events.activate, SELECTORS.LATER_BUTTON, function(e, data) {
            this.hide();
            PROMISES.LATER();
        }.bind(this));

        // Never button clicked.
        this.getModal().on(CustomEvents.events.activate, SELECTORS.NEVER_BUTTON, function(e, data) {
            this.hide();
            PROMISES.NEVER();
        }.bind(this));
    };

    // Automatically register with the modal registry the first time this module is imported so that you can create modals
    // of this type using the modal factory.
    if (!registered) {
        ModalRegistry.register(MIGRATION.TYPE, MIGRATION, 'local_edwiserreports/modal-migration');
        registered = true;
    }

    return MIGRATION;
});