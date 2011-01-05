<?php
/**
 * This core ResourceLoader provides the resource loader functionality
 * @file
 */

//Setup the script local script cache directory

// Check if being used in mediaWiki ( ResourceLoader.php is NOT an entry point )
if( is_file ( dirname( __FILE__ ) .'../mwResourceLoader.php' )
 	&& !defined( 'SCRIPTLOADER_MEDIAWIKI') ) {
	die( 'ResourceLoader.php is not an entry point when used with the JS2 extension' );
}

// Check if we are an entry point or being used as part of MEDIAWIKI:
if ( !defined( 'MEDIAWIKI' ) && !defined( 'SCRIPTLOADER_MEDIAWIKI') ) {
	// Include settings ( will include LocalSettings.php in the root mwEmbed folder
	require_once(  dirname( __FILE__ )  . '/includes/DefaultSettings.php' );
echo 'URL' . $wgKalturaServiceUrl;exit();
	// Load stand alone Resource Loader config
	// ( if running as a remote, mediaWiki variables / functions are already included as part of mediaWiki )
	require_once( realpath( dirname( __FILE__ ) ) . '/includes/noMediaWikiConfig.php' );

	$myResourceLoader = new ResourceLoader();
	if( $myResourceLoader->outputFromCache() ) {
		exit();
	}
	$myResourceLoader->doResourceLoader();
}


class ResourceLoader {

	// The list of named javascript & css files
	private $namedFileList = array();

	// The language code for the script-loader request
	var $langCode = '';

	// The output string
	var $output = '';

	// Special prepend js var to be added to the top of minification output.
	// useful for special comment tags in minification output
	var $notMinifiedTopOutput = '';

	// The request Key for the js
	var $requestKey = '';

	// Error msg
	var $errorMsg = '';

	// Output format is either 'js' or 'css' or 'messages' for exclusively msg text;
	var $outputFormat = 'js';

	// Debug flag
	var $debug = false;

	// The raw requested class
	private static $rawClassList = '';

	// The includeAllModuleMessages string regular expression
	private static $includeAllMsgsRegEx = "/mw\.includeAllModuleMessages\s*\(\s*\)\;?/";

	//Temporary store for message js
	private static $addMessageJs = '';

	/**
	 * Output the javascript from cache
	 *
	 * @return {Boolean} true on success, false on failure
	 */
	function outputFromCache(){
		// Process the request
		$this->requestKey = $this->preProcRequestVars();
		// Setup file cache object
		$this->sFileCache = new simpleFileCache( $this->requestKey );
		if ( $this->sFileCache->isFileCached() ) {
			// Output headers
			$this->outputHeaders();
			// Output cached file
			$this->sFileCache->outputFile();
			return true;
		}
		return false;
	}

	/**
	 * Core resourceLoader driver:
	 *
	 * 	get request key
	 *  builds javascript string
	 *  optionally gzips the output
	 *  checks for errors
	 *  sends the headers
	 *  outputs js
	 */
	function doResourceLoader() {
		global 	$wgResourceLoaderNamedPaths, $IP, $wgUseFileCache;

		// Load the resource paths:
		require_once( realpath( dirname( __FILE__ ) ) . "/includes/NamedResourceLoader.php");
		try {
			NamedResourceLoader::loadResourcePaths();
		} catch( Exception $e ) {
			$this->errorMsg .= "loadResourcePaths:" . $e->getMessage() ;
		}

		// Reset the requestKey:
		$this->requestKey = '';

		// Do the post proc request with configuration vars:
		$this->postProcRequestVars();

		// Update the filename (if gzip is on)
		$this->sFileCache->getCacheFileName();

		// Setup script loader header ( to easy identify file data )
		if( $this->outputFormat == 'js' ) {
			$this->output .= 'var mwResourceLoaderDate = "' .
			xml::escapeJsString( date( 'c' ) ) . '";' . "\n";
			$this->output .= 'var mwResourceLoaderRequestKey = "' .
			xml::escapeJsString( $this->requestKey ) . '";' . "\n";
		}

		// Build the output
		// Swap in the appropriate language per js_file
		foreach ( $this->namedFileList as $resourceName => $filePath ) {
			// Check if we are exclusively getting messages
			if( $this->outputFormat == 'messages' ) {
				// Get only the message code
				$this->output .= $this->getResourceMessageJS( $resourceName );
			} else {
				// Process the full class
				$this->output .= $this->getLocalizedScriptText( $resourceName );
			}

			// MwEmbed is a core component so it includes loaders and other styles
			if( $resourceName == 'mwEmbed' && $this->outputFormat != 'messages' ){
				// Output core components ( parts of core mwEmbed that are in different files )
				$coreComponentsList = NamedResourceLoader::getComponentsList();
				foreach( $coreComponentsList as $componentClassName ) {
					// Output the core component via the script loader:
					$this->output .= $this->getLocalizedScriptText( $componentClassName );
				}

				// Output the loaders js
				$loaderJS = NamedResourceLoader::getCombinedLoaderJs();
				// Transform the loader text to remove debug statements and
				// update language msgs if any are present
				$this->output .= $this->transformScriptText( $loaderJS , 'mwEmbed');

				// Output the current language resource js
				$this->output .= NamedResourceLoader::getLanguageJs( $this->langCode );

				// Output the localSettings.js
				$localSettingsJsPath = realpath( dirname( __FILE__ ) ) . '/localSettings.js';
				if( is_file( $localSettingsJsPath ) ){
					wfSuppressWarnings();
					$this->output .= file_get_contents( $localSettingsJsPath );
					wfRestoreWarnings();
				}

				// Add the required core mwEmbed style sheets
				// removed for now because when creating stand alone packages js package with css
				// the paths get messed up.
				/*if( !isset( $this->namedFileList[ 'mw.style.mwCommon' ] ) ) {
					$this->output .= $this->getResourceText( 'mw.style.mwCommon' );
				}*/

				// Output "special" IE comment tag to support "special" mwEmbed tags.
				$this->notMinifiedTopOutput .='/*@cc_on@if(@_jscript_version<9){\'video audio source itext playlist\'.replace(/\w+/g,function(n){document.createElement(n)})}@end@*/'."\n";
			}
		}

		/**
		 * Add a mw.loadDone resource callback if there was no "error" in getting any of the classes
		 */
		if ( $this->errorMsg == '' && $this->outputFormat == 'js' ){
			$this->output .= self::getOnDoneCallback( );
		}

		// Check if we should minify the whole thing:
		if ( !$this->debug && $this->outputFormat == 'js' ) {
			$this->output = self::getMinifiedJs( $this->output , $this->requestKey );
			$this->output = $this->notMinifiedTopOutput . $this->output;
		}

		// Save to the file cache
		if ( $wgUseFileCache && !$this->debug ) {
			$status = $this->sFileCache->saveToFileCache( $this->output );
			if ( $status !== true ) {
				$this->errorMsg .= "Could not save file to cache::" . $status;
			}
		}

		// Check for an error msg
		if ( $this->errorMsg != '' ) {
			//just set the content type (don't send cache header)
			header( 'Content-Type: text/javascript' );
			echo 'if(console && console.log)console.log(\'Error With ResourceLoader ::' .
					 str_replace( "\n", '\'+"\n"+' . "\n'",
					 	xml::escapeJsString( $this->errorMsg )
					 ) . '\');'."\n";
			echo trim( $this->output );
		} else {
			// All good, let's output "cache" headers
			$this->outputJsWithHeaders();
		}
	}
	/**
	 * Get the loadDone javascript callback for a given resource list
	 *
	 * Enables a done loading callback for browsers like older safari
	 * that did not consistently support the <script>.onload call
	 * or IE that sometimes gets undefined symbols at onload
	 * call time for associated script content
	 *
	 * @return String javascript to tell mwEmbed that the requested resource set is loaded
	 */
	static private function getOnDoneCallback( ){
		return "\n" . 'if( typeof mw !=\'undefined\' && mw.loadDone ){ mw.loadDone(\'' .
			htmlspecialchars( self::$rawClassList ) . '\')};';
	}

