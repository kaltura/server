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
	
	const HTTP_PROTOCOLS = array(
		'http',
		'https',
	);

	const HTTP_FORMATS = array(
		self::HTTP,
		self::SILVER_LIGHT,
		self::APPLE_HTTP,
		self::HDS,
		self::HLS,	
		self::AKAMAI_HDS,
		self::AKAMAI_HD,
		self::MPEG_DASH,
	);
}