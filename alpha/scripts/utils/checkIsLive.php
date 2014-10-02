<?php

/**
 * Description:	Check if the given entry ID at the given host is live
 * Usage:		php checkIsLive.php <entryId> <host>
 */

require_once(__DIR__ . '/../bootstrap.php');

if ( count($argv) < 3 )
{
	Kaltura::err( "checkIsLive: Wrong number of input args." );
	exit(1);
}

$entryId = $argv[1];
$host = $argv[2];

// Note: "/p/0" is good enough for checking if the url is live
$url = "http://$host/p/0/playManifest/entryId/$entryId/format/applehttp/protocol/http/b.m3u8/?rnd=" . time();

$monitor = new DeliveryProfileLiveAppleHttp();
$isLive = $monitor->checkIsLive($url);

KalturaLog::info( "checkIsLive $entryId $host = " . ($isLive ? "true" : "false") );		