	/**
	 * Get Minified js
	 *
	 * @param {String} $js_string Javascript string to be minified
	 * @param {String} $requestKey Unique key for minification
	 * @return minified javascript value
	 */
	static function getMinifiedJs( & $js_string, $requestKey='' ){
		global $wgJavaPath, $wgClosureCompilerPath, $wgClosureCompilerLevel;


		// Check if google closure compiler is enabled and we can get its output
		if( $wgJavaPath && $wgClosureCompilerPath ){
			$jsMinVal = self::getClosureMinifiedJs( $js_string, $requestKey );
			if( $jsMinVal ){
				return $jsMinVal;
			}else{
				wfDebug( 'Closure compiler failed to produce code for:' . $requestKey);
			}
		}
		// Do the minification using php JSMin
		return JSMin::minify( $js_string );
	}
	/**
	 * Optional function to use the goggle closer compiler to minify js
	 * @param {String} $js_string Javascript string to be minified
	 * @param {String} $requestKey request key used for temporary name in closure compile
	 * @return minified js, or false if minification failed.
	 */
	static function getClosureMinifiedJs( & $js_string, $requestKey=''){
		global $wgClosureCompilerPath, $wgJavaPath, $wgClosureCompilerLevel;

		// Check the paths
		if( !is_file( $wgJavaPath ) || ! is_file( $wgClosureCompilerPath ) ){
			return false;
		}

		// Update the requestKey with a random value if not provided
		// requestKey is used for the temporary file
		// ( There are problems with using standard output and Closure compile )
		if( $requestKey == '') {
			$requestKey = rand() + microtime();
		}

		// Write the grouped javascript to a temporary file:
		// ( closure compiler does not support reading from standard in pipe )
		$td = wfTempDir();
		$jsFileName = $td . '/' . md5( $requestKey ) . '.tmp.js';
		file_put_contents( $jsFileName, $js_string );
		$retval = '';
		$cmd = $wgJavaPath . ' -jar ' . $wgClosureCompilerPath;
		$cmd.= ' --js ' . $jsFileName;

		if( $wgClosureCompilerLevel )
		$cmd.= ' --compilation_level ' . wfEscapeShellArg( $wgClosureCompilerLevel );

		// only output js ( no warnings )
		$cmd.= ' --warning_level QUIET';
		// Run the command:
		$jsMinVal = wfShellExec($cmd , $retval);

		// Clean up ( remove temporary file )
		wfSuppressWarnings();
		unlink( $jsFileName );
		wfRestoreWarnings();

		if( strlen( $jsMinVal ) != 0 && $retval === 0){
			//die( "used closure" );
			return $jsMinVal;
		}
		return false;
	}

