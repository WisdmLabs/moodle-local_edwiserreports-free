define(['jquery', 'core/ajax'], function($, ajax) {
    var init = function () {
        var getConfig = 'local_sitereport_get_plugin_config';
        var getPluginConfig = ajax.call([
            {
                methodname: getConfig,
                args: {
                    pluginname: 'local_sitereport',
                    configname: 'sitereportinstallation'
                }
            }
        ]);

        getPluginConfig[0].done(function (response) {
            if (response.success) {
                var completeInstallation = 'local_sitereport_complete_sitereport_installation';
                var completePluginInstallation = ajax.call([
                    {
                        methodname: completeInstallation,
                        args: {}
                    }
                ]);

                completePluginInstallation[0].done(function (response) {
                    console.log(response);
                });
            }
        });

        $(document).ready(function() {
            $('#page-admin-setting-managesitereports #adminsettings [type="submit"]').on ('click', function(event) {
                event.preventDefault();
                var setConfig = 'local_sitereport_set_plugin_config';
                var setPluginConfig = ajax.call([
                    {
                        methodname: setConfig,
                        args: {
                            pluginname: 'local_sitereport',
                            configname: 'sitereportinstallation'
                        }
                    }
                ]); 
                
                setPluginConfig[0].done(function() {
                    $('#adminsettings').submit();
                });
            })
        });

        var positionSelector = 'select[id ^=id_s_local_sitereport][id $=position]';

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

            var prevSelectVal = currentVal[posChangedIdx];
            var currSelectVal = $(this).val();

            $(positionSelector).each(function(idx, val) {
                var currVal = $(val).val();
                if (_this.name !== val.name) {
                    console.log(val.name);
                    console.log(prevSelectVal);
                    console.log(currSelectVal);
                    console.log(currVal);
                    if (prevSelectVal > currSelectVal && currSelectVal <= currVal) {
                        console.log($(val).find('option:selected').next());
                        $(val).find('option:selected').next().attr('selected', 'selected');
                    } else if (prevSelectVal < currSelectVal && currSelectVal >= currVal) {
                        console.log($(val).find('option:selected').prev());
                        $(val).find('option:selected').prev().attr('selected', 'selected');
                    }
                }
            });
        });
    }

    return {
        init : init
    }
})
