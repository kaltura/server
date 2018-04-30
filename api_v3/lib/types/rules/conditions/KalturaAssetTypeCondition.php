<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAssetTypeCondition extends KalturaCondition
{
	/**
	 * @dynamicType KalturaAssetType
	 * @var string
	 */
	public $assetTypes;

	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = ConditionType::ASSET_TYPE;
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

		if (!is_null($this->assetTypes))
			$dbObject->setAssetTypes(explode(',', $this->assetTypes));

		return $dbObject;
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function doFromObject($dbObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/** @var $dbObject kAssetTypeCondition */
		parent::doFromObject($dbObject, $responseProfile);
		if($this->shouldGet('AssetTypes', $responseProfile))
			$this->assetTypes = implode(',', $dbObject->getAssetTypes());
	}
}
