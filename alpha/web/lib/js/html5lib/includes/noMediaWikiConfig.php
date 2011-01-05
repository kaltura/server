<?php
/**
 * NoMediaWikiConfig sets variables for using the script-loader and mwEmbed modules
 * without a complete mediaWiki install.
 *
 * NoMediaWikiConfig also copies some utility functions from mediaWiki
 */

// Google Closure Compiler ( for improved minification )
$wgClosureCompilerPath = false;
$wgJavaPath = false;
$wgClosureCompilerLevel = 'SIMPLE_OPTIMIZATIONS';

// Give us true for MediaWiki
define( 'MEDIAWIKI', true );

define( 'MWEMBED_STANDALONE', true );

// Setup the globals: 	(for documentation see: DefaultSettings.php )

$IP = realpath( dirname( __FILE__ ) . '/../' );

// $wgMwEmbedDirectory becomes the root file system:
$wgMwEmbedDirectory = '';

$wgScriptPath = '';

$wgUseFileCache = true;

// Named paths for the script loader
$wgResourceLoaderNamedPaths = array();

//If the scriptLoader should use relative css
$wgScriptLoaderRelativeCss = false;

// Named Module Paths
$wgExtensionJavascriptModules = array();

// Extension Messages Files
$wgExtensionMessagesFiles = array();

/*Localization:*/
$wgEnableScriptLocalization = true;

// Array to store all loaded msgs
$wgMessageCache = array();

// flag for loading msgs
$wgLoadedMsgKeysFlag = false;

$mwLanguageCode = 'en';
$wgLang = false;

$wgStyleVersion = '218';
$wgEnableScriptMinify = true;
$wgUseGzip = true;

// If the core loader.js should be used to set the module list.
$wgUseMwEmbedLoaderModuleList = true;

/**
 * Default value for chmoding of new directories.
 */
$wgDirectoryMode = 0777;

$wgJsMimeType = 'text/javascript';

// Get the autoload classes
require_once( realpath( dirname( __FILE__ ) ) . '/NamedResourceLoader.php' );

// Get the JSmin class:
require_once( realpath( dirname( __FILE__ ) ) . '/library/JSMin.php' );

// Get the css class:
require_once( realpath( dirname( __FILE__ ) ) . '/library/CSS.php' );
require_once( realpath( dirname( __FILE__ ) ) . '/library/CSS/Compressor.php' );
require_once( realpath( dirname( __FILE__ ) ) . '/library/CSS/UriRewriter.php' );
require_once( realpath( dirname( __FILE__ ) ) . '/library/CommentPreserver.php' );


function wfDebug() {
	return false;
}

if (!function_exists('wfTempDir'))
{
	function wfTempDir(){
		return realpath( dirname( __FILE__ ) ) . '/includes/cache';
	}
}
/**
 * Make directory, and make all parent directories if they don't exist
 *
 * @param string $dir Full path to directory to create
 * @param int $mode Chmod value to use, default is $wgDirectoryMode
 * @param string $caller Optional caller param for debugging.
 * @return bool
 */
function wfMkdirParents( $dir, $mode = null, $caller = null ) {
	global $wgDirectoryMode;

	if ( !is_null( $caller ) ) {
		wfDebug( "$caller: called wfMkdirParents($dir)" );
	}

	if ( strval( $dir ) === '' || file_exists( $dir ) )
	return true;

	if ( is_null( $mode ) )
	$mode = $wgDirectoryMode;

	return @mkdir( $dir, $mode, true );  // PHP5 <3
}

/**
 * Copied from mediaWIki GlobalFunctions.php wfMsgGetKey
 * but we return [] instead of &lt; &gt; since &lt; does not
 * look good in javascript msg strings
 *
 * Fetch a message string value, but don't replace any keys yet.
 * @param $key String
 * @param $useDB Bool
 * @param $langCode String: Code of the language to get the message for, or
 *                  behaves as a content language switch if it is a boolean.
 * @param $transform Boolean: whether to parse magic words, etc.
 * @return string
 * @private
 */
