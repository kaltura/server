<?php
/**
 * @package Core
 * @subpackage model.data
 */
class KalturaAccessControlScope extends KalturaObject
{
	/**
	 * @var string
	 */
	public $ip;
	
	/**
	 * @var ks
	 */
	public $ks;
	
	/**
	 * @var string
	 */
	public $userAgent;
	
	/**
	 * Indicates what contexts should be tested. No contexts means any context.
	 * 
	 * @var KalturaAccessControlContextTypeHolderArray
	 */
	public $contexts;

	private static $mapBetweenObjects = array
	(
		'ip',
		'ks',
		'userAgent',
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