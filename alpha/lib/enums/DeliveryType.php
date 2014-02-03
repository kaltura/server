<?php
/**
 * @package Core
 * @subpackage model.enum
 * @_!! Name???
 */ 
interface DeliveryType extends BaseEnum
{
		// TODO @_!! Edit the plugin. f.i. VlcPlugin
	const AKAMAI_HTTP = 0;
	const AKAMAI_RTMP = 1;
	const AKAMAI_RTSP = 2;
	const AKAMAI_HD = 3;
	const AKAMAI_HLS_DIRECT = 4;		// returns a manifest with index_0_av.m3u8
	const AKAMAI_HLS_MANIFEST = 5;		// redirects to /i.../master.m3u8
	const AKAMAI_HDS = 6;
	const AKAMAI_SS = 6;
	
	const LEVEL3_HTTP = 6;
	const LEVEL3_RTMP = 6;
	const LEVEL3_HLS = 6;
	
	const LIMELIGHT_HTTP = 6;
	const LIMELIGHT_RTMP = 6;
	
	const UPLYNK_HTTP = 6;
	const UPLYNK_RTMP = 6;
	
	const EDGECAST_HTTP = 6;
	const EDGECAST_RTMP = 6;
	
	const MIRRORIMAGE_HTTP = 6;		// seems unused
	const MIRRORIMAGE_RTMP = 6;		// seems unused
	
	const KONTIKI_HTTP = 6;
	
	const GENERIC_HTTP = 6;			// formerly kFmsUrlManager, has a pattern URL with tokens (e.g. hds_pattern = '/hds-vod/{url}.f4m')
	const GENERIC_HLS = 6;
	const GENERIC_HDS = 6;
	
	const LIVE_HDS = 6;
	const LIVE_HLS_MANIFEST = 6;
	const LIVE_RTMP = 6;
}


/**
 * @package plugins.vlc
 * @subpackage lib
 */
// class VlcKalturaDeliveryType implements IKalturaPluginEnum, DeliveryType
// {
// 	const ASD = 'asd';

// 	public static function getAdditionalValues()
// 	{
// 		return array(
// 				'asd' => self::ASD
// 		);
// 	}

// 	/**
// 	 * @return array
// 	 */
// 	public static function getAdditionalDescriptions()
// 	{
// 		return array();
// 	}
// }

