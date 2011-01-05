<?php
/**
 * The javascript resource loader handles loading lists of available
 * javascript classes into php from their defined locations in javascript.
 */

if ( !defined( 'MEDIAWIKI' ) ) die( 1 );

class NamedResourceLoader {
	// The list of mwEmbed core components that make up the base mwEmbed class
	private static $coreComponentsList = array();

	// The list of mwEmbed modules that are enabled
	private static $moduleList = array();

	// Stores the contents of the combined loader.js files
	private static $combinedLoadersJs = '';

	// Reg Exp that supports extracting classes from loaders
	private static $classReplaceExp = '/mw\.addResourcePaths\s*\(\s*{(.*)}\s*\)\s*[\;\n]/siU';

	// Flag to specify if the javascript resource paths have been loaded.
	private static $classesLoaded = false;

	// The current directory context. Used in loading javascript modules outside of the mwEmbed folder
	private static $directoryContext = '';

	// Stores resource parent moduleName
	private static $resourceParentModuleName = array();

	// The current module name used for callback functions in regular expressions
	private static $currentModuleName = '';

	/**
	 * Get the javascript resource paths from javascript files
	 */
	public static function loadResourcePaths(){
		global $wgMwEmbedDirectory, $wgExtensionJavascriptModules, $wgMwEmbedEnabledModules,
		$wgResourceLoaderNamedPaths, $wgExtensionMessagesFiles, $IP;

		// Only run once
		if( self::$classesLoaded ) {
			return true;
		}
		self::$classesLoaded = true;

		// Start the profiler if running
		$fname = 'NamedResourceLoader::loadResourcePaths';
		wfProfileIn( $fname );


		$mwEmbedAbsolutePath = ( $wgMwEmbedDirectory == '' ) ? $IP : $IP .'/' .$wgMwEmbedDirectory;
		// Add the mwEmbed localizations
		$wgExtensionMessagesFiles[ 'mwEmbed' ] = $mwEmbedAbsolutePath . '/mwEmbed.i18n.php';

		// Load javascript classes from mwEmbed.js
		if ( !is_file( $mwEmbedAbsolutePath . '/loader.js' ) ) {
			// throw error no mwEmbed found
			throw new MWException( "mwEmbed loader.js missing check \$wgMwEmbedDirectory path\n" );
			return false;
		}

		// Process the mwEmbed loader file:
		$fileContent = file_get_contents( $mwEmbedAbsolutePath . '/loader.js' );
		self::$directoryContext = $wgMwEmbedDirectory;
		self::proccessLoaderContent( $fileContent , 'mwEmbed' );

		// Get the list of core component into self::$coreComponentsList
		preg_replace_callback(
			'/mwCoreComponentList\s*\=\s*\[(.*)\]/siU',
			'NamedResourceLoader::preg_buildComponentList',
			$fileContent
		);

		// Check if we should load module list from mwEmbed loader.js
		if( $wgMwEmbedEnabledModules ) {
			// Get the list of enabled modules into $moduleList
			self::validateModuleList( $wgMwEmbedEnabledModules );
		}

		// Change to the root mediawiki directory ( loader.js paths are relative to root mediawiki directory )
		// ( helpful for when running maintenance scripts )
		if( defined( 'DO_MAINTENANCE' ) ) {
			$initialPath = getcwd();
			chdir( $IP );
		}

		// Get all the classes from the enabled mwEmbed modules folder
		foreach( self::$moduleList as $na => $moduleName ) {
			$relativeSlash = ( $wgMwEmbedDirectory == '' )? '' : '/';
			$modulePath = $wgMwEmbedDirectory . $relativeSlash . 'modules/' . $moduleName;
			self::proccessModulePath( $moduleName, $modulePath );
		}

		// Get all the extension loader paths registered mwEmbed modules
		foreach( $wgExtensionJavascriptModules as $moduleName => $modulePath ){
			self::proccessModulePath( $moduleName, $modulePath );
		}

		if( defined( 'DO_MAINTENANCE' ) ) {
			chdir( $initialPath );
		}

		wfProfileOut( $fname );
	}
	/**
	 * Process a loader path, passes off to proccessLoaderContent
	 *
	 * @param String $moduleName Name of module to be processed
	 * @param String $modulePath Path to module to be processed
	 */
	private static function proccessModulePath( $moduleName, $modulePath ){
		global $wgExtensionMessagesFiles;

		// Get the module name
		$modulePathComponents = explode('/', $modulePath );
		$moduleName = end( $modulePathComponents );

		// Set the directory context for relative js/css paths
		self::$directoryContext = $modulePath;

		// Check for the loader.js
		if( !is_file( $modulePath . '/loader.js' ) ){
			throw new MWException( "Javascript Module $moduleName missing loader.js file\n" );
			return false;
		}

		$fileContent = file_get_contents( $modulePath . '/loader.js');
		self::proccessLoaderContent( $fileContent, $moduleName );

		$i18nPath = realpath( $modulePath . '/' . $moduleName . '.i18n.php' );

		// Add the module localization file if present:
		if( is_file( $i18nPath ) ) {
			$wgExtensionMessagesFiles[ $moduleName ] = $i18nPath;
		} else {
			// Module has no message file
		}
	}

