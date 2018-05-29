<?php
/**
 * @package plugins.youTubeDistribution
 * @subpackage lib
 */
class YouTubeDistributionCsvFeedHelper
{
	protected $_csvMap = array();
	protected $_captionCsvMap = array();

	/**
	 * @var string
	 */
	protected $_directoryName;

	/**
	 * @var string
	 */
	protected $_metadataTempFileName;

	public function __construct(KalturaYouTubeDistributionProfile $distributionProfile)
	{
		$timestampName = date('Ymd-His') . '_' . time();
		$this->_directoryName = '/' . $timestampName;
		if ($distributionProfile->sftpBaseDir)
			$this->_directoryName = '/' . trim($distributionProfile->sftpBaseDir, '/') . $this->_directoryName;

		$this->_metadataTempFileName = 'youtube_csv20_' . $timestampName . '.csv';
	}

	public static function initializeDefaultSubmitFeed(KalturaYouTubeDistributionProfile $distributionProfile, $fieldValues, $videoFilePath, $thumbnailFilePath, $captionAssetIds)
	{
		$feed = new YouTubeDistributionCsvFeedHelper($distributionProfile);
		$feed->genericHandling($distributionProfile, $fieldValues, $videoFilePath, $thumbnailFilePath);
		$feed->handleCaptions($captionAssetIds);
		return $feed;
	}

	public static function initializeDefaultUpdateFeed(KalturaYouTubeDistributionProfile $distributionProfile, $fieldValues, $videoFilePath, $thumbnailFilePath, YouTubeDistributionRemoteIdHandler $remoteIdHandler)
	{
		$feed = new YouTubeDistributionCsvFeedHelper($distributionProfile);
		$feed->genericHandling($distributionProfile, $fieldValues, $videoFilePath, $thumbnailFilePath, $remoteIdHandler->getVideoId());
		return $feed;
	}

	public function handleCaptions($captionAssetIds)
	{
		$captionAssetInfo = $this->getCaptionAssetInfo($captionAssetIds);
		foreach($captionAssetInfo as $captionInfo)
		{
			$captionData = array();
			if(file_exists($captionInfo['fileUrl']))
			{
				$captionData['language'] = $captionInfo['language'];
				$captionData['caption_file'] = pathinfo($captionInfo['fileUrl'], PATHINFO_BASENAME);
				$captionData['caption_file_ext'] = $captionExtension = $captionInfo['fileExt'];

				$this->_captionCsvMap[] = $captionData;
			}
		}

	}

	public function genericHandling(KalturaYouTubeDistributionProfile $distributionProfile, $fieldValues, $videoFilePath, $thumbnailFilePath, $videoId = null)
	{
		// video file
		if (file_exists($videoFilePath))
			$this->setCsvFieldValue("filename", pathinfo($videoFilePath, PATHINFO_BASENAME));

		// thumbnail file
		if (file_exists($thumbnailFilePath))
			$this->setCsvFieldValue('custom_thumbnail', pathinfo($thumbnailFilePath, PATHINFO_BASENAME));

		$this->setDataByFieldValues($fieldValues, $distributionProfile, $videoId);

		$this->setAdParamsByFieldValues($fieldValues, $distributionProfile);
		$this->appendRightsAdminByFieldValues($fieldValues, $distributionProfile);

	}

	public static function initializeDefaultDeleteFeed(KalturaYouTubeDistributionProfile $distributionProfile, $fieldValues, $videoFilePath, $thumbnailFilePath, YouTubeDistributionRemoteIdHandler $remoteIdHandler)
	{
		$feed = new YouTubeDistributionCsvFeedHelper($distributionProfile);
		$feed->setVideoToDelete($remoteIdHandler->getVideoId());

		return $feed;
	}