	/**
	 * Get the javascript or css text content from a given classKey
	 *
	 * @param {String} $resourceName Resource Key to grab text for
	 * @param {String} [$filePath] Optional file path to get js text
	 * @return unknown
	 */
	function getResourceText( $resourceName ){
		$output = '';
		// Special case: title classes
		if ( substr( $resourceName, 0, 3 ) == 'WT:' ) {
			global $wgUser;
			// Get just the title part
			$titleBlock = substr( $resourceName, 3 );
			if ( $titleBlock[0] == '-' ) {
				// Special case of "-" title
				$parts = explode( '|', $titleBlock );
				$title = array_shift( $parts );
				$titleParams = array ();
				foreach ( $parts as $titleParam ) {
					list( $key, $val ) = explode( '=', $titleParam );
					$titleParams[ $key ] = $val;
				}
				/*
				 * The "-" is a special key to user / site js system sucks
				 * here is some code to handle it ... but it really
				 * should be depreciated for some more logical system
				 * like directly referencing the titles that we have a script-loader
				 */
				$sk = $wgUser->getSkin();
				if( isset( $titleParams[ 'useskin' ] ) ) {
					// Make sure the skin name is valid
					$skinNames = Skin::getSkinNames();
					$skinNames = array_keys( $skinNames );
					if ( in_array( strtolower( $titleParams[ 'useskin' ] ), $skinNames ) ) {
						if( $titleParams['gen' ] == 'css' ){
							return $sk->generateUserStylesheet();
						}
						// If in debug mode, add a comment with wiki title and rev:
						if ( $this->debug ) {
							$output .= "\n/**\n* GenerateUserJs: \n*/\n";
						}
						return $sk->generateUserJs( $titleParams[ 'useskin' ] ) . "\n";
					}
				} else if( isset( $titleParams['gen' ] ) && $titleParams['gen' ] == 'css' ) {
					return $sk->generateUserStylesheet();
				}
			} else {
				$ext = substr($titleBlock, strrpos($titleBlock, '.') + 1);
				// Make sure the wiki title ends with .js or .css
				if ( self::validFileExtension( $ext ) ) {
					$this->errorMsg .= 'WikiTitle includes should end with .js or .css';
					return false;
				}
				// It's a wiki title, append the output of the wikitext:
				$t = Title::newFromText( $titleBlock );
				$a = new Article( $t );
				// Only get the content if the page is not empty:
				if ( $a->getID() !== 0 ) {
					// If in debug mode, add a comment with wiki title and rev:
					if ( $this->debug ) {
						$output .= "\n/**\n* ScriptLoader WikiPage: " . xml::escapeJsString( $titleBlock ) . " rev: " . $a->getID() . " \n*/\n";
					}
					$fileContents = $a->getContent() . "\n";
					// transform the output if the file is of type css:
					$output.= ( $ext == 'css' ) ?
						$this->transformCssOutput( $resourceName, $fileContents ) :
						$fileContents;
					return $output;
				}
			}
		}

		// Deal with the classKey as a file:
		$filePath = self::getPathFromClass( $resourceName );

		if( ! $filePath ) {
			$this->errorMsg .= "\nError could not get file path: ". xml::escapeJsString( $resourceName ) ."\n";
			return false;
		}

		// Get the file extension:
		$ext = substr( $filePath, strrpos( $filePath, '.' ) + 1 );

		// Dealing with files
		if ( trim( $filePath ) != '' ) {
			$fileContents = $this->getFileContents( $filePath ) . "\n";
			if( $fileContents ){
				// Add the file name if debug is enabled
				if ( $this->debug ){
					$output .= "\n\n/**\n* File: " . xml::escapeJsString( $filePath ) . "\n*/\n";
				}
				// Transform the css output if the file is css
				$output.= ( $ext == 'css' ) ?
					$this->transformCssOutput( $resourceName, $fileContents, $filePath ) :
					$fileContents;

				return $output;
			}else{
				$this->errorMsg .= "\nError could not read file: ". xml::escapeJsString( $filePath ) ."\n";
				return false;
			}
		}
		// If we did not return some js
		$this->errorMsg .= "\nUnknown error in getting scriptText for key: " . xml::escapeJsString( $resourceName ) . "\n";
		return false;
	}

	/**
	 * Special function to transform css output and wrap in js call
	 */
	private function transformCssOutput( $resourceName, $cssString , $path ='') {
		global $wgScriptLoaderRelativeCss;
		// Minify and update paths on cssString:
		$cssOptions = array();

		// Set comments preservation based on debug state
		$cssOptions[ 'preserveComments' ] = ( $this->debug );

		// Check for the two ResourceLoader entry points:
		if( $wgScriptLoaderRelativeCss ) {
			// Using the local mediaWiki entry point we should have our $wgScriptPath global
			global $wgScriptPath;
			$prePendPath = ( $wgScriptPath == '' ) ? '' : $wgScriptPath . '/';
			$cssOptions[ 'prependRelativePath' ] = $prePendPath . dirname( $path ) . '/';
		} else {
			// Get the server URL
			$serverUri = $this->getResourceLoaderUri();

			$cssOptions[ 'prependRelativePath' ] = str_replace('ResourceLoader.php', '', $serverUri)
			. dirname( $path ) . '/';
		}

		// We always run minify to update css image urls
		$cssString = Minify_CSS::minify( $cssString, $cssOptions);

		// Check output format ( if format is "css" ) return the css string directly
		if( $this->outputFormat == 'css' ) {
			return $cssString;
		}

		// Output Format is "javascript" return the string in an addStyleString call
		// CSS classes should be of form mw.style.{className}
		$cssStyleName = str_replace('mw.style.', '', $resourceName );
		return 'mw.addStyleString("' . Xml::escapeJsString( $cssStyleName )
		. '", "' . Xml::escapeJsString( $cssString ) . '");' . "\n";
	}

	/**
	 * Get the URI of the scriptLoader
	 */
	private function getResourceLoaderUri() {
		// protocol is http or https
		$protocol = 'http';
		if ($_SERVER['SERVER_PORT'] == 443 || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')) {
			$protocol .= 's';
			$protocol_port = $_SERVER['SERVER_PORT'];
		} else {
			$protocol_port = 80;
		}

		// $port will be "" or something like ":8100"
		$port = ( $_SERVER['SERVER_PORT'] == $protocol_port ) ? '' : ':' . $_SERVER['SERVER_PORT'];

		// php_self is the URL that invoked this script, without CGI parameters or fragment.
		return $protocol . '://' . $_SERVER['HTTP_HOST'] . $port . $_SERVER['PHP_SELF'];
	}


