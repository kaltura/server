<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */

class KalturaVendorCredit extends KalturaBaseVendorCredit
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
	 *  @var int
	 */
	public $overageCredit;

	/**
	 *  @var int
	 */
	public $addOn;

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	private static $map_between_objects = array (
		'credit',
		'fromDate',
		'overageCredit',
		'addOn'
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
	
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('fromDate');
		$this->validatePropertyNotNull('credit');

		if(isset($this->overageCredit) && $this->overageCredit < 0)
		{
			throw new KalturaAPIException(KalturaReachErrors::OVERAGE_CREDIT_CANNOT_BE_NEGATIVE);
		}

		parent::validateForInsert($propertiesToSkip);
	}
	
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('fromDate');
		$this->validatePropertyNotNull('credit');

		if (isset($this->overageCredit) && $this->overageCredit < 0)
		{
			throw new KalturaAPIException(KalturaReachErrors::OVERAGE_CREDIT_CANNOT_BE_NEGATIVE);
		}
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}

	public function hasObjectChanged($sourceObject)
	{
		if(parent::hasObjectChanged($sourceObject))
		{
			return true;
		}
		
		/* @var $sourceObject kVendorCredit */
		if( ($this->credit && $this->credit != $sourceObject->getCredit())
			|| ($this->fromDate && $this->fromDate != $sourceObject->getFromDate())
			|| ($this->overageCredit && $this->overageCredit != $sourceObject->getOverageCredit()))
		{
			return true;
		}
		return false;
	}

	/**
	 * @return string
	 */
	protected function getMatchingCoreClassName()
	{
		return 'kVendorCredit';
	}
}