	/**
	 * Process loader content
	 *
	 * parses the loader files and adds
	 *
	 * @param String $fileContent content of loader.js file
	 */
	private static function proccessLoaderContent( & $fileContent , $moduleName){
		// Add the mwEmbed loader js to its global collector:
		self::$combinedLoadersJs .= $fileContent;

		// Is there a way to pass arguments in preg_replace_callback ?
		self::$currentModuleName = $moduleName;

		// Run the replace callback:
		preg_replace_callback(
			self::$classReplaceExp,
			'NamedResourceLoader::preg_classPathLoader',
			$fileContent
		);
	}
	/**
	 * Get the language file javascript
	 * @param String $languageJs The language file javascript
	 */
	public static function getLanguageJs( $langKey = 'en' ){
		global $wgMwEmbedDirectory;
		$path = $wgMwEmbedDirectory . '/languages/classes/Language' . ucfirst( $langKey ) . '.js';
		if( is_file( $path ) ){
			$languageJs = file_get_contents( $path );
			return $languageJs;
		}
		return '';
	}

	/**
	 * Get the combined loader javascript
	 *
	 * @return the combined loader jss
	 */
	public static function getCombinedLoaderJs(){
		self::loadResourcePaths();
		return self::$combinedLoadersJs;
	}

	/**
	 * Get the list of enabled modules
	 */
	public static function getModuleList(){
		self::loadResourcePaths();
		return self::$moduleList;
	}

	/**
	* Get the list of enabled components
	*/
	public static function getComponentsList(){
		self::loadResourcePaths();
		return self::$coreComponentsList;
	}

	/**
	 * Build a list of components to be included with mwEmbed
	 */
	private static function preg_buildComponentList( $jsvar ){
		if(! isset( $jsvar[1] )){
			return false;
		}
		$componentSet = explode(',', $jsvar[1] );
		foreach( $componentSet as $na => $componentName ) {
			$componentName = str_replace( array( '../', '\'', '"'), '', trim( $componentName ));
			// Add the component to the $coreComponentsList
			if( trim( $componentName ) != '' ) {
				array_push( self::$coreComponentsList, trim( $componentName ) );
			}
		}
	}

	/**
	 * Build the list of modules from the mwEnabledModuleList replace callback
	 * @param String $moduleSet array of modules to be validated
	 */
	private static function validateModuleList( $moduleSet ){
		global $IP, $wgMwEmbedDirectory;

		$mwEmbedAbsolutePath = ( $wgMwEmbedDirectory == '' )? $IP: $IP .'/' .$wgMwEmbedDirectory;

		foreach( $moduleSet as $na => $moduleName ) {
			// Skip empty module names
			if(trim( $moduleName ) == '' ){
				continue;
			}
			$moduleName = str_replace( array( '../', '\'', '"'), '', trim( $moduleName ));
			// Check if there is there are module loader files
			if( is_file( $mwEmbedAbsolutePath . '/modules/' . $moduleName . '/loader.js' )){
				array_push( self::$moduleList, $moduleName );
			} else {
				// Not valid module ( missing loader.js )
				throw new MWException( "Module: $moduleName missing loader.js \n" );
			}
		}
	}

	/**
	 * Adds javascript autoloader resource names and paths
	 * to $wgResourceLoaderNamedPaths global
	 *
	 * @param string $jvar Json string with resource name list
	 */
	private static function preg_classPathLoader( $jsvar ) {
		global $wgResourceLoaderNamedPaths;
		if ( !isset( $jsvar[1] ) ) {
			return false;
		}

		$jClassSet = FormatJson::decode( '{' . $jsvar[1] . '}', true );
		// Check for null json decode:
		if( $jClassSet == NULL ){
			throw new MWException( "Error could not decode javascript resource list for module: " + self::$currentModuleName + "\n" );
			return false;
		}

		foreach ( $jClassSet as $resourceName => $classPath ) {
			// Strip $ from resource (as they are stripped on URL request parameter input)
			$resourceName = str_replace( '$', '', $resourceName );
			$classPath = ( self::$directoryContext == '' )? $classPath : self::$directoryContext . '/' . $classPath;

			// Throw an error if we already have defined this class:
			// This prevents a module from registering a shared class
			// or multiple modules using the same className
			if( isset( $wgResourceLoaderNamedPaths[ $resourceName ] ) ){

				// Presently extensions don't register were the named path parent module
				// so we just have a general extension error.
				$setInModuleError = ( self::$resourceParentModuleName [ $resourceName ] )
					? " set in module: " . self::$resourceParentModuleName [ $resourceName ]
					: " set in an extension ";

				throw new MWException( "Error resource $resourceName already $setInModuleError \n" );
			}

			// Else update the global $wgResourceLoaderNamedPaths ( all scriptloader named paths )
			$wgResourceLoaderNamedPaths[ $resourceName ] = $classPath;
			// Register the parent module ( javascript module specific )
			self::$resourceParentModuleName [ $resourceName ] = self::$currentModuleName ;
		}
	}
	/**
	* Return the module name for a given resource or false if not found
	* @param $resourceName resource to get the module for
	*/
	public static function getModuleNameForResource( $resourceName ){
		if( isset( self::$resourceParentModuleName [ $resourceName ] ) ){
			return self::$resourceParentModuleName [ $resourceName ];
		} else {
			return false;
		}
	}
}

