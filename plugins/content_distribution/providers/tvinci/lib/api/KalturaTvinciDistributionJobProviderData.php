<?php
/**
 * @package plugins.tvinciDistribution
 * @subpackage api.objects
 */
class KalturaTvinciDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{
	const DUR_24_HOURS_IN_SECS = 86400; // = 24h * 60m * 60s;
	
// 	/**
// 	 * @var string
// 	 */
// 	public $videoAssetFilePath;
	
// 	/**
// 	 * @var string
// 	 */
// 	public $thumbAssetFilePath;
	
// 	/**
// 	 * @var string
// 	 */
// 	public $captionAssetIds;
	
// 	/**
// 	 * @var string
// 	 */
// 	public $sftpDirectory;
	
// 	/**
// 	 * @var string
// 	 */
// 	public $sftpMetadataFilename;
	
// 	/**
// 	 * @var string
// 	 */
// 	public $currentPlaylists;

// 	/**
// 	 * @var string
// 	 */
// 	public $newPlaylists;

	/**
	 * @var string
	 */
	public $submitXml;

// 	/**
// 	 * @var string
// 	 */
// 	public $updateXml;

// 	/**
// 	 * @var string
// 	 */
// 	public $deleteXml;

// 	/**
// 	 * @var string
// 	 */
// 	public $googleClientId;

// 	/**
// 	 * @var string
// 	 */
// 	public $googleClientSecret;

// 	/**
// 	 * @var string
// 	 */
// 	public $googleTokenData;

	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
KalturaLog::log(">>> KalturaTvinciDistributionJobProviderData c'tor");
		parent::__construct($distributionJobData);
	    
		if(!$distributionJobData)
			return;
			
		if(!($distributionJobData->distributionProfile instanceof KalturaTvinciDistributionProfile))
			return;
		
// 		$flavorAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->flavorAssetIds));
// 		if(count($flavorAssets)) // if we have specific flavor assets for this distribution, grab the first one
// 			$flavorAsset = reset($flavorAssets);
// 		else // take the source asset
// 			$flavorAsset = assetPeer::retrieveOriginalReadyByEntryId($distributionJobData->entryDistribution->entryId);
		
// 		if($flavorAsset) 
// 		{
// 			$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
// 			if(kFileSyncUtils::fileSync_exists($syncKey))
// 			    $this->videoAssetFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey, false);
// 		}

		$fieldValues = unserialize($this->fieldValues);
		
		$entry = entryPeer::retrieveByPK($distributionJobData->entryDistribution->entryId);
		$extraData = array(
				'entryId' => $entry->getId(),
				'createdAt' => $entry->getCreatedAtAsInt(), 
				'broadcasterName' => 'Kaltura-' . $entry->getPartnerId(), 
			);
		
// 		// get all metadata objects that related to the entry
// 		$tvinciMetadataProfileSysName = $fieldValues[TvinciDistributionField::METADATA_PROFILE_SYSTEM_NAME];
// 		$metadataProfile = MetadataProfilePeer::retrieveBySystemName($tvinciMetadataProfileSysName, $entry->getPartnerId());
// 		$metadataProfileFields = MetadataProfileFieldPeer::retrieveByMetadataProfileId( $metadataProfile->getId() );
// KalturaLog::log(">>> metadataProfileFields: " . print_r($metadataPrfileField, true));
//
// 		$metas = MetadataPeer::retrieveByProfile( $metadataProfile->getId() );
// KalturaLog::log(">>> metas: " . print_r($metas, true));

		$thumbAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->thumbAssetIds));
		$picRatios = array();
		foreach ( $thumbAssets as $thumbAsset )
		{
// 			$syncKey = reset($thumbAssets)->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
// 			if(kFileSyncUtils::fileSync_exists($syncKey))
// 				$this->thumbAssetFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey, false);
			
			$thumbDownloadUrl = $thumbAsset->getDownloadUrlWithExpiry( self::DUR_24_HOURS_IN_SECS );
			$thumbDownloadUrl .= '/f/' . $thumbAsset->getId() . '.' . $thumbAsset->getFileExt();
			
			$ratio = KDLVideoAspectRatio::ConvertFrameSize($thumbAsset->getWidth(), $thumbAsset->getHeight());
			
			$picRatios[] = array(
					'url' => $thumbDownloadUrl,
					'ratio' => $ratio,
				);
			
			if ( $thumbAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB) )
			{
				$extraData['defaultThumbUrl'] = $thumbDownloadUrl;
			}
		}

		$extraData['picRatios'] = $picRatios;
		if ( ! isset($extraData['defaultThumbUrl']) && count($picRatios) )
		{
			// Choose the URL of the first resource in the array
			$extraData['defaultThumbUrl'] = $picRatios[0]['url'];
		}

