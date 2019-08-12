var gulp = require('gulp'),
    concat = require('gulp-concat'),
    rename = require('gulp-rename'),
    uglify = require('gulp-uglify'),
    sourcemaps = require('gulp-sourcemaps'),
    remove = require('gulp-rm'),
    sequence = require('run-sequence'),
    exec = require('gulp-exec'),
    notify = require('gulp-notify');

var sources = [
    'src/js/var/defaultconfig.js',
    'src/js/var/variables.js',
    'src/js/vendor/dataTables.bootstrap4.js',
    'src/js/vendor/dataTables.buttons.min.js',
    'src/js/vendor/dataTables.js',
    'src/js/vendor/flatpickr.min.js',
    'src/js/vendor/jquery.dataTables.js',
    'src/js/vendor/select2.min.js',
    'src/js/vendor/jquery-asPieProgress.min.js',
    'src/js/main.js',
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
    'src/js/completion.js'
];

gulp.task('purge', function(done) {
    gulp.src('.')
    .pipe(exec('php ../../../admin/cli/purge_caches.php'))
    .pipe(gulp.dest('.'))
    .pipe(notify('Purged All'));
    done;
});

gulp.task('uglify', function () {
    return gulp.src(sources)
       .pipe(uglify())
       .pipe(gulp.dest('dist'));
});

gulp.task('copy', function() {
    return gulp.src('./dist/*.js')
        .pipe(gulp.dest('../amd/build/'));
});

gulp.task('dist-js',
    gulp.series('uglify', 'copy', 'purge')
);

// gulp.task('watchjs', function(done) {
//   gulp.watch('src/js/**/*.js', gulp.series('dist-js'));
//   done();
// });

gulp.task('default', gulp.series('dist-js'));