	public function setDataByFieldValues(array $fieldValues, KalturaYouTubeDistributionProfile $distributionProfile, $videoId = null)
	{
		if ($videoId)
			$this->setCsvFieldValue('video_id',$videoId);

		if ($distributionProfile->enableContentId)
			$this->setCsvFieldValue('enable_content_id',"Yes");

		if ($distributionProfile->blockOutsideOwnership)
			$this->setCsvFieldValue('block_outside_ownership',"Yes");

		$this->setPrivacyStatus($fieldValues,$distributionProfile);
		$this->setDefaultCategory($fieldValues,$distributionProfile);

		$this->setCsvFieldValueIfHasValue('custom_id', $fieldValues, KalturaYouTubeDistributionField::ASSET_CUSTOM_ID);
		$this->setCsvFieldValueIfHasValue('title', $fieldValues, KalturaYouTubeDistributionField::ASSET_TITLE);
		$this->setCsvFieldValueIfHasValue('spoken_language', $fieldValues, KalturaYouTubeDistributionField::ASSET_SPOKEN_LANGUAGE);
		$this->setCsvFieldValueIfHasValue('description', $fieldValues, KalturaYouTubeDistributionField::MEDIA_DESCRIPTION); //make this like privacy context
		$this->setCsvFieldValueIfHasValue('channel', $fieldValues, KalturaYouTubeDistributionField::VIDEO_CHANNEL);
		$this->setCsvFieldValueIfHasValue('require_paid_subscription', $fieldValues, KalturaYouTubeDistributionField::REQUIRE_PAID_SUBSCRIPTION_TO_VIEW);
		$this->setCsvFieldValueIfHasValue('notify_subscribers', $fieldValues, KalturaYouTubeDistributionField::VIDEO_NOTIFY_SUBSCRIBERS);

		$this->setTime('start_time', $fieldValues, KalturaYouTubeDistributionField::START_TIME);
		$this->setTime('end_time', $fieldValues, KalturaYouTubeDistributionField::END_TIME);

		$this->appendDelimitedValues('keywords', $fieldValues, KalturaYouTubeDistributionField::MEDIA_KEYWORDS, '|');
		$this->appendDelimitedValues('domain_whitelist', $fieldValues, KalturaYouTubeDistributionField::VIDEO_DOMAIN_WHITE_LIST, '|');
		$this->appendDelimitedValues('add_asset_labels', $fieldValues, KalturaYouTubeDistributionField::ASSET_LABLES, '|');
	}

	public function setVideoToDelete($videoId)
	{
		$this->_csvMap['video_id'] = $videoId;
	}

	public function getValueForField(array $fieldValues ,$key)
	{
		if (isset($fieldValues[$key])) {
			return $fieldValues[$key];
		}
		return null;
	}

	public function setCsvFieldValueIfHasValue($fieldName , array $fieldValues, $key)
	{
		$value = $this->getValueForField($fieldValues, $key);
		if (!$value)
			return;
		$this->_csvMap["$fieldName"] = $value;
	}

	public function setCsvFieldValue($key, $value)
	{
		$this->_csvMap["$key"] = $value;
	}

	public function setTime($fieldName, $fieldValues , $value)
	{
		$time = $this->getValueForField($fieldValues, $value);
		if ($time && intval($time))
			$this->_csvMap["$fieldName"] = date('c', intval($time));
	}

	/**
	 * @param KalturaYoutubeDistributionProfile $distributionProfile
	 * @return null|string
	 */
	protected function setPrivacyStatus(array $fieldValues , KalturaYoutubeDistributionProfile $distributionProfile)
	{
		$privacyStatus = $this->getValueForField($fieldValues, KalturaYouTubeApiDistributionField::ENTRY_PRIVACY_STATUS);
		if ($privacyStatus == "" || is_null($privacyStatus))
			$privacyStatus = $distributionProfile->privacyStatus;

		if ($privacyStatus)
		{
			$values = str_replace(',', '|', $privacyStatus);
			$this->_csvMap["privacy"] = $values;
		}
	}
	/**
	 * @param KalturaYoutubeDistributionProfile $distributionProfile
	 * @return null|string
	 */
	protected function setDefaultCategory(array $fieldValues , KalturaYoutubeDistributionProfile $distributionProfile)
	{
		$category = $this->getValueForField($fieldValues, KalturaYouTubeApiDistributionField::MEDIA_CATEGORY);
		if ($category == "" || is_null($category))
			$category = $distributionProfile->defaultCategory;

		if ($category)
			$this->_csvMap["category"] = $category;
	}

	/**
	 * @return null|string
	 */
	protected function getAdvertisingValue(array $fieldValues , $fieldName, $defaultValue )
	{
		$value = $this->getValueForField($fieldValues, $fieldName);
		if ($value == "" || is_null($value))
		{
			$value = $defaultValue;
		}

		return $value;
	}


	/**
	 * @return null|string
	 */
	protected function getPolicyValue(array $fieldValues , $fieldName, $defaultValue )
	{
		$value = $this->getValueForField($fieldValues, $fieldName);
		if ($value == "" || is_null($value))
		{
			$value = $defaultValue;
		}

		return $value;
	}


	public function getCaptionAssetInfo($captionAssetIds)
	{
		$captionAssetInfo = array();
		
		$assetIdsArray = explode ( ',', $captionAssetIds );
			
		if (empty($assetIdsArray)) 
			return null;
				
		$assets = assetPeer::retrieveByIds($assetIdsArray);
			
		foreach ($assets as $asset)
		{
			$assetType = $asset->getType();
			if($assetType == CaptionPlugin::getAssetTypeCoreValue ( CaptionAssetType::CAPTION ))
			{
				/* @var $asset CaptionAsset */
				$syncKey = $asset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
				if(kFileSyncUtils::fileSync_exists($syncKey))
				{
			    	$captionAssetInfo[$asset->getId()]['fileUrl'] = kFileSyncUtils::getLocalFilePathForKey ( $syncKey, false );
			    	$captionAssetInfo[$asset->getId()]['fileExt'] = $asset->getFileExt();
			    	$captionAssetInfo[$asset->getId()]['language'] = $asset->getLanguage();
			    	break;
				}
			}
		}
		return $captionAssetInfo;
	}

