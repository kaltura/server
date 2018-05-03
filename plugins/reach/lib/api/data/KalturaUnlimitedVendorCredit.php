<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */

class KalturaUnlimitedVendorCredit extends KalturaBaseVendorCredit
{
	/**
	 *  @var int
	 *  @readonly
	 */
	public $credit = ReachProfileCreditValues::UNLIMITED_CREDIT;

	/**
	 *  @var time
	 */
	public $fromDate;

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	private static $map_between_objects = array (
		'fromDate','credit',
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert()
	*/
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull("fromDate");
		parent::validateForInsert(array("credit"));

	}
	
	/* (non-PHPdoc)
 	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
 	 */
	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new kUnlimitedVendorCredit();
		}

		return parent::toObject($dbObject, $propsToSkip);
	}
	
	public function hasObjectChanged($sourceObject)
	{
		if(parent::hasObjectChanged($sourceObject))
			return true;
		
		/* @var $sourceObject kUnlimitedVendorCredit */
		if( ($this->credit && $this->credit != $sourceObject->getCredit())
			|| ($this->fromDate && $this->fromDate != $sourceObject->getFromDate())
		)
			true;
		
		return false;
	}
}
