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
        })
    }

    return {
        init : init
    }
})