/*		
Kaltura::log(">>> entry: " . print_r($entry,true));
		
		$thumbUrl = $entry->getThumbUrl();
		if ( $thumbUrl )
		{
			$thumbUrl .= '/f/' . $entry->getId() . '.jpg'; // TODO: Replace this hack with real code the retrieves the full URL of a thumbnail's image file 
			$data['defaultThumbUrl'] = $thumbUrl;
		}
		
		$thumbAssets = assetPeer::retrieveByEntryId($distributionJobData->entryDistribution->entryId, array(assetType::THUMBNAIL));
		$thumbAssetURLs = array();
		foreach ( $thumbAssets as $thumbAsset )
		{
			$thumbUrl = kMrssManager::getAssetUrl($thumbAsset); // TODO: Remove the call the kMrssManager
			$thumbUrl .= '/f/' . 
			
			$isDefault = $thumbAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB);
			
			
			{
				$thumbAssetURL = $thumbAsset->
				if ( ! isset( $data['defaultThumbUrl'] ) )
				{
					$data['defaultThumbUrl']
				}
				
				$nonDefaultThumbAssets[] = $thumbAssert;
			}
		}
*/
		
// 		//Add caption Asset id's
// 		$this->captionAssetIds = $distributionJobData->entryDistribution->assetIds;
		
// 		$entryDistributionDb = EntryDistributionPeer::retrieveByPK($distributionJobData->entryDistributionId);
// 		if ($entryDistributionDb)
// 			$this->currentPlaylists = $entryDistributionDb->getFromCustomData('currentPlaylists');
// 		else
// 			KalturaLog::err('Entry distribution ['.$distributionJobData->entryDistributionId.'] not found');  

// 		if (is_null($this->fieldValues))
// 			return;
// 			//23.5.13 this return is a hack because of bad inheritance of kTvinciDistributionJobProviderData causing some Tvinci distribution 
// 			//batch jobs to not have fieldValues. it can be removed at some point. 
			
// 		$videoFilePath = $this->videoAssetFilePath;
// 		$thumbnailFilePath = $this->thumbAssetFilePath;
// 		$captionAssetIds = $this->captionAssetIds;

		$feed = null;
		if ($distributionJobData instanceof KalturaDistributionSubmitJobData)
		{
			$feed = TvinciDistributionFeedHelper::initializeDefaultSubmitFeed($distributionJobData->distributionProfile, $fieldValues, $extraData);//, $videoFilePath, $thumbnailFilePath, $captionAssetIds);
			$this->submitXml = $feed->getXml();
throw new Exception(">>> distributionJobData: " . print_r($distributionJobData,true) . "\nfieldValues: " . print_r($fieldValues,true) . "\nsubmitXml: {$this->submitXml}");
		}
// 		elseif ($distributionJobData instanceof KalturaDistributionUpdateJobData)
// 		{
// 			$remoteIdHandler = TvinciDistributionRemoteIdHandler::initialize($distributionJobData->remoteId);
// 			$feed = TvinciDistributionFeedHelper::initializeDefaultUpdateFeed($distributionJobData->distributionProfile, $fieldValues);//, $videoFilePath, $thumbnailFilePath, $remoteIdHandler);
// 			$this->updateXml = $feed->getXml();
// 		}
// 		elseif ($distributionJobData instanceof KalturaDistributionDeleteJobData)
// 		{
// 			$remoteIdHandler = TvinciDistributionRemoteIdHandler::initialize($distributionJobData->remoteId);
// 			$feed = TvinciDistributionFeedHelper::initializeDefaultDeleteFeed($distributionJobData->distributionProfile, $fieldValues);//, $videoFilePath, $thumbnailFilePath, $remoteIdHandler);
// 			$this->deleteXml = $feed->getXml();
// 		}

// 		$this->newPlaylists = isset($fieldValues[KalturaTvinciDistributionField::PLAYLISTS]) ? $fieldValues[KalturaTvinciDistributionField::PLAYLISTS] : null;
// 		if ($feed)
// 		{
// 			$this->sftpDirectory = $feed->getDirectoryName();
// 			$this->sftpMetadataFilename = $feed->getMetadataTempFileName();
// 		}

// 		$partnerId = $distributionJobData->distributionProfile->partnerId;
// 		$distributionProfileId = $distributionJobData->distributionProfile->id;
	}
		
	private static $map_between_objects = array
	(
// 		"videoAssetFilePath",
// 		"thumbAssetFilePath",
// 		"captionAssetIds",
// 		"sftpDirectory",
// 		"sftpMetadataFilename",
// 		"currentPlaylists",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}
