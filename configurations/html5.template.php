<?php
/**
 * This file store all of mwEmbed local configuration
 * 
 * See includes/DefaultSettings.php for a configuration options
 * 
 */
 
// Kaltura HTML5lib Version
$wgKalturaVersion = basename(getcwd()); // Gets the version by the folder name

$wgKalturaServiceUrl = '@SERVICE_URL@';
// Default Kaltura CDN url:
$wgKalturaCDNUrl = '@SERVICE_URL@';
// Default Stats URL
$wgKalturaStatsServiceUrl = '@SERVICE_URL@';

// Default Asset CDN Path (used in ResouceLoader.php):
$wgCDNAssetPath = $wgKalturaCDNUrl;

// Default Kaltura Cache Path
$wgScriptCacheDirectory = '@APP_DIR@/cache/html5/' . $wgKalturaVersion;

$wgLoadScript = $wgKalturaServiceUrl . '/html5/html5lib/' . $wgKalturaVersion . '/load.php';
$wgResourceLoaderUrl = $wgLoadScript;

// Salt for proxy the user IP address to Kaltura API
$wgKalturaRemoteAddressSalt = '@APP_REMOTE_ADDR_HEADER_SALT@';

$wgKalturaUseAppleAdaptive = false;

// Allow Iframe to connect remote service
$wgKalturaAllowIframeRemoteService = true;

// Set debug for true (testing only)
$wgEnableScriptDebug = false;
