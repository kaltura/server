<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaEntryContextDataResult extends KalturaObject
{
	/**
	 * @var bool
	 */
	public $isSiteRestricted;
	
	/**
	 * @var bool
	 */
	public $isCountryRestricted;
	
	/**
	 * @var bool
	 */
	public $isSessionRestricted;
	
	/**
	 * @var bool
	 */
	public $isIpAddressRestricted;
	
	/**
	 * @var bool
	 */
	public $isUserAgentRestricted;
	
	/**
	 * @var int
	 */
	public $previewLength;
	
	/**
	 * @var bool
	 */
	public $isScheduledNow;
	
	/**
	 * @var bool
	 */
	public $isAdmin;
	
	/**
	 * http/rtmp/hdnetwork
	 * @var string
	 */
	public $streamerType;
	
	/**
	 * http/https, rtmp/rtmpe
	 * @var string
	 */
	public $mediaProtocol;
	
	/**
	 *
	 * @var string
	 */
	public $storageProfileIds;
}