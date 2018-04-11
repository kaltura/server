<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */

class KalturaTimeRangeVendorCredit extends KalturaVendorCredit
{
	/**
	 *  @var time
	 */
	public $toDate;

	private static $map_between_objects = array (
		'toDate',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
 	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
 	 */
	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new kTimeRangeVendorCredit();
		}
		
		return parent::toObject($dbObject, $propsToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull("toDate");
		
		parent::validateForInsert($propertiesToSkip);

		if ($this->fromDate > $this->toDate)
			throw new KalturaAPIException(KalturaReachErrors::INVALID_CREDIT_DATES , $this->fromDate, $this->toDate);
	}
}