	/**
	 * Outputs the script headers
	 */
	function outputHeaders() {
		// Output MIME type:
		if( $this->outputFormat == 'css' ){
			header( 'Content-Type: text/css' );
		} else if ( $this->outputFormat == 'js' || $this->outputFormat == 'messages' ) {
			header( 'Content-Type: text/javascript' );
		}
		header( 'Pragma: public' );
		if( $this->debug ){
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		}else{
			// Cache for 1 day ( we update the request urid so this has a long expire delay )
			$one_day = 60 * 60 * 24;
			header( "Expires: " . gmdate( "D, d M Y H:i:s", time() + $one_day ) . " GM" );
		}
	}

	/**
	 * Outputs the javascript text with script headers
	 */
	function outputJsWithHeaders() {
		global $wgUseGzip;
		$this->outputHeaders();
		if ( $wgUseGzip ) {
			if ( $this->clientAcceptsGzip() ) {
				header( 'Content-Encoding: gzip' );
				echo gzencode( $this->output );
			} else {
				echo $this->output;
			}
		} else {
			echo $this->output;
		}
	}

	/**
	 * Checks if client Accepts Gzip response
	 *
	 * @return boolean
	 * 	true if client accepts gzip encoded response
	 * 	false if client does not accept gzip encoded response
	 */
	static function clientAcceptsGzip() {
		$m = array();
		if( isset( $_SERVER['HTTP_ACCEPT_ENCODING'] ) ){
			if( preg_match(
				'/\bgzip(?:;(q)=([0-9]+(?:\.[0-9]+)))?\b/',
			$_SERVER['HTTP_ACCEPT_ENCODING'],
			$m ) ) {
				if( isset( $m[2] ) && ( $m[1] == 'q' ) && ( $m[2] == 0 ) )
				return false;
				//no gzip support found
				return true;
			}
		}
		return false;
	}

	/**
	 * Post process request uses globals and mediaWiki configuration to
	 * validate classes and generate request key
	 */
	function postProcRequestVars(){
		global $wgContLanguageCode, $wgResourceLoaderNamedPaths,
		$wgStyleVersion;

		// Set debug flag
		if ( ( isset( $_GET['debug'] ) && $_GET['debug'] == 'true' )
		|| ( isset( $wgEnableScriptDebug ) && $wgEnableScriptDebug == true ) ) {
			$this->debug = true;
		}

		// Set the urid. Be sure to escape it as it goes into our JS output.
		if ( isset( $_GET['urid'] ) && $_GET['urid'] != '' ) {
			$this->urid = htmlspecialchars( $_GET['urid'] );
		} else {
			// Just give it the current style sheet ID:
			// NOTE: read the svn version number
			$this->urid = $wgStyleVersion;
		}

		// Get the language code (if not provided use the "default" language
		if ( isset( $_GET['uselang'] ) && $_GET['uselang'] != '' ) {
			// Strip any non alphaNumeric or dash characters from the language code:
			$this->langCode = preg_replace( "/[^A-Za-z\-_]/", '', $_GET['uselang']);
		}else{
			//set English as default
			$this->langCode = 'en';
		}
		$this->langCode = self::checkForCommonsLanguageFormHack( $this->langCode );

		$reqClassList = false;
		if ( isset( $_GET['class'] ) && $_GET['class'] != '' ) {
			$reqClassList = explode( ',', $_GET['class'] );
			self::$rawClassList = $_GET['class'];
		}

		// Check for the requested classes
		if ( $reqClassList ) {
			// sanitize the Resource list and populate namedFileList
			foreach ( $reqClassList as $reqClass ) {
				if ( trim( $reqClass ) != '' ) {
					if ( substr( $reqClass, 0, 3 ) == 'WT:' ) {
						$doAddWT = false;
						// Check for special case '-' resource for user-generated JS
						if( substr( $reqClass, 3, 1) == '-'){
							$doAddWT = true;
						}else{
							if( strtolower( substr( $reqClass, -3) ) == '.js'){
								//make sure its a valid wikipage before doing processing
								$t = Title::newFromDBkey( substr( $reqClass, 3) );
								if( $t->exists()
								&& ( $t->getNamespace() == NS_MEDIAWIKI
								|| $t->getNamespace() == NS_USER ) ){
									$doAddWT = true;
								}
							}
						}
						if( $doAddWT ){
							$this->namedFileList[$reqClass] = true;
							$this->requestKey .= $reqClass;
							$this->jsvarurl = true;
						}
						continue;
					}

					$reqClass = preg_replace( "/[^A-Za-z0-9_\-\.]/", '', $reqClass );

					$filePath = self::getPathFromClass( $reqClass );
					if( !$filePath ){
						$this->errorMsg .= 'Requested class: ' . xml::escapeJsString( $reqClass ) . ' not found' . "\n";
					}else{
						$this->namedFileList[ $reqClass ] = $filePath;
						$this->requestKey .= $reqClass;
					}
				}
			}
		}

		// Add the language code to the requestKey:
		$this->requestKey .= '_' . $wgContLanguageCode;

		// Add the unique rid
		$this->requestKey .= $this->urid;

	}
	/**
	 * Pre-process request variables ~without configuration~ or any utility functions.
	 *
	 *  This is to quickly get a requestKey that we can check against the cache,
	 *  request key validation is done in postProcRequestVars
	 */
	function preProcRequestVars() {
		$requestKey = '';

		// Update the outupt format is "css", "messages" or "js"
		if( isset( $_GET['format'] ) &&
			( $_GET['format'] == 'css' || $_GET['format'] == 'messages')
		){
			$this->outputFormat = $_GET['format'];
		} else {
			$this->outputFormat = 'js';
		}

		// Check for debug ( won't use the cache)
		if ( ( isset( $_GET['debug'] ) && $_GET['debug'] == 'true' ) ) {
			// We are going to have to run postProcRequest
			return false;
		}

		// Check for the urid. Be sure to escape it as it goes into our JS output.
		if ( isset( $_GET['urid'] ) && $_GET['urid'] != '' ) {
			$urid = htmlspecialchars( $_GET['urid'] );
		}else{
			// If no urid is set use special "cache" version.
			// (this requires that the cache files be removed for updates to take effect.)
			$urid = 'cache';
		}

		// Get the language code (if not provided use the "default" language
		if ( isset( $_GET['uselang'] ) && $_GET['uselang'] != '' ) {
			// Make sure its just a simple [A-Za-z] value
			$langCode = preg_replace( "/[^A-Za-z_]/", '', $_GET['uselang']);
		}else{
			// Set English as default
			$langCode = 'en';
		}
		$langCode = self::checkForCommonsLanguageFormHack( $langCode );

		$reqClassList = false;
		if ( isset( $_GET['class'] ) && $_GET['class'] != '' ) {
			$reqClassList = explode( ',', $_GET['class'] );
		}

		// Check for the requested classes
		if ( $reqClassList && count( $reqClassList ) > 0 ) {
			// Clean the resource list and populate namedFileList
			foreach ( $reqClassList as $reqClass ) {
				//do some simple checks:
				if ( trim( $reqClass ) != '' ){
					if( substr( $reqClass, 0, 3 ) == 'WT:' && strtolower( substr( $reqClass, -3) ) == '.js' ){
						// Wiki page requests (must end with .js):
						$requestKey .= $reqClass;
					}else if( substr( $reqClass, 0, 3 ) != 'WT:' ){
						// Normal resource requests:
						$reqClass = preg_replace( "/[^A-Za-z0-9_\-\.]/", '', $reqClass );
						$requestKey .= $reqClass;
					}else{
						// Not a valid class
					}
				}
			}
		}

		// Add the language code to the requestKey:
		$requestKey .= '_' . $langCode;

		// Add the format to the requestKey:
		$requestKey .= '_' . $this->outputFormat;

		// Add the unique rid
		$requestKey .= '_' . $urid;

		return $requestKey;
	}

