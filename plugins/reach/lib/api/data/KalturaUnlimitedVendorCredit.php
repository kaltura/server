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

	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull(array("credit", "fromDate"));
		parent::validateForInsert($propertiesToSkip);

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
}