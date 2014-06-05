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

		$entry = entryPeer::retrieveByPK($distributionJobData->entryDistribution->entryId);

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

		$flavorAssets = assetPeer::retrieveByIds(explode(',', $distributionJobData->entryDistribution->flavorAssetIds));
		$assetInfo = array();
		foreach ( $flavorAssets as $flavorAsset )
		{
			$this->updateFlavorAssetInfo($assetInfo, $flavorAsset, $fieldValues, $entry);
		}

		if ( count($assetInfo) )
		{
			$feedHelper->setAssetInfoArray( $assetInfo ); 
		}

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

	private function updateFlavorAssetInfo(array &$assetInfo, $flavorAsset, $fieldValues, $entry)
	{
		$assetFlavorParams = assetParamsPeer::retrieveByPK( $flavorAsset->getFlavorParamsId() );
		$assetFlavorParamsName = $assetFlavorParams->getName();

		$videoAssetFieldNames = array(
				TvinciDistributionField::VIDEO_ASSET_MAIN,
				TvinciDistributionField::VIDEO_ASSET_TABLET_MAIN,
				TvinciDistributionField::VIDEO_ASSET_SMARTPHONE_MAIN,
			);

		foreach ( $videoAssetFieldNames as $videoAssetFieldName )
		{
			if ( isset($fieldValues[$videoAssetFieldName]) )
			{
				$configFlavorParamName = $fieldValues[$videoAssetFieldName];

				if ( $configFlavorParamName == $assetFlavorParamsName )
				{
					if ( $videoAssetFieldName == TvinciDistributionField::VIDEO_ASSET_MAIN )
					{
						// Main video asset if PC oriented, so we'll fetch a full path (.mp4) download file URL
						$url = $this->getAssetDownloadUrl($flavorAsset);
					}
					else
					{
						// Other assets will be converted to a .m3u8 URL
						$url = $this->getAssetM3U8DownloadUrl($flavorAsset, $entry);
					}

					$assetInfo[$videoAssetFieldName] = array(
							'url' => $url,
							'name' => $assetFlavorParamsName,
						);

					// Note: instead of 'break'ing here, we'll continue to loop in case
					//       the same flavor asset is required by another $videoAssetField
				}
			}
		}
	}

	private function getAssetDownloadUrl($asset)
	{
		$downloadUrl = myPartnerUtils::getCdnHost($asset->getPartnerId()) . $asset->getFinalDownloadUrlPathWithoutKs();
		$downloadUrl .= '/f/' . $asset->getId() . '.' . $asset->getFileExt();
		return $downloadUrl;
	}

	private function getAssetM3U8DownloadUrl($asset, $entry)
	{
		$partnerPath = myPartnerUtils::getUrlForPartner($entry->getPartnerId(), $entry->getSubpId());

		$downloadUrl = myPartnerUtils::getCdnHost($asset->getPartnerId())
						. $partnerPath
						. "/playManifest"
						. "/entryId/{$asset->getEntryId()}"
						. "/format/applehttp"
						. "/protocol/http"
						. "/preferredBitrate/{$asset->getBitrate()}"
						. "/a.m3u8";
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
