var gulp = require('gulp'),
    concat = require('gulp-concat'),
    rename = require('gulp-rename'),
    uglify = require('gulp-uglify-es').default,
    sourcemaps = require('gulp-sourcemaps'),
    remove = require('gulp-rm'),
    sequence = require('run-sequence'),
    exec = require('gulp-exec'),
    notify = require('gulp-notify'),
    shell  = require('gulp-shell'),
    clean = require('gulp-clean'),
    extReplace = require('gulp-ext-replace');

var sources = [
    './src/js/var/*.js',
    './src/js/vendor/*.js',
    './src/js/*.js'
];

gulp.task('clean', function(done) {
    gulp.src('./../amd/src/**/*', {read: false})
    .pipe(clean({force: true}));
    gulp.src('./../amd/build/**/*', {read: false})
    .pipe(clean({force: true}));
    done();
});

gulp.task('purge', shell.task('php '+__dirname+'/../../../admin/cli/purge_caches.php'));

gulp.task('uglify', function (done) {
    gulp.src(sources)
    .pipe(extReplace('.js', '.min.js'))
    .pipe(rename({
        dirname: ''
    }))
    .pipe(gulp.dest('./../amd/src/'));
    gulp.src(sources)
    .pipe(extReplace('.js', '.min.js'))
    .pipe(rename({
        dirname: '',
        suffix: '.min',
        extname: '.js'
    }))
    .pipe(uglify())
    .pipe(gulp.dest('./../amd/build/'));
    done();
});

gulp.task('watch', function(done) {
  gulp.watch('src/js/**/*.js', gulp.series('clean', 'uglify', 'purge'));
  gulp.watch(['../lang/**/*', '../styles/*', '../styles.css'], gulp.series('purge'));
  done();
});

gulp.task('default', gulp.series('clean', 'uglify', 'watch'));
