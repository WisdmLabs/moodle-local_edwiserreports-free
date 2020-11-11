/* eslint-disable no-undef */
var gulp = require('gulp'),
    sourcemaps = require('gulp-sourcemaps'),
    shell = require('gulp-shell'),
    clean = require('gulp-clean'),
    babel = require('gulp-babel'),
    minify = require('gulp-minify'),
    extReplace = require('gulp-ext-replace');

var PRODUCTION = process.argv.includes('-production');

var sources = [
    './amd/src/*.js'
];

var minifyOptions = {
    ext: {
        min: '.min.js'
    },
    mangle: true,
    compress: true,
    noSource: true
};
if (PRODUCTION == false) {
    minifyOptions.preserveComments = 'all';
}

gulp.task('clean', function() {
    return gulp.src('./amd/build/**/*', {read: false})
    .pipe(clean({force: true}));
});

gulp.task('purge', shell.task('php ' + __dirname + '/../../admin/cli/purge_caches.php'));

gulp.task('uglify', function() {
    var task = gulp.src(sources)
    .pipe(extReplace('.js', '.min.js'));
    if (PRODUCTION) {
        task = task.pipe(sourcemaps.init())
        .pipe(babel({
            presets: [["@babel/preset-env"]]
        }))
        .pipe(minify(minifyOptions))
        .pipe(sourcemaps.write('.'));
    }
    return task.pipe(gulp.dest('./amd/build/'));
});

gulp.task('watch', function(done) {
  gulp.watch('./amd/src/*.js', gulp.series('uglify', 'purge'));
  gulp.watch(['../lang/**/*', '../styles/*', '../styles.css'], gulp.series('purge'));
  done();
});

gulp.task('default', gulp.series('clean', 'uglify', 'watch', 'purge'));