	/**
	 * Check for the commons language hack.
	 * ( someone had the bright idea to use language keys as message
	 *  name-spaces for separate upload forms )
	 *
	 * @param {String} $langKey The lang key for the form
	 */
	public static function checkForCommonsLanguageFormHack( $langKey){
		$formNames = array( 'ownwork', 'fromflickr', 'fromwikimedia', 'fromgov');
		foreach( $formNames as $formName ){
			// Directly reference a form Name then its "english"
			if( $formName == $langKey ){
				return 'en';
			}
			// If the langKey includes a form name (ie esownwork or es-ownwork)
			// then strip the form name use that as the language key
			if( strpos($langKey, $formName) !==false ){
				$langKey = str_replace($formName, '', $langKey);
				//English wikipedia puts "-" after language keys remove that:
				if( $langKey[ strlen( $langKey ) - 1 ] == '-'){
					$langKey = substr( $langKey, 0, strlen( $langKey ) - 1 );
				}
				return $langKey;
			}
		}
		// Else just return the key unchanged:
		return $langKey;
	}

	/**
	 * Get a file path for a given class
	 *
	 * @param {String} $reqClass Resource key to get the path for
	 * @return path of the resource or "false"
	 */
	public static function getPathFromClass( $reqClass ){
		global $wgResourceLoaderNamedPaths;
		// Make sure the resource is loaded:
		try {
			NamedResourceLoader::loadResourcePaths();
		} catch( Exception $e ) {
			$this->errorMsg .= "getPathFromClass: " . $e->getMessage() ;
		}

		if ( isset( $wgResourceLoaderNamedPaths[ $reqClass ] ) ) {
			return $wgResourceLoaderNamedPaths[ $reqClass ];
		} else {
			return false;
		}
	}

	// Check that the filename ends with .js or .css
	function validFileExtension( $ext ){
		return !( $ext == 'js'	 || $ext == 'css');
	}

	/**
	 * Retrieve the js or css file into a string, updates errorMsg if not retrievable.
	 *
	 * @param {String} $filePath File to get
	 * @return {String} of the file contents
	 */
	function getFileContents( $filePath ) {
		global $IP;

		$ext = substr($filePath, strrpos($filePath, '.') + 1);
		if ( self::validFileExtension( $ext) ) {
			$this->errorMsg .= "\nError file name must end with .js or .css " . htmlspecialchars( $filePath ) . " \n ";
			return false;
		}

		// Check the file does not include ../ traversing
		if ( strpos( $filePath, '../' ) !== false ) {
			$this->errorMsg .= "\nError file name must not traverse paths: " . htmlspecialchars( $filePath ) . " \n ";
			return false;
		}
		// Load the file
		wfSuppressWarnings();
		$fileContents = file_get_contents( realpath( $filePath ) );
		wfRestoreWarnings();

		if ( $fileContents === false ) {
			$fname = explode( '/',$filePath);
			$fname = end( $fname );
			// NOTE: check PHP error level. Don't want to expose paths if errors are hidden.
			$this->errorMsg .= 'Requested File: ' . htmlspecialchars( $filePath ) . ' could not be read' . "\n";
			return false;
		}
		return $fileContents;
	}

