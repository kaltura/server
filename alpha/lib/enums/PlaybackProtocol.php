<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface PlaybackProtocol extends BaseEnum
{
	const HTTP = 'http';
	const RTMP = 'rtmp';
	const SILVER_LIGHT = 'sl';
	const APPLE_HTTP = 'applehttp';
	const RTSP = 'rtsp';
	const AUTO = 'auto';
	const HDS = 'hds';
	const HLS = 'hls';	
	const AKAMAI_HDS = 'hdnetworkmanifest';
	const AKAMAI_HD = 'hdnetwork';
	const MPEG_DASH = 'mpegdash';
	const MULTICAST_SL = 'multicast_silverlight';
	const APPLE_HTTP_TO_MC = 'applehttp_to_mc';
	const URL = 'url';
	const DOWNLOAD = 'download';
}