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
    }

    return {
        init : init
    }
})
