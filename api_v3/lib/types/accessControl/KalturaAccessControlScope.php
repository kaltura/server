<?php
/**
 * @package Core
 * @subpackage model.data
 */
class KalturaAccessControlScope extends KalturaObject
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
	 * @var time
	 */
	public $time;
	
	/**
	 * Indicates what contexts should be tested. No contexts means any context.
	 * 
	 * @var KalturaAccessControlContextTypeHolderArray
	 */
	public $contexts;
	
	/**
	 * Array of hashes to pass to the access control profile scope
	 * @var KalturaKeyValueArray
	 */
	public $hashes;

	private static $mapBetweenObjects = array
	(
		'referrer',
		'ip',
		'ks',
		'userAgent',
		'time',
		'contexts',
		'hashes',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}
