<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kAssetTypeCondition extends kCondition
{
	/**
	 * @var array
	 */
	private $assetTypes;
	
	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(ConditionType::ASSET_TYPE);
		parent::__construct($not);
	}

	/**
	 * @param kScope $scope
	 * @return bool
	 */
	protected function internalFulfilled(kScope $scope)
	{
		//get flavor from scope
		$asset = ($scope instanceof  accessControlScope) ? $scope->getAsset() : null;
		$intAssetTypes = $this->getIntAssetTypes($this->assetTypes);
		if ($asset)
			return in_array($asset->getType(), $intAssetTypes);
		return false;
	}

	/**
	 * @param array $assetTypes
	 */
	public function setAssetTypes($assetTypes)
	{
		$this->assetTypes = $assetTypes;
	}

	/**
	 * @return array
	 */
	public function getAssetTypes()
	{
		return $this->assetTypes;
	}

	protected function getIntAssetTypes($assetTypes)
	{
		$intAssetTypes = array();
		foreach ($assetTypes as $type)
		{
			if(is_numeric($type))
			{
				$intAssetTypes[] = $type;
			}
			else
			{
				$intAssetTypes[] = kPluginableEnumsManager::apiToCore('assetType', $type);
			}
		}
		return $intAssetTypes;
	}

}
