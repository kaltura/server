<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */

class KalturaReoccurringVendorCredit extends KalturaTimeRangeVendorCredit
{
	/**
	 * @var KalturaVendorCreditRecurrenceFrequency
	 */
	public $frequency;

	private static $map_between_objects = array (
		'frequency',
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
			$dbObject = new kReoccurringVendorCredit();
		}
		return parent::toObject($dbObject, $propsToSkip);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert()
	 */	 
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull("frequency");
		
		parent::validateForInsert($propertiesToSkip);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		if (isset($this->frequency))
		{
			$this->validatePropertyNotNull('frequency');
		}
		parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
	
	public function hasObjectChanged($sourceObject)
	{
		if(parent::hasObjectChanged($sourceObject))
			return true;
		
		/* @var $sourceObject kReoccurringVendorCredit */
		if($this->frequency && $this->frequency != $sourceObject->getFrequency())
			return true;
		
		return false;
	}

	/**
	 * @param $object
	 * @return bool
	 */
	public function isMatchingCoreClass($object)
	{
		if (!$object)
		{
			return false;
		}
		return get_class($object) == 'kReoccurringVendorCredit';
	}
}
