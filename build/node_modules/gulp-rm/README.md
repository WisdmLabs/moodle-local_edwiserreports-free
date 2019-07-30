# gulp-rm
[![npm](http://img.shields.io/npm/v/gulp-rm.svg?style=flat-square)](https://npmjs.com/gulp-rm)
[![npm](http://img.shields.io/npm/l/gulp-rm.svg?style=flat-square)](https://npmjs.com/gulp-rm)
[![npm downloads](http://img.shields.io/npm/dm/gulp-rm.svg?style=flat-square)](https://npmjs.com/gulp-rm)
[![build status](http://img.shields.io/travis/jhermsmeier/gulp-rm.svg?style=flat-square)](https://travis-ci.org/jhermsmeier/gulp-rm)

## Install with [npm](https://npmjs.org)
```sh
$ npm install --save-dev gulp-rm
```

## Usage

Passing `{ read: false }` to `gulp.src()` prevents gulp from
reading in the contents of files and thus speeds up the whole process.

**NOTE:** Deleting directories with dotfiles (i.e. `.DS_Store`) in them will fail, unless
a glob pattern matching them (i.e. `app/tmp/**/.*`) is also supplied to `gulp.src()`,
as they're considered hidden files and ignored by default by `gulp.src()`.

```javascript
var gulp = require( 'gulp' )
var rm = require( 'gulp-rm' )

gulp.task( 'clean:tmp', function() {
  return gulp.src( 'app/tmp/**/*', { read: false })
    .pipe( rm() )
})
```

To force sync `fs` operations, pass `async: false` to `rm()`:

```javascript
gulp.task( 'clean:tmp', function() {
  return gulp.src( 'app/tmp/**/*', { read: false })
    .pipe( rm({ async: false }) )
})
```
