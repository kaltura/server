<?php
/**
 * @package plugins.tvinciDistribution
 * @subpackage api.objects
 */
class KalturaTvinciDistributionJobProviderData extends KalturaConfigurableDistributionJobProviderData
{
	/**
	 * @var string
	 */
	public $xml;

	public function __construct(KalturaDistributionJobData $distributionJobData = null)
	{
		parent::__construct($distributionJobData);

		if( (!$distributionJobData) ||
			(!($distributionJobData->distributionProfile instanceof KalturaTvinciDistributionProfile)) ||
			(! $distributionJobData->entryDistribution) )
			return;

		$entry = null;
		if ( $distributionJobData->entryDistribution->entryId )
		{
			$entry = entryPeer::retrieveByPK($distributionJobData->entryDistribution->entryId);
		}

		if ( ! $entry ) {
			KalturaLog::err("Can't find entry with id: {$distributionJobData->entryDistribution->entryId}");
			return;
		}

		$feedHelper = new TvinciDistributionFeedHelper($distributionJobData->distributionProfile);
		$feedHelper->setEntryId( $entry->getId() );
		$feedHelper->setReferenceId($entry->getReferenceID());
		$feedHelper->setDescription( $entry->getDescription() );
		$feedHelper->setTitleName( $entry->getName() );
		$feedHelper->setSunrise($distributionJobData->entryDistribution->sunrise);
		$feedHelper->setSunset($distributionJobData->entryDistribution->sunset);

		$thumbAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->thumbAssetIds));
		$picRatios = array();
		$defaultThumbUrl = null;
		foreach ( $thumbAssets as $thumbAsset )
		{
			$thumbDownloadUrl = $this->getAssetDownloadUrl( $thumbAsset );

			$ratio = KDLVideoAspectRatio::ConvertFrameSize($thumbAsset->getWidth(), $thumbAsset->getHeight());

			$picRatios[] = array(
					'url' => $thumbDownloadUrl,
					'ratio' => $ratio,
				);

			if ( $thumbAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB) )
			{
				$defaultThumbUrl = $thumbDownloadUrl;
			}
		}

		$feedHelper->setPicRatiosArray( $picRatios );
		if ( !$defaultThumbUrl && count($picRatios) )
		{
			// Choose the URL of the first resource in the array
			$defaultThumbUrl = $picRatios[0]['url'];
		}

		$feedHelper->setDefaultThumbnailUrl( $defaultThumbUrl );

		$this->createPlayManifestURLs($distributionJobData->entryDistribution, $entry, $feedHelper);

		$metadatas = MetadataPeer::retrieveAllByObject(MetadataObjectType::ENTRY, $distributionJobData->entryDistribution->entryId);
		$fullMetadataXML='';
		foreach($metadatas as $metadataField) {
			$syncKey = $metadataField->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
			$currMetaXML = kFileSyncUtils::file_get_contents($syncKey, true, false);
			$fullMetadataXML.=$currMetaXML;
		}
		$feedHelper->setMetasXML($fullMetadataXML);

		if ($distributionJobData instanceof KalturaDistributionSubmitJobData)
		{
			$this->xml = $feedHelper->buildSubmitFeed();
		}
		elseif ($distributionJobData instanceof KalturaDistributionUpdateJobData)
		{
			$this->xml = $feedHelper->buildUpdateFeed();
		}
		elseif ($distributionJobData instanceof KalturaDistributionDeleteJobData)
		{
			$this->xml = $feedHelper->buildDeleteFeed();
		}
		KalturaLog::debug("XML Constructed by the Tvinci feed helper :{$this->xml}");

	}

	private static function getVideoAssetDataMap()
	{
		return array(
			array( 'Main',						PlaybackProtocol::SILVER_LIGHT,	array('ism'),		    		'ism' ),
			array( 'Tablet Main',				PlaybackProtocol::APPLE_HTTP,	array('ipadnew','ipad'),		'm3u8' ),
			array( 'Smartphone Main',			PlaybackProtocol::APPLE_HTTP,	array('iphonenew','iphone'),	'm3u8' ),
		);
	}

	private static function createFileCoGuid($entryId, $flavorParamsId)
	{
		return "{$entryId}_{$flavorParamsId}";
	}


	private function createPlayManifestURLs(KalturaEntryDistribution $entryDistribution, entry $entry, TvinciDistributionFeedHelper $feedHelper)
	{
		$distributionFlavorAssets  = assetPeer::retrieveByIds(explode(',', $entryDistribution->flavorAssetIds));
		$videoAssetDataMap = $this->getVideoAssetDataMap();
		foreach ( $videoAssetDataMap as $videoAssetData )
		{
			$tvinciAssetName = $videoAssetData[0];
			$playbackProtocol = $videoAssetData[1];
			$tags = $videoAssetData[2];
			$fileExt = $videoAssetData[3];
			$keys = array();
			$relevantTags = array();
			foreach ( $distributionFlavorAssets as $distributionFlavorAsset )
			{
				foreach ($tags as $tag)
				{
					if ($distributionFlavorAsset->isLocalReadyStatus() &&
						$distributionFlavorAsset->hasTag($tag) )
					{
						$key = $this->createFileCoGuid($entry->getEntryId(),$distributionFlavorAsset->getFlavorParamsId());
						if (!in_array($key, $keys))	$keys[] = $key;
						if (!in_array($tag, $relevantTags))	$relevantTags[] = $tag;
					}
				}
			}

			if ($keys)
			{
				$fileCoGuid = implode(",", $keys);
				$tagFlag = implode(",", $relevantTags);
				$url = $this->getPlayManifestUrl($entry, $playbackProtocol, $tagFlag, $fileExt);
				$feedHelper->setVideoAssetData( $tvinciAssetName, $url, $fileCoGuid );
			}
		}
	}

	private function getAssetDownloadUrl($asset)
	{
		$downloadUrl = myPartnerUtils::getCdnHost($asset->getPartnerId(), null, 'thumbnail') . $asset->getFinalDownloadUrlPathWithoutKs();
		$downloadUrl .= '/f/' . $asset->getId() . '.' . $asset->getFileExt();
		return $downloadUrl;
	}

	private function getPlayManifestUrl($entry, $playbackProtocol, $tag, $fileExt)
	{
		$partnerPath = myPartnerUtils::getUrlForPartner($entry->getPartnerId(), $entry->getSubpId());

		$downloadUrl = myPartnerUtils::getCdnHost($entry->getPartnerId(), null , 'api')
						. $partnerPath
						. "/playManifest"
						. "/entryId/{$entry->getId()}"
						. "/format/$playbackProtocol"
						. "/tags/$tag"
						. "/protocol/http"
						. "/f/a.$fileExt";
		return $downloadUrl;
	}

	private static $map_between_objects = array
	(
		'xml',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}
