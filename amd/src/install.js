/* eslint-disable no-console */
define(['jquery', 'core/ajax'], function($, ajax) {
    var init = function() {
        var getConfig = 'local_edwiserreports_get_plugin_config';
        var getPluginConfig = ajax.call([
            {
                methodname: getConfig,
                args: {
                    pluginname: 'local_edwiserreports',
                    configname: 'edwiserreportsinstallation'
                }
            }
        ]);

        getPluginConfig[0].done(function(response) {
            if (response.success) {
                var completeInstallation = 'local_edwiserreports_complete_edwiserreports_installation';
                var completePluginInstallation = ajax.call([
                    {
                        methodname: completeInstallation,
                        args: {}
                    }
                ]);

                completePluginInstallation[0].done(function(response) {
                    console.log(response);
                });
            }
        });

        $(document).ready(function() {
            $('#page-admin-setting-manageedwiserreportss #adminsettings [type="submit"]').on('click', function(event) {
                event.preventDefault();
                var setConfig = 'local_edwiserreports_set_plugin_config';
                var setPluginConfig = ajax.call([
                    {
                        methodname: setConfig,
                        args: {
                            pluginname: 'local_edwiserreports',
                            configname: 'edwiserreportsinstallation'
                        }
                    }
                ]);

                setPluginConfig[0].done(function() {
                    $('#adminsettings').submit();
                });
            });
        });

        var positionSelector = 'select[id ^=id_s_local_edwiserreports][id $=position]';

        var currentVal = [];
        $(positionSelector).each(function(idx, val) {
            currentVal.push($(val).val());
        });

        $(positionSelector).on('change', function() {
            var _this = this;
            var posChangedIdx = false;
            $(positionSelector).each(function(idx, val) {
                if (_this.name == val.name) {
                    posChangedIdx = idx;
                    return;
                }
            });

            var prevSelectVal = parseInt(currentVal[posChangedIdx]);
            var currSelectVal = parseInt($(this).val());

            $(positionSelector).each(function(idx, val) {
                var currVal = parseInt($(val).val());
                if (_this.name !== val.name) {
                    if (prevSelectVal > currSelectVal && prevSelectVal > currVal && currSelectVal <= currVal) {
                        $(val).val(parseInt(currVal) + 1);
                    } else if (prevSelectVal < currSelectVal && prevSelectVal < currVal && currSelectVal >= currVal) {
                        $(val).val(parseInt(currVal) - 1);
                    }
                }
            });

            currentVal = [];
            $(positionSelector).each(function(idx, val) {
                currentVal.push($(val).val());
            });
        });
    };

    return {
        init: init
    };
});
