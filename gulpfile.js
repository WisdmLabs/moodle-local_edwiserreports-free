/* eslint-disable no-undef */
var gulp = require('gulp'),
    sourcemaps = require('gulp-sourcemaps'),
    shell = require('gulp-shell'),
    clean = require('gulp-clean'),
    babel = require('gulp-babel'),
    minify = require('gulp-minify'),
    sass = require('gulp-sass'),
    concat = require('gulp-concat'),
    extReplace = require('gulp-ext-replace'),
    stripCssComments = require('gulp-strip-css-comments'),
    header = require('gulp-header');

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

gulp.task('fix-styles', function() {
    return gulp
    .src('scss/**/*.scss')
    .pipe(gulpStylelint({
        fix: true
    }))
    .pipe(gulp.dest('scss'));
});

gulp.task('sass', function() {
    gulp.src('./styles/**/*.min.css', {read: false})
    .pipe(clean({force: true}));

    return gulp.src(['scss/**/*.scss', 'scss/**/*.css'])
    .pipe(stripCssComments())
    .pipe(sass({
        indentWidth: 4,
        outputStyle: 'expanded'
    }))
    .pipe(concat('edwiserreports.min.css'))
    .pipe(header('/* stylelint-disable */\n'))
    .pipe(gulp.dest('./styles/'));
});

gulp.task('watch', function(done) {
  gulp.watch('./amd/src/*.js', gulp.series('uglify', 'purge'));
  gulp.watch(['./lang/**/*', './styles/**/*', './templates/**/*'], gulp.series('purge'));
  gulp.watch(['scss/**/*.scss'], gulp.series('sass', 'purge'));
  done();
});

gulp.task('default', gulp.series('clean', 'sass', 'uglify', 'watch', 'purge'));
