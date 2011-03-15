<?php
/**
 * @package plugins.youTubeDistribution
 * @subpackage api.objects
 */
class KalturaYouTubeDistributionJobProviderData extends KalturaDistributionJobProviderData
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
	 * 
	 * @var KalturaStringArray
	 */
	public $playlists;
	
	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
		if(!$distributionJobData)
			return;
			
		if(!($distributionJobData->distributionProfile instanceof KalturaYouTubeDistributionProfile))
			return;
			
		$flavorAssets = flavorAssetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->flavorAssetIds));
		if(count($flavorAssets)) // if we have specific flavor assets for this distribution, grab the first one
			$flavorAsset = reset($flavorAssets);
		else // take the source asset
			$flavorAsset = flavorAssetPeer::retrieveOriginalReadyByEntryId($distributionJobData->entryDistribution->entryId);
		
		if($flavorAsset) 
		{
			$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			$this->videoAssetFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey, true);
		}
		
		$thumbAssets = thumbAssetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->thumbAssetIds));
		if(count($thumbAssets))
		{
			$syncKey = reset($thumbAssets)->getSyncKey(thumbAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			$this->thumbAssetFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey, true);
		}
		
		$this->loadPlaylistsFromMetadata($distributionJobData->entryDistribution->entryId, $distributionJobData->distributionProfile);
	}
		
	private static $map_between_objects = array
	(
		"videoAssetFilePath",
		"thumbAssetFilePath",
		"sftpDirectory",
		"sftpMetadataFilename",
		"playlists",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	protected function loadPlaylistsFromMetadata($entryId, KalturaYouTubeDistributionProfile $distributionProfile)
	{
		$this->playlists = new KalturaStringArray();
		$metadataProfileId = $distributionProfile->metadataProfileId; 
		$metadata = MetadataPeer::retrieveByObject($metadataProfileId, Metadata::TYPE_ENTRY, $entryId);
		if ($metadata)
		{
			$key = $metadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
			$xmlContent = kFileSyncUtils::file_get_contents($key, true, false);
			$xml = new DOMDocument();
			$xml->loadXML($xmlContent);
			
			// first metada field
			$nodes = $xml->getElementsByTagName(YouTubeDistributionProfile::METADATA_FIELD_PLAYLIST);
			foreach($nodes as $node)
			{
				$string = new KalturaString();
				$string->value = $node->textContent;
				$this->playlists[] = $string;
			}
			
			// second metadata field
			$nodes = $xml->getElementsByTagName(YouTubeDistributionProfile::METADATA_FIELD_PLAYLISTS);
			foreach($nodes as $node)
			{
				$string = new KalturaString();
				$string->value = $node->textContent;
				$this->playlists[] = $string;
			}
		}
	}
}
