<?php
/**
 * @package plugins.youTubeDistribution
 * @subpackage api.objects
 */
class KalturaYouTubeDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{
	/**
	 * @var string
	 */
	public $videoAssetFilePath;
	
	/**
	 * @var string
	 */
	public $thumbAssetFilePath;
	
	/**
	 * @var string
	 */
	public $sftpDirectory;
	
	/**
	 * @var string
	 */
	public $sftpMetadataFilename;
	
	/**
	 * @var string
	 */
	public $currentPlaylists;

	/**
	 * @var string
	 */
	public $newPlaylists;

	/**
	 * @var string
	 */
	public $submitXml;

	/**
	 * @var string
	 */
	public $updateXml;

	/**
	 * @var string
	 */
	public $deleteXml;

	/**
	 * @var string
	 */
	public $googleClientId;

	/**
	 * @var string
	 */
	public $googleClientSecret;

	/**
	 * @var string
	 */
	public $googleTokenData;

	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
	    parent::__construct($distributionJobData);
	    
		if(!$distributionJobData)
			return;
			
		if(!($distributionJobData->distributionProfile instanceof KalturaYouTubeDistributionProfile))
			return;
		
		$flavorAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->flavorAssetIds));
		if(count($flavorAssets)) // if we have specific flavor assets for this distribution, grab the first one
			$flavorAsset = reset($flavorAssets);
		else // take the source asset
			$flavorAsset = assetPeer::retrieveOriginalReadyByEntryId($distributionJobData->entryDistribution->entryId);
		
		if($flavorAsset) 
		{
			$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			if(kFileSyncUtils::fileSync_exists($syncKey))
			    $this->videoAssetFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey, false);
		}
		
		$thumbAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->thumbAssetIds));
		if(count($thumbAssets))
		{
			$syncKey = reset($thumbAssets)->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			if(kFileSyncUtils::fileSync_exists($syncKey))
			    $this->thumbAssetFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey, false);
		}
		
		$entryDistributionDb = EntryDistributionPeer::retrieveByPK($distributionJobData->entryDistributionId);
		if ($entryDistributionDb)
			$this->currentPlaylists = $entryDistributionDb->getFromCustomData('currentPlaylists');
		else
			KalturaLog::err('Entry distribution ['.$distributionJobData->entryDistributionId.'] not found');  

		if ($distributionJobData->distributionProfile->feedSpecVersion != YouTubeDistributionFeedSpecVersion::VERSION_2)
			return;
			
		if (is_null($this->fieldValues))
			return;
			//23.5.13 this return is a hack because of bad inheritance of kYouTubeDistributionJobProviderData causing some YouTube distribution 
			//batch jobs to not have fieldValues. it can be removed at some point. 
			
		$videoFilePath = $this->videoAssetFilePath;
		$thumbnailFilePath = $this->thumbAssetFilePath;

		$feed = null;
		$fieldValues = unserialize($this->fieldValues);
		if ($distributionJobData instanceof KalturaDistributionSubmitJobData)
		{
			$feed = YouTubeDistributionRightsFeedHelper::initializeDefaultSubmitFeed($distributionJobData->distributionProfile, $fieldValues, $videoFilePath, $thumbnailFilePath);
			$this->submitXml = $feed->getXml();
		}
		elseif ($distributionJobData instanceof KalturaDistributionUpdateJobData)
		{
			$remoteIdHandler = YouTubeDistributionRemoteIdHandler::initialize($distributionJobData->remoteId);
			$feed = YouTubeDistributionRightsFeedHelper::initializeDefaultUpdateFeed($distributionJobData->distributionProfile, $fieldValues, $videoFilePath, $thumbnailFilePath, $remoteIdHandler);
			$this->updateXml = $feed->getXml();
		}
		elseif ($distributionJobData instanceof KalturaDistributionDeleteJobData)
		{
			$remoteIdHandler = YouTubeDistributionRemoteIdHandler::initialize($distributionJobData->remoteId);
			$feed = YouTubeDistributionRightsFeedHelper::initializeDefaultDeleteFeed($distributionJobData->distributionProfile, $fieldValues, $videoFilePath, $thumbnailFilePath, $remoteIdHandler);
			$this->deleteXml = $feed->getXml();
		}

		$this->newPlaylists = isset($fieldValues[KalturaYouTubeDistributionField::PLAYLISTS]) ? $fieldValues[KalturaYouTubeDistributionField::PLAYLISTS] : null;
		if ($feed)
		{
			$this->sftpDirectory = $feed->getDirectoryName();
			$this->sftpMetadataFilename = $feed->getMetadataTempFileName();
		}

		$partnerId = $distributionJobData->distributionProfile->partnerId;
		$distributionProfileId = $distributionJobData->distributionProfile->id;
		$this->loadGoogleConfig($partnerId, $distributionProfileId);
	}
		
	private static $map_between_objects = array
	(
		"videoAssetFilePath",
		"thumbAssetFilePath",
		"sftpDirectory",
		"sftpMetadataFilename",
		"currentPlaylists",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/**
	 * @param $partnerId
	 * @param $distributionProfileId
	 * @return void
	 */
	protected function loadGoogleConfig($partnerId, $distributionProfileId)
	{
		$appConfigId = 'youtubepartner'; // config section for configuration/google_auth.ini
		$tokenSubId = $distributionProfileId;
		$authConfig = kConf::get($appConfigId, 'google_auth', null);

		$this->googleClientId = isset($authConfig['clientId']) ? $authConfig['clientId'] : null;
		$this->googleClientSecret = isset($authConfig['clientSecret']) ? $authConfig['clientSecret'] : null;

		/** @var Partner $partner */
		$partner = PartnerPeer::retrieveByPK($partnerId);

		// try to load based on the sub id,
		// it means that we have a custom auth config for the distribution profile
		$tokenData = $partner->getFromCustomData($appConfigId.'_'.$tokenSubId, 'googleAuth');
		if ($tokenData)
		{
			$this->googleTokenData = json_encode($tokenData);
			return;
		}

		// now try to load base on the app config id
		$tokenData = $partner->getFromCustomData($appConfigId, 'googleAuth');
		if ($tokenData)
		{
			$this->googleTokenData = serialize($tokenData);
			return;
		}
	}
}