	/**
	 * Get a localized script javascript
	 *
	 * Strips debug statements:  mw.log( 'msg' );
	 * Localizes the javascript calling the languageMsgReplace function
	 *
	 * @param {String} $resourceName Name of resource to be processed.
	 * @return processed javascript string
	 */
	function getLocalizedScriptText( $resourceName ){
		global $wgEnableScriptLocalization;

		// Get the script text:
		$scriptText = $this->getResourceText( $resourceName );
		if( !$scriptText ) {
			// Specific error already reported in getResourceText()
			return '';
		}
		$moduleName = NamedResourceLoader::getModuleNameForResource( $resourceName );
		$scriptText = $this->transformScriptText( $scriptText, $moduleName );

		// Return the js string unmodified if we did not transform with the localisation.
		return $scriptText;
	}
	/*
	 * Transform script text with language key substitution
	 * and clear out debug lines if present.
	 * @param {String} $scriptText Text string to be transformed
	 */
	function transformScriptText( $scriptText , $moduleName){
		global $wgEnableScriptLocalization;
		// Strip out mw.log debug lines (if not in debug mode)
		if( !$this->debug ){
			$scriptText = $this->removeLogStatements( $scriptText );
		}

		// Do language swap by index:
		if ( $wgEnableScriptLocalization ){
			// NOTE getResourceMessageJS could identify which mode we are in and we would not need to
			// try each of these search patterns in the same order as before.

			// Get the mw.addMessage javascript
			self::$addMessageJs = $this->getAddMessagesFromScriptText( $scriptText , $moduleName);

			// Check for mw.includeAllModuleMsgs() call to be replaced with all the msgs
			// Use preg_replace_callback to avoid back-refrence substitution
			$scriptText = preg_replace_callback(
				self::$includeAllMsgsRegEx,
				'ResourceLoader::preg_addMessageJs',
			 	$scriptText,
			 	1,
			 	$count
			 );


			if( $count != 0 ){
				return $scriptText;
			}

			// Replace mw.addMessages with localized msgs in javascript string
			$inx = self::getAddMessagesIndex( $scriptText );
			if( $inx ){
				// Return the final string (without double {})
				return substr($scriptText, 0, $inx['sfull']) . self::$addMessageJs . substr($scriptText, $inx['efull']);
			}

			// Replace mw.addMessageKeys with localized msgs in javascript string
			$inx = self::getAddMessageKeyIndex( $scriptText );
			if( $inx ) {
				// Return the final string (without double {})
				return substr( $scriptText, 0, $inx['sfull'] ). self::$addMessageJs . substr($scriptText, $inx['efull']);
			}
		}
		// Return the javascript str unmodified if we did not transform with the localisation
		return $scriptText;
	}
	/**
	 * Remove all occurances of mw.log( 'some js string or expresion' );
	 * @param {string} $jsString
	 */
	static function removeLogStatements( $jsString ){
		$outputJs = '';
		for ( $i = 0; $i < strlen( $jsString ); $i++ ) {
			// find next occurrence of
			preg_match( '/([\n;]\s*mw\.log\s*)\(/', $jsString, $matches, PREG_OFFSET_CAPTURE, $i );
			// check if any matches are left:
			if( count( $matches ) == 0){
				$outputJs .= substr( $jsString, $i );
				break;
			}
			if( count( $matches ) > 0 ){
				$startOfLogIndex = strlen( $matches[1][0] ) + $matches[1][1];
				// append everything up to this point:
				$outputJs .= substr( $jsString, $i, ( $startOfLogIndex - strlen( $matches[1][0] ) )-$i );

				// Increment i to position of closing ) not inside quotes
				$parenthesesDepth = 0;
				$ignorenext = false;
				$inquote = false;
				$inSingleQuote = false;
				for ( $i = $startOfLogIndex; $i < strlen( $jsString ); $i++ ) {
					$char = $jsString[$i];
					if ( $ignorenext ) {
						$ignorenext = false;
					} else {
						// Search for a close ) that is not in quotes
						switch( $char ) {
							case '"':
								if( !$inSingleQuote ){
									$inquote = !$inquote;
								}
							break;
							case '\'':
								if( !$inquote ){
									$inSingleQuote = !$inSingleQuote;
								}
							break;
							case '(':
								if( !$inquote && !$inSingleQuote ){
									$parenthesesDepth++;
								}
							break;
							case ')':
								if( ! $inquote && !$inSingleQuote ){
									$parenthesesDepth--;
								}
							break;
							case '\\':
								if ( $inquote ) $ignorenext = true;
							break;
						}
						// Done with close parentheses search for next mw.log in outer loop:
						if( $parenthesesDepth === 0 ){
							break;
						}
					}
				}
			}
		}
		return $outputJs;
	}
	/* simple function to return addMessageJs without preg_replace back reference substitution */
	private static function preg_addMessageJs(){
		return self::$addMessageJs;
	}

