<?php 
/**
 * This file stores default settings for Kaltura html5 client library "mwEmbed".
 * 
 *  DO NOT MODIFY THIS FILE. Instead modify LocalSettings.php in the parent mwEmbd directory. 
 * 
 */

// The default cache directory
$wgScriptCacheDirectory = realpath( dirname( __FILE__ ) ) . '/cache';

// The absolute or relative path to mwEmbed install folder.
// by default its the entry point minus the entry point name:
$wgMwEmbedPathUrl = str_replace( 
	// List entry points: 
	array( 'mwEmbedFrame.php', 'ResourceLoader.php' ),
	'', 
	$_SERVER['SCRIPT_NAME']
);

// The list of enabled modules all modules listed here will have their loaders included and 
// have their javascript functions available.
// By default we enable every folder the "modules" folder
$wgMwEmbedEnabledModules = array(
	'AddMedia',
	'ClipEdit',
	'EmbedPlayer',
	'ApiProxy',
	'Sequencer',
	'TimedText',
	'SmilPlayer',
	'Playlist',
	'SwarmTransport',
	'SyntaxHighlighter',
	'MiroSubs',
	'PlayerThemer',
	'KalturaSupport',
	'AdSupport',
	'Plymedia'
);

/*********************************************************
 * Default Kaltura Configuration: 
 * TODO move kaltura configuration to KalturaSupport module ( part of ResourceLoader update ) 
 ********************************************************/

// The default Kaltura service url:
$wgKalturaServiceUrl = 'http://www.kaltura.com/';

// Default Kaltura CDN url: 
$wgKalturaCDNUrl = 'http://cdn.kaltura.com';

// Default Kaltura service url:
$wgKalturaServiceBase = '/api_v3/index.php?';

// Default expire time for ui conf api queries in seconds 
$wgKalturaUiConfCacheTime = 600;




/*********************************************************
 * Include local settings override:
 ********************************************************/
$wgLocalSettingsFile = realpath( dirname( __FILE__ ) ) . '/../LocalSettings.php';

if( is_file( $wgLocalSettingsFile ) ){
	require_once( $wgLocalSettingsFile );
}


?>
