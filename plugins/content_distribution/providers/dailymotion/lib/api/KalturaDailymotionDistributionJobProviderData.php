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
	
	/**
	 * @var KalturaDailymotionDistributionCaptionInfoArray
	 */
	public $captionsInfo;
	
	
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
		if ($entry && $entry->getAccessControl())
			$this->setGeoBlocking($entry->getAccessControl());
			
		$this->addCaptionsData($distributionJobData);
	}


	
	private static $map_between_objects = array
	(
		"videoAssetFilePath",
		"captionsInfo",
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
				if($action->getType() == RuleActionType::BLOCK)
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
	
	private function addCaptionsData(KalturaDistributionJobData $distributionJobData) {
		/* @var $mediaFile KalturaDistributionRemoteMediaFile */
		$assetIdsArray = explode ( ',', $distributionJobData->entryDistribution->assetIds );
		if (empty($assetIdsArray)) return;
		$assets = array ();
		$this->captionsInfo = new KalturaDailymotionDistributionCaptionInfoArray();
		
		foreach ( $assetIdsArray as $assetId ) {
			$asset = assetPeer::retrieveByIdNoFilter( $assetId );
			if (!$asset){
				KalturaLog::err("Asset [$assetId] not found");
				continue;
			}
			if ($asset->getStatus() == asset::ASSET_STATUS_READY) {
				$assets [] = $asset;
			}
			elseif($asset->getStatus()== asset::ASSET_STATUS_DELETED) {
				$captionInfo = new KalturaDailymotionDistributionCaptionInfo ();
				$captionInfo->action = KalturaDailymotionDistributionCaptionAction::DELETE_ACTION;
				$captionInfo->assetId = $assetId;
				//getting the asset's remote id
				foreach ( $distributionJobData->mediaFiles as $mediaFile ) {
					if ($mediaFile->assetId == $assetId) {
						$captionInfo->remoteId = $mediaFile->remoteId;
						$this->captionsInfo [] = $captionInfo;
						break;
					}
				}
			}
			else{
				KalturaLog::err("Asset [$assetId] has status [".$asset->getStatus()."]. not added to provider data");
			}
		}
		
		foreach ( $assets as $asset ) {
			$assetType = $asset->getType ();
			switch ($assetType) {
				case CaptionPlugin::getAssetTypeCoreValue ( CaptionAssetType::CAPTION ):
					$captionInfo = $this->getCaptionInfo($asset, $distributionJobData);
					if ($captionInfo)
					{
						$captionInfo->language = $this->getLanguageCode($asset->getLanguage());
						$captionInfo->format = $this->getCaptionFormat($asset);
						if ($captionInfo->language)
							$this->captionsInfo [] = $captionInfo;
						else
							KalturaLog::err('The caption ['.$asset->getId().'] has unrecognized language ['.$asset->getLanguage().']');
					}

					break;
				case AttachmentPlugin::getAssetTypeCoreValue ( AttachmentAssetType::ATTACHMENT ) :
					$captionInfo = $this->getCaptionInfo($asset, $distributionJobData);
					if ($captionInfo)
					{
						//language code should be set in the attachments title
						$captionInfo->language = $asset->getTitle();
						$captionInfo->format = $this->getCaptionFormat($asset);
						$languageCodeReflector = KalturaTypeReflectorCacher::get('KalturaLanguageCode');
						//check if the language code exists
						if ($languageCodeReflector && $languageCodeReflector->getConstantName($captionInfo->language))
							$this->captionsInfo [] = $captionInfo;
						else
							KalturaLog::err('The attachment [' . $asset->getId() . '] has unrecognized language [' . $asset->getTitle() . ']');
					}

					break;
			}
		}
	}
	
	private function getLanguageCode($language = null){
		$languageReflector = KalturaTypeReflectorCacher::get('KalturaLanguage');
		$languageCodeReflector = KalturaTypeReflectorCacher::get('KalturaLanguageCode');
		if($languageReflector && $languageCodeReflector)
		{
			$languageCode = $languageReflector->getConstantName($language);
			if($languageCode)
				return $languageCodeReflector->getConstantValue($languageCode);
		}
		return null;
	}
	
	private function getCaptionInfo($asset, KalturaDistributionJobData $distributionJobData) {
		$captionInfo = new KalturaDailymotionDistributionCaptionInfo ();
		$captionInfo->assetId = $asset->getId();
		$captionInfo->version = $asset->getVersion();
		/* @var $mediaFile KalturaDistributionRemoteMediaFile */
		$distributed = false;
		foreach ( $distributionJobData->mediaFiles as $mediaFile ) {
			if ($mediaFile->assetId == $asset->getId ()) {
				$distributed = true;
				if ($asset->getVersion () > $mediaFile->version) {
					$captionInfo->action = KalturaDailymotionDistributionCaptionAction::UPDATE_ACTION;
				}
				break;
			}
		}
		if (! $distributed)
			$captionInfo->action = KalturaDailymotionDistributionCaptionAction::SUBMIT_ACTION;
		elseif ($captionInfo->action != KalturaDailymotionDistributionCaptionAction::UPDATE_ACTION) {
			return;
		}
		return $captionInfo;
	}
	
	private function getCaptionFormat($asset){		
		if ($asset instanceof  AttachmentAsset && ($asset->getPartnerDescription() == 'smpte-tt'))
			return KalturaDailymotionDistributionCaptionFormat::TT;
			
		if ($asset instanceof  captionAsset){
			switch ($asset->getContainerFormat()){
				case KalturaCaptionType::SRT:
					return KalturaDailymotionDistributionCaptionFormat::SRT;
				case KalturaCaptionType::DFXP:
					return KalturaDailymotionDistributionCaptionFormat::TT;	
			}
		}
		KalturaLog::err("caption [".$asset->getId()."] has an unknow format.");
		return null;
	}
}
