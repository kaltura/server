<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */

class KalturaVendorCredit extends KalturaObject
{
	/**
	 *  @var int
	 */
	public $credit;
	
	/**
	 *  @var time
	 */
	public $fromDate;
	
	/**
	 *  @var KalturaNullableBoolean
	 */
	public $allowOverage;
	
	/**
	 *  @var int
	 */
	public $overageCredit;

	/**
	 *  @var time
	 */
	public $lastSyncTime;

	/**
	 *  @var int
	 */
	public $syncedCredit;

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	private static $map_between_objects = array (
		'credit',
		'fromDate',
		'allowOverage',
		'overageCredit',
		'lastSyncTime',
		'syncedCredit'
	);

	/* (non-PHPdoc)
 	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
 	 */
	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new kVendorCredit();
		}
		
		return parent::toObject($dbObject, $propsToSkip);
	}
}