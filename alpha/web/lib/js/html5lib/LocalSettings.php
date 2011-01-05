<?php 
/**
 * This file store all of mwEmbed local configuration ( in a default svn check out this file is empty )
 * 
 * See includes/DefaultSettings.php for a configuration options
 */

// Get kaltura configuration file
require_once( realpath( '../../../../config/' ) . '/kConf.php' );

$kConf = new kConf();

// The default Kaltura service url:
$wgKalturaServiceUrl = 'http://' . $kConf->get('www_host');

// Default Kaltura CDN url: 
$wgKalturaCDNUrl = 'http://' . $kConf->get('cdn_host');

// Default Kaltura service url:
$wgKalturaServiceBase = '/api_v3/index.php?';

?>