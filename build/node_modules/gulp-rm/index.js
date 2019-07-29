var fs = require( 'fs' )
var path = require( 'path' )
var stream = require( 'stream' )
var log = console.log.bind( console, '[gulp-rm]' )

/**
 * Walk an array of directories and
 * delete one after another
 * @param {Array<Object>} ls
 * @param {Function} done
 */
function rmdirWalk( ls, done ) {
  if( ls.length === 0 ) return done()
  fs.rmdir( ls.shift().path, function( error ) {
    if( error ) log( error.message )
    rmdirWalk( ls, done )
  })
}

class DeleteStream extends stream.Transform {

  /**
   * DeleteStream constructor
   * @param {Object} options
   * @param {Boolean} [options.async=true]
   */
  constructor( options ) {

    options = options || {}
    options.objectMode = true

    super( options )

    /** @type {Array<Object>} List of directories to be deleted */
    this._directories = []
    /** @type {Boolean} Whether to use synchronous fs methods */
    this._async = options.async !== false

  }

  _transform( file, encoding, next ) {

    if( path.relative( file.cwd, file.path ) === '' ) {
      log( 'Cannot delete current working directory' )
    }

    if( file.stat == null ) {
      try { file.stat = fs.statSync( file.path ) }
      catch( error ) {
        log( error.message )
        return next()
      }
    }

    // Defer removal of directories until
    // all files are deleted to ensure they're empty
    if( file.stat.isDirectory() ) {
      this._directories.unshift( file )
      next()
    } else if( !this._async ) {
      try { fs.unlinkSync( file.path ) }
      catch( error ) { log( error.message ) }
      finally { next() }
    } else {
      fs.unlink( file.path, function( error ) {
        if( error ) log( error.message )
        next()
      })
    }

  }

  _flush( done ) {

    // Sort by depth in the directory tree,
    // so that the deepest are the first
    this._directories.sort( function( a, b ) {
      var x = a.path.replace( /^\/|\/$/, '' ).split( '/' ).length
      var y = b.path.replace( /^\/|\/$/, '' ).split( '/' ).length
      if( x > y ) return -1
      if( x < y ) return +1
      return 0
    })

    if( !this._async ) {
      var dir = null
      while( dir = this._directories.shift() ) {
        try { fs.rmdirSync( dir.path ) }
        catch( error ) { log( error.message ) }
        if( !this._directories.length ) done()
      }
    } else {
      rmdirWalk( this._directories, done )
    }

  }

}

/**
 * DeleteStream factory
 * @param  {Object} options
 * @return {Stream}
 */
DeleteStream.create = function( options ) {
  return new DeleteStream( options )
}

// Exports
module.exports = DeleteStream.create
