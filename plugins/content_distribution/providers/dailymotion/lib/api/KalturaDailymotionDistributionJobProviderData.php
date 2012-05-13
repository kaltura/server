<?php
/**
 * @package plugins.dailymotionDistribution
 * @subpackage api.objects
 */
class KalturaDailymotionDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{
	/**
	 * @var string
	 */
	public $videoAssetFilePath;

	/**
	 * @var string
	 */
	public $accessControlGeoBlockingOperation;

	/**
	 * @var string
	 */
	public $accessControlGeoBlockingCountryList;
	
	
	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
	    parent::__construct($distributionJobData);
	    
		if(!$distributionJobData)
			return;
			
		if(!($distributionJobData->distributionProfile instanceof KalturaDailymotionDistributionProfile))
			return;
			
		$flavorAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->flavorAssetIds));
		if(count($flavorAssets)) // if we have specific flavor assets for this distribution, grab the first one
			$flavorAsset = reset($flavorAssets);
		else // take the source asset
			$flavorAsset = assetPeer::retrieveOriginalReadyByEntryId($distributionJobData->entryDistribution->entryId);
		
		if($flavorAsset) 
		{
			$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			$this->videoAssetFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey, false);
		}

		// look for krule with action block and condition of country
		$entry = entryPeer::retrieveByPK($distributionJobData->entryDistribution->entryId);
		if ($entry->getAccessControl())
			$this->setGeoBlocking($entry->getAccessControl());
	}


	
	private static $map_between_objects = array
	(
		"videoAssetFilePath",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	/**
	 * @return string $videoAssetFilePath
	 */
	public function getVideoAssetFilePath()
	{
		return $this->videoAssetFilePath;
	}

	/**
	 * @param string $videoAssetFilePath
	 */
	public function setVideoAssetFilePath($videoAssetFilePath)
	{
		$this->videoAssetFilePath = $videoAssetFilePath;
	}

	protected function setGeoBlocking(accessControl $accessControl)
	{
		$rules = $accessControl->getRulesArray();
		foreach($rules as $rule)
		{
			$hasBlockAction = false;
			/* @var $rule kRule */
			foreach($rule->getActions() as $action)
			{
				/* @var $action kAccessControlAction */
				if($action->getType() == accessControlActionType::BLOCK)
				{
					$hasBlockAction = true;
					break;
				}
			}

			if (!$hasBlockAction)
				continue;

			foreach($rule->getConditions() as $condition)
			{
				if ($condition instanceof kCountryCondition)
				{
					/* @var $condition kCountryCondition */
					$this->accessControlGeoBlockingCountryList = implode(',', $condition->getStringValues());
					if ($condition->getNot() === true)
						$this->accessControlGeoBlockingOperation = 'allow';
					else
						$this->accessControlGeoBlockingOperation = 'deny';

					break;
				}
			}
		}
	}
}