	public function getCaptionLanguage($language)
	{
		$languageReflector = KalturaTypeReflectorCacher::get('KalturaLanguage');
		return $languageReflector->getConstantName($language);
	}

	public function appendDelimitedValues($csvFieldKey, array $fieldValues, $fieldName, $delimiter )
	{
		$valuesStr = $this->getValueForField($fieldValues, $fieldName);
		$values = str_replace(',' ,$delimiter, $valuesStr );
		if($values)
			$this->_csvMap["$csvFieldKey"] = $values ;
	}

	public function setAdParamsByFieldValues(array $fieldValues, KalturaYouTubeDistributionProfile $distributionProfile)
	{
		$adTypes = '';
		$delimiter = '|';
		$adValue = $this->getAdvertisingValue($fieldValues,KalturaYouTubeDistributionField::ADVERTISING_INSTREAM_STANDARD,$distributionProfile->instreamStandard);
		if ($this->isAllowedValue($adValue))
			$adTypes = "instream_standard";
		elseif($this->isNotAllowedValue($adValue))
			$adTypes = "!instream_standard";

		$adValue = $this->getAdvertisingValue($fieldValues,KalturaYouTubeDistributionField::ADVERTISING_INSTREAM_TRUEVIEW,$distributionProfile->instreamTrueview);
		if ($this->isAllowedValue($adValue))
			$adTypes .= $delimiter."instream_trueview";
		elseif($this->isNotAllowedValue($adValue))
			$adTypes .= $delimiter."!instream_trueview";

		$adValue = $this->getAdvertisingValue($fieldValues,KalturaYouTubeDistributionField::ADVERTISING_ALLOW_INVIDEO,$distributionProfile->allowInvideo);
		if ($this->isAllowedValue($adValue))
			$adTypes .= $delimiter."invideo_overlay";
		elseif($this->isNotAllowedValue($adValue))
			$adTypes .= $delimiter."!invideo_overlay";

		$adValue = $this->getAdvertisingValue($fieldValues,KalturaYouTubeDistributionField::PRODUCT_LISTING_ADS,$distributionProfile->productListingAds);
		if ($this->isAllowedValue($adValue))
			$adTypes .= $delimiter."product_listing";
		elseif($this->isNotAllowedValue($adValue))
			$adTypes .= $delimiter."!product_listing";

		$adValue = $this->getAdvertisingValue($fieldValues,KalturaYouTubeDistributionField::ADVERTISING_ALLOW_ADSENSE_FOR_VIDEO,$distributionProfile->allowAdsenseForVideo);
		if ($this->isAllowedValue($adValue))
			$adTypes .= $delimiter."display";
		elseif($this->isNotAllowedValue($adValue))
			$adTypes .= $delimiter."!display";

		$adValue = $this->getAdvertisingValue($fieldValues,KalturaYouTubeDistributionField::THIRD_PARTY_ADS,$distributionProfile->thirdPartyAds);
		if ($this->isAllowedValue($adValue))
		{
			$adTypes .= $delimiter."third_party_ads";
			$this->setCsvFieldValueIfHasValue('ad_server_video_id', $fieldValues, KalturaYouTubeDistributionField::THIRD_PARTY_AD_SERVER_VIDEO_ID);
		}
		elseif($this->isNotAllowedValue($adValue))
			$adTypes .= $delimiter."!third_party_ads";

		$this->setCsvFieldValue('ad_types', $adTypes);
	}

	public function appendRightsAdminByFieldValues(array $fieldValues, KalturaYouTubeDistributionProfile $distributionProfile)
	{
		$usagePolicy = $this->getPolicyValue($fieldValues, KalturaYouTubeDistributionField::POLICY_COMMERCIAL, $distributionProfile->commercialPolicy );
		$this->setCsvFieldValue('usage_policy', $usagePolicy );

		$matchPolicy = $this->getPolicyValue($fieldValues, KalturaYouTubeDistributionField::POLICY_UGC, $distributionProfile->ugcPolicy );
		$this->setCsvFieldValue('match_policy', $matchPolicy );
	}

	public function getCsvMap()
	{
		//get the csv as string to send
		return serialize($this->_csvMap);
	}

	public function getDeleteVideoIds()
	{
		return serialize($this->_csvMap);
	}

	public function getCaptionsCsvMap()
	{
		//get the csv as string to send
		return serialize($this->_captionCsvMap);
	}

	/**
	 * @return string
	 */
	public function getDirectoryName()
	{
		return $this->_directoryName;
	}

	/**
	 * @return string
	 */
	public function getMetadataTempFileName()
	{
		return $this->_metadataTempFileName;
	}

	private function isAllowedValue($value)
	{
		return in_array($value, array('true', 'True', '1'), true);
	}

	private function isNotAllowedValue($value)
	{
		return in_array($value, array('false', 'False', '0'), true);
	}
}