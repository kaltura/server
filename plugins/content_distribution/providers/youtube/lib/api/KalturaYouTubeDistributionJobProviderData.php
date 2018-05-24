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
	public $captionAssetIds;
	
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

	/**
	 * @var string
	 */
	public $captionsCsvMap;

	/**
	 * @var string
	 */
	public $submitCsvMap;

	/**
	 * @var string
	 */
	public $updateCsvMap;

	/**
	 * @var string
	 */
	public $deleteVideoIds;

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
		
		//Add caption Asset id's
		$this->captionAssetIds = $distributionJobData->entryDistribution->assetIds;
		
		$entryDistributionDb = EntryDistributionPeer::retrieveByPK($distributionJobData->entryDistributionId);
		if ($entryDistributionDb)
			$this->currentPlaylists = $entryDistributionDb->getFromCustomData('currentPlaylists');
		else
			KalturaLog::err('Entry distribution ['.$distributionJobData->entryDistributionId.'] not found');  

		if ($distributionJobData->distributionProfile->feedSpecVersion == YouTubeDistributionFeedSpecVersion::VERSION_1)
			return;
			
		if (is_null($this->fieldValues))
			return;
			//23.5.13 this return is a hack because of bad inheritance of kYouTubeDistributionJobProviderData causing some YouTube distribution 
			//batch jobs to not have fieldValues. it can be removed at some point. 
			
		$videoFilePath = $this->videoAssetFilePath;
		$thumbnailFilePath = $this->thumbAssetFilePath;
		$captionAssetIds = $this->captionAssetIds;

		$feed = null;
		$fieldValues = unserialize($this->fieldValues);
		if ($distributionJobData instanceof KalturaDistributionSubmitJobData)
		{
			if ($distributionJobData->distributionProfile->feedSpecVersion == YouTubeDistributionFeedSpecVersion::VERSION_2)
			{
				$feed = YouTubeDistributionRightsFeedHelper::initializeDefaultSubmitFeed($distributionJobData->distributionProfile, $fieldValues, $videoFilePath, $thumbnailFilePath, $captionAssetIds);
				$this->submitXml = $feed->getXml();
			}
			else
			{
				$feed = YouTubeDistributionCsvFeedHelper::initializeDefaultSubmitFeed($distributionJobData->distributionProfile, $fieldValues, $videoFilePath, $thumbnailFilePath, $captionAssetIds);
				$this->submitCsvMap = $feed->getCsvMap();
				$this->captionsCsvMap = $feed->getCaptionsCsvMap();
			}

		}
		elseif ($distributionJobData instanceof KalturaDistributionUpdateJobData)
		{
			$remoteIdHandler = YouTubeDistributionRemoteIdHandler::initialize($distributionJobData->remoteId);
			if ($distributionJobData->distributionProfile->feedSpecVersion == YouTubeDistributionFeedSpecVersion::VERSION_2)
			{
				$feed = YouTubeDistributionRightsFeedHelper::initializeDefaultUpdateFeed($distributionJobData->distributionProfile, $fieldValues, $videoFilePath, $thumbnailFilePath, $remoteIdHandler);
				$this->updateXml = $feed->getXml();
			}
			else
			{
				$feed = YouTubeDistributionCsvFeedHelper::initializeDefaultUpdateFeed($distributionJobData->distributionProfile, $fieldValues, $videoFilePath, $thumbnailFilePath, $remoteIdHandler);
				$this->updateCsvMap = $feed->getCsvMap();
			}

		}
		elseif ($distributionJobData instanceof KalturaDistributionDeleteJobData)
		{
			$remoteIdHandler = YouTubeDistributionRemoteIdHandler::initialize($distributionJobData->remoteId);
			if ($distributionJobData->distributionProfile->feedSpecVersion == YouTubeDistributionFeedSpecVersion::VERSION_2)
			{
				$feed = YouTubeDistributionRightsFeedHelper::initializeDefaultDeleteFeed($distributionJobData->distributionProfile, $fieldValues, $videoFilePath, $thumbnailFilePath, $remoteIdHandler);
				$this->deleteXml = $feed->getXml();
			}
			else
			{
				$feed = YouTubeDistributionCsvFeedHelper::initializeDefaultDeleteFeed($distributionJobData->distributionProfile, $fieldValues, $videoFilePath, $thumbnailFilePath, $remoteIdHandler);
				$this->deleteVideoIds = $feed->getDeleteVideoIds();
			}
		}

		$this->newPlaylists = isset($fieldValues[KalturaYouTubeDistributionField::PLAYLISTS]) ? $fieldValues[KalturaYouTubeDistributionField::PLAYLISTS] : null;
		if ($feed)
		{
			$this->sftpDirectory = $feed->getDirectoryName();
			$this->sftpMetadataFilename = $feed->getMetadataTempFileName();
		}

		$distributionProfileId = $distributionJobData->distributionProfile->id;
		$this->loadGoogleConfig($distributionProfileId);
	}
		
	private static $map_between_objects = array
	(
		"videoAssetFilePath",
		"thumbAssetFilePath",
		"captionAssetIds",
		"sftpDirectory",
		"sftpMetadataFilename",
		"currentPlaylists",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/**
	 * @param int $distributionProfileId
	 */
	protected function loadGoogleConfig($distributionProfileId)
	{
		$appConfigId = 'youtubepartner'; // config section for configuration/google_auth.ini
		$authConfig = kConf::get($appConfigId, 'google_auth', null);

		$this->googleClientId = isset($authConfig['clientId']) ? $authConfig['clientId'] : null;
		$this->googleClientSecret = isset($authConfig['clientSecret']) ? $authConfig['clientSecret'] : null;
	
		$distributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		/* @var $distributionProfile YoutubeApiDistributionProfile */

		$tokenData = $distributionProfile->getGoogleOAuth2Data();
		if ($tokenData)
		{
			$this->googleTokenData = json_encode($tokenData);
		}
	}
}
