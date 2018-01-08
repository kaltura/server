<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAssetTypeCondition extends KalturaCondition
{
	/**
	 * @var KalturaAssetTypeHolderArray holder for flavor 
	 */
	public $flavorTypes;

	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = ConditionType::FLAVOR_TYPE;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kAssetTypeCondition();

		/** @var $dbObject kAssetTypeCondition */
		$dbObject = parent::toObject($dbObject, $skip);

		if (!is_null($this->flavorTypes))
			$dbObject->setAssetTypes($this->flavorTypes->toObjectsArray());

		return $dbObject;
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function doFromObject($dbObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/** @var $dbObject kAssetTypeCondition */
		parent::doFromObject($dbObject, $responseProfile);
		if($this->shouldGet('flavorTypes', $responseProfile))
			$this->flavorTypes = KalturaAssetTypeHolderArray::fromDbArray($dbObject->getAssetTypes(), $responseProfile);
	}
}
