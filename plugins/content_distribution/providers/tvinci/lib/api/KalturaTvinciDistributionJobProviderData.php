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

		$fieldValues = unserialize($this->fieldValues);

		$entry = null;
		if ( $distributionJobData->entryDistribution->entryId )
		{
			$entry = entryPeer::retrieveByPK($distributionJobData->entryDistribution->entryId);
		}

		if ( ! $entry ) {
			KalturaLog::err("Can't find entry with id: {$distributionJobData->entryDistribution->entryId}");
			return;
		}

		$feedHelper = new TvinciDistributionFeedHelper($distributionJobData->distributionProfile, $fieldValues);
		$feedHelper->setEntryId( $entry->getId() );
		$feedHelper->setCreatedAt( $entry->getCreatedAtAsInt() );

		$broadcasterName = 'Kaltura-' . $entry->getPartnerId();
		$feedHelper->setBroadcasterName( $broadcasterName );

		$thumbAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->thumbAssetIds));
		$picRatios = array();
		$defaultThumbnail = null;
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
	}

	private function initPlayManifestUrls($entry, $feedHelper)
	{
		$url = $this->getPlayManifestUrl($entry, PlaybackProtocol::AKAMAI_HDS, 'mbr', 'a4m');
		$feedHelper->setMainPlayManifestUrl( $url );

		$url = $this->getPlayManifestUrl($entry, PlaybackProtocol::APPLE_HTTP, 'ipad', 'm3u8');
		$feedHelper->setiPadPlayManifestUrl( $url );

		$url = $this->getPlayManifestUrl($entry, PlaybackProtocol::APPLE_HTTP, 'iphone', 'm3u8');
		$feedHelper->setiPhonePlayManifestUrl( $url );
	}

	private function getAssetDownloadUrl($asset)
	{
		$downloadUrl = myPartnerUtils::getCdnHost($asset->getPartnerId()) . $asset->getFinalDownloadUrlPathWithoutKs();
		$downloadUrl .= '/f/' . $asset->getId() . '.' . $asset->getFileExt();
		return $downloadUrl;
	}

	private function getPlayManifestUrl($entry, $format, $tag, $fileExt)
	{
		$partnerPath = myPartnerUtils::getUrlForPartner($entry->getPartnerId(), $entry->getSubpId());

		$downloadUrl = myPartnerUtils::getCdnHost($entry->getPartnerId())
						. $partnerPath
						. "/playManifest"
						. "/entryId/{$entry->getId()}"
						. "/format/$format"
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
