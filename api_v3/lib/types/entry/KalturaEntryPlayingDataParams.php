<?php
/**
 * Object which contains contextual entry-related data.
 * @package api
 * @subpackage objects
 */
class KalturaEntryPlayingDataParams extends KalturaObject
{
	/**
	 * The tags of the flavors that should be used for playback.
	 * @var KalturaFlavorAssetArray
	 */
	public $flavors;
	
	/**
	 * Playback streamer type: RTMP, HTTP, appleHttps, rtsp, sl.
	 * @var string
	 */
	public $streamerType;

	/**
	 * @var KalturaDeliveryProfile
	 */
	public $deliverProfile;
	
}