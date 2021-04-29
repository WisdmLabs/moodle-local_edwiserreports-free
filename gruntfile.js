"use strict";
// eslint-disable-next-line no-undef
module.exports = function(grunt) {

    // We need to include the core Moodle grunt file too, otherwise we can't run tasks like "amd".
    require("grunt-load-gruntfile")(grunt);

    grunt.config.merge({
        eslint: {
            // Even though warnings dont stop the build we don't display warnings by default because
            // at this moment we've got too many core warnings.
            options: {quiet: !grunt.option('show-lint-warnings')},
            amd: {
                src: 'amd/src/*.js'
            },
            // Check YUI module source files.
        },
        stylelint: {
            scss: {
                src: ['./styles/**/*', './scss/**/*'],
                options: {
                    ignoreDisables: false,
                    configOverrides: {
                        rules: {
                            // These rules have to be disabled in .stylelintrc for scss compat.
                            'declaration-block-no-duplicate-properties': null,
                            'declaration-no-important': null,
                            'max-line-length': null,
                            'csstree/validator': null
                        }
                    }
                }
            }
        }
    });
    // The default task (running "grunt" in console).
    grunt.loadNpmTasks('grunt-eslint');
    grunt.loadNpmTasks('grunt-stylelint');
};
