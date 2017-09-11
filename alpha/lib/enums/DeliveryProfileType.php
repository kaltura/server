<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface DeliveryProfileType extends BaseEnum
{
	const APPLE_HTTP = 1;
	const HDS = 3;
	const HTTP = 4;
	const RTMP = 5;
	const RTSP = 6;
	const SILVER_LIGHT = 7;
	
	const AKAMAI_HLS_DIRECT = 10;
	const AKAMAI_HLS_MANIFEST = 11;
	const AKAMAI_HD = 12;
	const AKAMAI_HDS = 13;
	const AKAMAI_HTTP = 14;
	const AKAMAI_RTMP = 15;
	const AKAMAI_RTSP = 16;
	const AKAMAI_SS = 17;
	
	const GENERIC_HLS = 21;
	const GENERIC_HDS = 23;
	const GENERIC_HTTP = 24;
	const GENERIC_HLS_MANIFEST = 25;
	const GENERIC_HDS_MANIFEST = 26;
	const GENERIC_SS = 27;
	const GENERIC_RTMP = 28;
	
	const LEVEL3_HLS = 31;
	const LEVEL3_HTTP = 34;
	const LEVEL3_RTMP = 35;
	
	const LIMELIGHT_HTTP = 44;
	const LIMELIGHT_RTMP = 45;
	
	const LOCAL_PATH_APPLE_HTTP = 51;
	const LOCAL_PATH_HDS = 53;
	const LOCAL_PATH_HTTP = 54;
	const LOCAL_PATH_RTMP = 55;
	
	const VOD_PACKAGER_HLS = 61;
	const VOD_PACKAGER_HDS = 63;
	const VOD_PACKAGER_MSS = 67;
	const VOD_PACKAGER_DASH = 68;
	const VOD_PACKAGER_HLS_MANIFEST = 69;
	
	const LIVE_HLS = 1001;
	const LIVE_HDS = 1002;
	const LIVE_DASH = 1003;
	const LIVE_RTMP = 1005;
	const LIVE_HLS_TO_MULTICAST = 1006;
	const LIVE_PACKAGER_HLS = 1007;
	const LIVE_PACKAGER_HDS = 1008;
	const LIVE_PACKAGER_DASH = 1009;
	const LIVE_PACKAGER_MSS = 1010;
	
	const LIVE_AKAMAI_HDS = 1013;
}
