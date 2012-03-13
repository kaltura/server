<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaEntryContextDataParams extends KalturaObject
{
	/**
	 * URL to be used to test domain conditions.
	 * @var string
	 */
	public $referrer;
	
	/**
	 * IP to be used to test geographic location conditions.
	 * @var string
	 */
	public $ip;
	
	/**
	 * Kaltura session to be used to test session and user conditions.
	 * @var string
	 */
	public $ks;
	
	/**
	 * Browser or client application to be used to test agent conditions.
	 * @var string
	 */
	public $userAgent;
	
	/**
	 * Unix timestamp (In seconds) to be used to test entry scheduling, keep null to use now.
	 * @var int
	 */
	public $time;
	
	/**
	 * Indicates what contexts should be tested. No contexts means any context.
	 * 
	 * @var KalturaAccessControlContextTypeHolderArray
	 */
	public $contexts;
	
	/**
	 * @var string
	 */
	public $flavorAssetId;
	
	/**
	 * @var string
	 */
	public $streamerType;
	
	/**
	 * @var string
	 */
	public $mediaProtocol;

	private static $mapBetweenObjects = array
	(
		'referrer',
		'ip',
		'ks',
		'userAgent',
		'time',
		'contexts',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}