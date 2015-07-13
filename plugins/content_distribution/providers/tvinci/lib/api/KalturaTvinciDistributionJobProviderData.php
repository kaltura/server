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

		if(!$distributionJobData)
			return;

		if(!($distributionJobData->distributionProfile instanceof KalturaTvinciDistributionProfile))
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
		$feedHelper->setCreatedAt( $entry->getCreatedAtAsInt() );
		$feedHelper->setDescription( $entry->getDescription() );
		$feedHelper->setTitleName( $entry->getName() );
		$feedHelper->setSunrise($distributionJobData->entryDistribution->sunrise);

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

		$this->initPlayManifestUrls( $entry, $feedHelper );

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

	private function initPlayManifestUrls($entry, $feedHelper)
	{
		$videoAssetDataMap = array(
			array( 'Main',						PlaybackProtocol::AKAMAI_HDS,	'mbr',		'a4m' ),
			array( 'Tablet Main',				PlaybackProtocol::APPLE_HTTP,	'ipad',		'm3u8' ),
			array( 'Smartphone Main',			PlaybackProtocol::APPLE_HTTP,	'iphone',	'm3u8' ),
		);

		// Loop and build the file nodes
		foreach ( $videoAssetDataMap as $videoAssetData )
		{
			$tvinciAssetName = $videoAssetData[0];
			$playbackProtocol = $videoAssetData[1];
			$tag = $videoAssetData[2];
			$fileExt = $videoAssetData[3];
			$fileCoGuid = "{$entry->getIntId()}{$entry->getFlavorParamsIds}";
			$url = $this->getPlayManifestUrl($entry, $playbackProtocol, $tag, $fileExt);
			$feedHelper->setVideoAssetData( $tvinciAssetName, $url,$fileCoGuid );
		}
	}

	private function getAssetDownloadUrl($asset)
	{
		$downloadUrl = myPartnerUtils::getCdnHost($asset->getPartnerId()) . $asset->getFinalDownloadUrlPathWithoutKs();
		$downloadUrl .= '/f/' . $asset->getId() . '.' . $asset->getFileExt();
		return $downloadUrl;
	}

	private function getPlayManifestUrl($entry, $playbackProtocol, $tag, $fileExt)
	{
		$partnerPath = myPartnerUtils::getUrlForPartner($entry->getPartnerId(), $entry->getSubpId());

		$downloadUrl = myPartnerUtils::getCdnHost($entry->getPartnerId())
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
