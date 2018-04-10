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
		$this->validatePropertyNotNull("frequency"));
		
		parent::validateForInsert($propertiesToSkip);
	}
}
