<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaEntryContextDataResult extends KalturaObject
{
	/**
	 * @var bool
	 * @deprecated
	 */
	public $isSiteRestricted = false;
	
	/**
	 * @var bool
	 * @deprecated
	 */
	public $isCountryRestricted = false;
	
	/**
	 * @var bool
	 * @deprecated
	 */
	public $isSessionRestricted = false;
	
	/**
	 * @var bool
	 * @deprecated
	 */
	public $isIpAddressRestricted = false;
	
	/**
	 * @var bool
	 * @deprecated
	 */
	public $isUserAgentRestricted = false;
	
	/**
	 * @var int
	 * @deprecated
	 */
	public $previewLength = -1;
	
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
	 * @var string
	 */
	public $storageProfilesXML;
	
	/**
	 * Array of messages as received from the access control rules that invalidated
	 * @var KalturaStringArray
	 */
	public $accessControlMessages;
	
	/**
	 * Array of actions as received from the access control rules that invalidated
	 * @var KalturaAccessControlActionArray
	 */
	public $accessControlActions;

	private static $mapBetweenObjects = array
	(
		'isSiteRestricted',
		'isCountryRestricted',
		'isSessionRestricted',
		'isIpAddressRestricted',
		'isUserAgentRestricted',
		'previewLength',
		'accessControlMessages',
		'accessControlActions',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}