function wfMsgGetKey( $msgKey, $na, $langKey = false ) {
	global $wgLoadedMsgKeysFlag, $wgMessageCache, $mwLanguageCode;

	if( !$langKey ){
		$langKey = $mwLanguageCode;
	}

	// Make sure msg Keys are loaded
	if( !$wgLoadedMsgKeysFlag ) {
		wfLoadMsgKeys( $langKey );
	}

	if ( isset( $wgMessageCache[$msgKey] ) ) {
		return $wgMessageCache[$msgKey];
	} else {
		return '[' . $msgKey . ']';
	}
}
/**
 * Load all the msg keys into $wgMessageCache
 * @param $langKey String Language key to be used
 */
function wfLoadMsgKeys( $langKey ){
	global $wgExtensionMessagesFiles, $wgMessageCache, $wgLoadedMsgKeysFlag;
	if( $wgLoadedMsgKeysFlag ) {
		return true;
	}
	if( !$wgMessageCache ){
		$wgMessageCache = array();
	}
	foreach( $wgExtensionMessagesFiles as $msgFile ){
		if( !is_file( $msgFile ) ) {
			throw new MWException( "Missing msgFile: " . htmlspecialchars( $msgFile ) . "\n" );
		}
		require( $msgFile );
		// First include the English fallback,
		$wgMessageCache = array_merge( $wgMessageCache, $messages[ 'en' ] );
		// Then override with the current language:
		if( isset( $messages[ $langKey ] ) ) {
			$wgMessageCache = array_merge( $wgMessageCache, $messages[ $langKey ] );
		}
	}
	$wgLoadedMsgKeysFlag = true;
}

/**
 * mediaWiki abstracts the json functions with fallbacks
 * here we just map directly to the call
 */
class FormatJson{
	public static function encode($value, $isHtml=false){
		return json_encode($value);
	}
	public static function decode( $value, $assoc=false ){
		return json_decode( $value, $assoc );
	}
}
// MWException extends Exception (for noWiki we don't do anything fancy )
class MWException extends Exception {
}

$mweSimpleProfileTester = array();
function wfProfileIn( $name ){
	global $mweSimpleProfileTester;
	$mweSimpleProfileTester[ $name ] = microtime_float();
};
function wfProfileOut( $name ){
	global $mweSimpleProfileTester;
	//print "run time for $name :: " . ( microtime_float() - $mweSimpleProfileTester[ $name ] ) . "\n";
}
function microtime_float()
{
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}

/**
 * Reference-counted warning suppression
 */
function wfSuppressWarnings( $end = false ) {
	static $suppressCount = 0;
	static $originalLevel = false;

	if ( $end ) {
		if ( $suppressCount ) {
			--$suppressCount;
			if ( !$suppressCount ) {
				error_reporting( $originalLevel );
			}
		}
	} else {
		if ( !$suppressCount ) {
			$originalLevel = error_reporting( E_ALL & ~( E_WARNING | E_NOTICE ) );
		}
		++$suppressCount;
	}
}

/**
 * Restore error level to previous value
 */
function wfRestoreWarnings() {
	wfSuppressWarnings( true );
}


/**
 * Simplifed MediaWiki wfShellExec Function
 *
 * Execute a shell command, with time and memory limits mirrored from the PHP
 * configuration if supported.
 * @param $cmd Command line, properly escaped for shell.
 * @param &$retval optional, will receive the program's exit code.
 *                 (non-zero is usually failure)
 * @return collected stdout as a string (trailing newlines stripped)
 */
function wfShellExec( $cmd, &$retval=null ) {
	$retval = 1; // error by default?
	ob_start();
	passthru( $cmd, $retval );
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}

/* Stubs for mediawiki XML class */
class Xml {
	public static function escapeJsString( $string ) {
		// See ECMA 262 section 7.8.4 for string literal format
		$pairs = array(
			"\\" => "\\\\",
			"\"" => "\\\"",
			'\'' => '\\\'',
			"\n" => "\\n",
			"\r" => "\\r",

		# To avoid closing the element or CDATA section
			"<" => "\\x3c",
			">" => "\\x3e",

		# To avoid any complaints about bad entity refs
			"&" => "\\x26",

		# Work around https://bugzilla.mozilla.org/show_bug.cgi?id=274152
		# Encode certain Unicode formatting chars so affected
		# versions of Gecko don't misinterpret our strings;
		# this is a common problem with Farsi text.
			"\xe2\x80\x8c" => "\\u200c", // ZERO WIDTH NON-JOINER
			"\xe2\x80\x8d" => "\\u200d", // ZERO WIDTH JOINER
		);
		return strtr( $string, $pairs );
	}
}