	/**
	 * Get the "addMesseges" function index ( for replacing msg text with localized json )
	 *
	 * @param {String} $str Javascript string to grab msg text from
	 * @return {Array} Array with start and end points character indexes
	 */
	static public function getAddMessagesIndex( $str ){
		$returnIndex = array();
		preg_match( '/mw.addMessages\s*\(\s*\{/', $str, $matches, PREG_OFFSET_CAPTURE );
		if( count($matches) == 0){
			return false;
		}
		if( count( $matches ) > 0 ){
			//offset + match str length gives startIndex:
			$returnIndex['s'] = strlen( $matches[0][0] ) + $matches[0][1];
			// Also store the full replacement index
			$returnIndex['sfull'] = $matches[0][1];
		}
		$ignorenext = false;
		$inquote = false;

		// Look for closing } not inside quotes::
		for ( $i = $returnIndex['s']; $i < strlen( $str ); $i++ ) {
			$char = $str[$i];
			if ( $ignorenext ) {
				$ignorenext = false;
			} else {
				// Search for a close } that is not in quotes or escaped
				switch( $char ) {
					case '"':
						$inquote = !$inquote;
						break;
					case '}':
						if( ! $inquote){
							$returnIndex['e'] = $i;
						}
						break;
					case '\\':
						if ( $inquote ) $ignorenext = true;
						break;
				}
			}
			// If return index is set break out
			if( isset( $returnIndex['e'] ) ) {
				break;
			}
		}
		// Check for ); at the end
		preg_match( '/\s*\)\s*\;?/', $str, $matches, PREG_OFFSET_CAPTURE, $returnIndex['e'] );
		if( $matches[0][1] ){
			$returnIndex[ 'efull' ] = $matches[0][1] + strlen( $matches[0][0] );
		}
		return $returnIndex;
	}

	/**
	 * Get the "addMessageKey" function index ( for replacing msg keys with localized json addMessage call)
	 *
	 * @param {String} $str Javascript string to grab msg text from
	 * @return {Array} Array with start and end points character indexes
	 */
	static public function getAddMessageKeyIndex( $str ){
		$returnIndex = array();
		preg_match( '/mw.addMessageKeys\s*\(\s*\[/', $str, $matches, PREG_OFFSET_CAPTURE );
		if( count( $matches ) == 0 ) {
			return false;
		}
		if( count( $matches ) > 0 ) {
			//offset + match str length gives startIndex:
			$returnIndex['s'] = strlen( $matches[0][0] ) + $matches[0][1];
			// Also store the full replacement index
			$returnIndex['sfull'] = $matches[0][1];
		}
		$inquote = false;
		$ignorenext = false;
		$inSingleQuote = false;
		// Look for closing ] not inside quotes::
		for ( $i = $returnIndex['s']; $i < strlen( $str ); $i++ ) {
			$char = $str[$i];
			if ( $ignorenext ) {
				$ignorenext = false;
			} else {
				// Search for a close ] that is not in quotes or escaped
				switch( $char ) {
					case '"':
						$inquote = !$inquote;
						break;
					case '\'':
						$inSingleQuote = !$inSingleQuote;
						break;
					case ']':
						if( ! $inquote){
							$returnIndex['e'] = $i;
						}
						break;
					case '\\':
						if ( $inquote || $inSingleQuote ) $ignorenext = true;
						break;
				}
				if( isset( $returnIndex['e'] ) ){
					break;
				}
			}
		}
		// look for at ); end
		preg_match( '/\s*\)\s*\;?/', $str, $matches, PREG_OFFSET_CAPTURE , $returnIndex['e']);
		if( $matches[0][1] ){
			$returnIndex[ 'efull' ] = $matches[0][1] + strlen( $matches[0][0] );
		}

		return $returnIndex;
	}

	/**
	 * Generates an in-line addMessege call for page output.
	 * For use with OutputPage when the script-loader is disabled.
	 *
	 * @param {String} $resourceName of resource to get inline messages for.
	 * @return in-line msg javascript text or empty string if no msgs need to be localized.
	 */
	function getResourceMessageJS( $resourceName ) {
		$scriptText = $this->getResourceText( $resourceName );
		$moduleName = NamedResourceLoader::getModuleNameForResource( $resourceName );
		return $this->getAddMessagesFromScriptText( $scriptText, $moduleName );
	}

	/**
	 * getAddMessagesFromScriptText generates a javascript addMesseges call for a given scriptText
	 *
	 * @param String $scriptText String of javascript text to search for message replacement text
	 * @param String $moduleName Name of scriptText module ( that hosts messages )
	 * @return string
	 */
	function getAddMessagesFromScriptText( & $scriptText, $moduleName ) {
		$messageSet = $this->getMsgKeysFromScriptText( $scriptText , $moduleName);
		if( $messageSet ) {
			self::updateMessageValues ( $messageSet , $this->langCode );
			return 'mw.addMessages(' . FormatJson::encode( $messageSet ) . ');';
		} else {
			//if could not parse return empty string:
			return '';
		}
	}

	/**
	 * Get the set of message associated from scriptText
	 *
	 * @param {String} $resourceName Resource to restive messages from
	 * @return {Array} decoded json array of message key value pairs
	 */
	function getMsgKeysFromScriptText( & $scriptString , $moduleName){
		global $wgExtensionMessagesFiles;

		// Try for includeAllModuleMsgs function call
		if ( preg_match ( self::$includeAllMsgsRegEx, $scriptString ) !== 0 ) {
			// Get the module $messages keys
			if( $moduleName && isset( $wgExtensionMessagesFiles[ $moduleName ] ) ) {

				// Empty out messages in the current scope
				$messages = array();

				// Get the i18n file from $wgExtensionMessagesFiles path
				require( $wgExtensionMessagesFiles[ $moduleName ] );

				// Return the English key set ( since base message text is in english )
				return $messages['en'];
			} else {
				// No $moduleName found.
			}
		}

		// Try for 'AddMessageKey' Index array type
		$inx = self::getAddMessageKeyIndex( $scriptString );
		if( $inx ) {
			// get the javascript array string:
			$javaScriptArrayString = substr($scriptString, $inx['s'], ($inx['e']-$inx['s'])) ;
			// Match all the quoted msg keys
			preg_match_all( "/\"([^\"]*)\"/", $javaScriptArrayString, $matches);
			$messageSet = array();
			if( $matches[1] ) {
				// Flip the matches array
				// The keys are msg keys the message values ( indexes ) are not used
				return array_flip( $matches[1] );
			}
		}

		// Try for AddMessagesIndex
		$inx = self::getAddMessagesIndex( $scriptString );
		if( $inx ) {
			return FormatJson::decode( '{' . substr($scriptString, $inx['s'], ($inx['e']-$inx['s'])) . '}', true);
		}

		// return an empty array if we are not able to grab any message keys
		return array();
	}

