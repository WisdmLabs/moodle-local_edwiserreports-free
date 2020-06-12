var gulp = require('gulp'),
    concat = require('gulp-concat'),
    rename = require('gulp-rename'),
    uglify = require('gulp-uglify-es').default,
    sourcemaps = require('gulp-sourcemaps'),
    remove = require('gulp-rm'),
    sequence = require('run-sequence'),
    exec = require('gulp-exec'),
    notify = require('gulp-notify'),
    shell  = require('gulp-shell');

var sources = ['src/js/main.js',
    'src/js/block_accessinfo.js',
    'src/js/block_activecourses.js',
    'src/js/block_activeusers.js',
    'src/js/block_certificatestats.js',
    'src/js/block_courseprogress.js',
    'src/js/block_f2fsessions.js',
    'src/js/block_inactiveusers.js',
    'src/js/block_lpstats.js',
    'src/js/block_realtimeusers.js',
    'src/js/block_todaysactivity.js',
    'src/js/activeusers.js',
    'src/js/courseprogress.js',
    'src/js/certificates.js',
    'src/js/f2fsessions.js',
    'src/js/lpstats.js',
    'src/js/courseengage.js',
    'src/js/completion.js',
    'src/js/courseanalytics.js',
    'src/js/common.js',
    'src/js/lpdetailedreport.js',
    'src/js/block_queryreport.js'
];

gulp.task('purge', shell.task('php '+__dirname+'/../../../admin/cli/purge_caches.php'));

gulp.task('uglify', function () {
    return gulp.src(sources)
       .pipe(uglify())
       .pipe(gulp.dest('dist'));
});

gulp.task('copy', function() {
    return gulp.src('./dist/*.js')
        .pipe(gulp.dest('../amd/build/'));
});

gulp.task('dist-js', gulp.series('uglify', 'copy'));

gulp.task('watch', function(done) {
  gulp.watch('src/js/**/*.js', gulp.series('dist-js'));
});

gulp.task('default', gulp.series('watch', 'dist-js'));
