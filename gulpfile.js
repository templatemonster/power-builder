'use strict';

const gulp = require( 'gulp' ),
	rename = require( 'gulp-rename' ),
	exec = require( 'child_process' ).exec,
	fs = require( 'fs' ),
	through = require( 'through2' ),
	path = require( 'path' ),
	gutil = require( 'gulp-util' ),
	sass = require( 'gulp-sass' ),
	rsvp = require( 'rsvp' ),
	Promise = rsvp.Promise;

// Converter options
let CONVERTER_OPTS = {
	'--from': 'css',
	'--to': 'scss',
	'--indent': 2
};

/**
 * Remove all files in the directory
 * @param  {string} dirpath Directory path
 * @return {Promise}
 */
function cleardir( dirpath ) {
	let filepath;

	return new Promise( (resolve, reject) => {
		fs.readdir( dirpath, ( error, files ) => {
      if ( error ) {
				return reject( err );
      }

			resolve(
				rsvp.all(
					files.map( ( file ) => {
						filepath = path.join( dirpath, file );

						console.log( `Deleting: ${ filepath }` );
						return rm( filepath );
          } )
				)
			);
		} );
	} );
}

/**
 * Remove file and / or directory with it's contents
 * @param  {string} filepath File or directory path
 * @return {Promise}
 */
function rm( filepath ) {
    return new Promise( ( resolve, reject ) => {
        fs.stat( filepath, ( error, stat ) => {
            if ( error ) {
                return reject( error );
            }

            if ( stat.isFile() ) {
                fs.unlink( filepath, ( error ) => {
                    if ( error ) {
                        return reject( error );
                    }
                    resolve( filepath );
                });
            } else if ( stat.isDirectory() ) {
                cleardir( filepath ).then( () => {
                    fs.rmdir( filepath, ( error ) => {
											if ( error ) {
	                        return reject( error );
	                    }
	                    resolve( filepath );
                    } );
                } );
            } else {
                reject( new Error( `${filepath} is not a file or directory` ) );
            }
        });
    });
}

/**
 * Get the gulp task for converting css into scss
 * @param  {string} src  Sources
 * @param  {string} dest Destination
 * @return {function}    Gulp task
 */
function getConverterTask( src, dest ) {

	function _sass_convert() {
		return through.obj( function( file, encoding, callback ) {
			let self = this,
				opts = [];

			Object.keys( CONVERTER_OPTS ).forEach( key => {
				opts.push( `${key} ${CONVERTER_OPTS[key]}` );
			} );

			opts = opts.join( ' ' );

			if ( file.isBuffer() ) {
				let filepath = path.join( dest, path.basename( file.path ).replace( /\.css$/i, '.scss' ) );
				gutil.log( filepath );

				exec( `sass-convert ${file.path} ${opts}`, ( error, stdout, stderr ) => {
					if ( null !== error ) {
						gutil.log( error );
						return callback( error, file );
					}

					let tmp_file = new gutil.File( {
						base: dest,
						cwd: dest,
						path: filepath
					} );

					tmp_file.contents = new Buffer( stdout );

					self.push(tmp_file);

					return callback( null, tmp_file );
				} );
			} else {
				return callback( null, file );
			}
		} );
	}

	return ( done ) => {
		gulp.src( src )
			.pipe( _sass_convert() )
			.pipe( gulp.dest( dest ) );
	};
}

function getFormatterTask( src, dest ) {

	function _sass_format() {
		return through.obj( function( file, encoding, callback ) {
			let self = this;

			if ( file.isBuffer() ) {
				let filepath = path.join( dest, path.basename( file.path ) );

				exec( `sass-convert ${file.path} ${filepath}`, ( error, stdout, stderr ) => {
					if ( null !== error ) {
						gutil.log( error );
						return callback( error, file );
					}

					gutil.log( filepath );
					self.push(file);
					return callback( null, file );
				} );
			} else {
				return callback( null, file );
			}
		} );
	}

	return ( done ) => {
		gulp.src( src )
			.pipe( _sass_format() )
			.pipe( gulp.dest( dest ) );
	};
}

// CSS to SCSS converter
//gulp.task( 'convert', getConverterTask(
//	`./framework/admin/assets/old_scss/**/*.css`,
//	`./framework/admin/assets/scss/`
//) );

// SCSS formatter
//gulp.task( 'format', getFormatterTask(
//	`./framework/admin/assets/scss/**/*.scss`,
//	`./framework/admin/assets/scss/`
//) );

gulp.task( 'scss.admin-assets', () => {
	gulp.src( './framework/admin/assets/scss/style.scss' )
		.pipe( sass().on( 'error', sass.logError ) )
		.pipe( gulp.dest( './framework/admin/assets/css/' ) );
} );

gulp.task( 'scss.assets', () => {
	gulp.src( './framework/assets/scss/style.scss' )
		.pipe( sass().on( 'error', sass.logError ) )
		.pipe( gulp.dest( './framework/assets/css/' ) );
} );

gulp.task( 'scss', [ 'scss.admin-assets', 'scss.assets' ] );

gulp.task( 'default', [ 'scss' ] );

gulp.task( 'copy', ( done ) => {
	if ( ! process.env.WPDEV_PATH ) {
		throw new Error( 'Please define WPDEV_PATH environment variable first!' );
	}

	const WPDEV_PATH = process.env.WPDEV_PATH,
		WP_PLUGIN_NAME = 'tm-content-builder',
		WP_PLUGIN_PATH = path.join( WPDEV_PATH, 'wp-content', 'plugins', WP_PLUGIN_NAME );

	function _copy() {
		gutil.log( 'Copying files...' );

		// Copy files
		gulp.src( '**/*.*' )
			.pipe( gulp.dest( WP_PLUGIN_PATH ) );
		done();
	}

	// Check if directory already exists
	gutil.log( 'Checking WPDEV_PATH...' );
	fs.stat( WP_PLUGIN_PATH, ( error, stats ) => {
		if ( error ) { // file not found, do nothing...
			gutil.log( 'WPDEV_PATH is empty' );
			return _copy();
		}

		gutil.log( 'WPDEV_PATH exists. Removing...' );

		// Remove old files
		rm( WP_PLUGIN_PATH ).then( ( path ) => {
			_copy();
		}, ( error ) => {
			done( error );
		} );
	} );
} );

gulp.task( 'watch', () => {
	let watcher = gulp.watch( [ '**/*.*', '!.git/**' ], [ 'copy' ] );

	watcher.on( 'change', ( event ) => {
		console.log( `File ${event.path} was ${event.type}, running tasks...` );
	} );
} );