	/**
	 * Updates an array of messages with the wfMsgGetKey value
	 *
	 * @param {Array} $jmsg Associative array of message key -> message value pairs
	 * @param {String} $langCode Language code override
	 */
	static public function updateMessageValues( & $messegeArray, $langCode = false ){
		global $wgLang;
		// Check the langCode
		if(!$langCode && $wgLang) {
			$langCode = $wgLang->getCode();
		}
		// Get the msg keys for the a json array
		foreach ( $messegeArray as $msgKey => $na ) {
			// Language codes use dash instead of underscore internally
			$msgLangCode = str_replace('_', '-', $langCode );
			$messegeArray[ $msgKey ] = wfMsgGetKey( $msgKey, true, $msgLangCode, false );
		}
	}

	/**
	 * Replace a string of json msgs with the translated json msgs.
	 *
	 * @param {String} $json_str Json string to be replaced
	 * @return {String} of msgs updated with the given language code
	 */
	function languageMsgReplace( $json_str , $format='json') {
		$jmsg = FormatJson::decode( '{' . $json_str . '}', true );
		// Do the language lookup
		if ( $jmsg ) {

			self::updateMessageValues( $jmsg, $this->langCode );

			// Return the updated JSON with Msgs:
			return FormatJson::encode( $jmsg );
		} else {
			// Could not parse JSON return error: (maybe a alert?)
			//we just make a note in the code, visitors will get the fallback language,
			//developers will read the js source when its not behaving as expected.
			return "\n/*
* Could not parse JSON language messages in this file,
* Please check that mw.addMessages call contains valid JSON (not javascript)
*/\n\n{" . $json_str . "\n}"; //include the original fallback msg string
		}
	}
}

/*
 *  A simple version of HTMLFileCache so that the resourceLoader can operate stand alone
 */
class simpleFileCache {
	var $mFileCache;
	var $filename = null;
	var $requestKey = null;

	/**
	 * Constructor
	 *
	 * @param {String} $requestKey Request key for unique identifying this cache file
	 */
	public function __construct( $requestKey ) {
		$this->requestKey = $requestKey;
		$this->getCacheFileName();
	}

	/**
	 * Get cache file file Name based on $requestKey and if gzip is enabled or not
	 * Updates the local filename var
	 *
	 * @return {String} file path
	 */
	public function getCacheFileName() {
		global $wgUseGzip, $wgScriptCacheDirectory;

		$hash = md5( $this->requestKey );
		# Avoid extension confusion
		$key = str_replace( '.', '%2E', urlencode( $this->requestKey ) );

		$hash1 = substr( $hash, 0, 1 );
		$hash2 = substr( $hash, 0, 2 );
		$this->filename = "{$wgScriptCacheDirectory}/{$hash1}/{$hash2}/{$hash}.js";

		// Check for defined files::
		if( is_file( $this->filename ) ){
			return $this->filename;
		}

		// Check for non-config based gzip version already there?
		if( is_file( $this->filename . '.gz') ){
			$this->filename .= '.gz';
			return $this->filename;
		}
		//Update the name based on the $wgUseGzip config var
		if ( isset($wgUseGzip) && $wgUseGzip )
		$this->filename.='.gz';

		return $this->filename;
	}
	/**
	 * Checks if file is cached
	 */
	public function isFileCached() {
		return file_exists( $this->filename );
	}

	/**
	 * Loads and outputs the file from the file cache
	 */
	public function outputFile() {
		if ( ResourceLoader::clientAcceptsGzip() && substr( $this->filename, -3 ) == '.gz' ) {
			header( 'Content-Encoding: gzip' );
			readfile( $this->filename );
			return true;
		}
		// Output without gzip:
		if ( substr( $this->filename, -3 ) == '.gz' ) {
			readgzfile( $this->filename );
		} else {
			readfile( $this->filename );
		}
		return true;
	}
	/**
	 * Saves text string to file
	 * @param unknown_type $text
	 */
	public function saveToFileCache( &$text ) {
		global $wgUseFileCache, $wgUseGzip;
		if ( !$wgUseFileCache ) {
			return 'Error: Called saveToFileCache with $wgUseFileCache off';
		}
		if ( strcmp( $text, '' ) == 0 )
		return 'saveToFileCache: empty output file';

		if ( $wgUseGzip ) {
			$outputText = gzencode( trim( $text ) );
		} else {
			$outputText = trim( $text );
		}

		// Check the directories. If we could not create them, error out.
		$status = $this->checkCacheDirs();

		if ( $status !== true )
		return $status;
		$f = fopen( $this->filename, 'w' );
		if ( $f ) {
			fwrite( $f, $outputText );
			fclose( $f );
		} else {
			return 'Could not open file for writing. Check your cache directory permissions?';
		}
		return true;
	}
	/**
	 * Checks cache directories and creates the directories if not present
	 */
	protected function checkCacheDirs() {
		$mydir2 = substr( $this->filename, 0, strrpos( $this->filename, '/' ) ); # subdirectory level 2
		$mydir1 = substr( $mydir2, 0, strrpos( $mydir2, '/' ) ); # subdirectory level 1

		// Suppress error so javascript can format it
		if ( @wfMkdirParents( $mydir1 ) === false || @wfMkdirParents( $mydir2 ) === false ) {
			return 'Could not create cache directory. Check your cache directory permissions?';
		} else {
			return true;
		}
	}
}
