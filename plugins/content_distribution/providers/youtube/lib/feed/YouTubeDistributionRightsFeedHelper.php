<?php
/**
 * @package plugins.youTubeDistribution
 * @subpackage lib
 */
class YouTubeDistributionRightsFeedHelper
{
	/**
	 * @var DOMDocument
	 */
	protected $_doc;

	/**
	 * @var DOMXPath
	 */
	protected $_xpath;

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
		$this->_doc = new DOMDocument();
		$this->_doc->formatOutput = true;
		$this->_doc->appendChild($this->_doc->createElement('feed'));
		$this->_doc->createAttributeNS('http://www.youtube.com/schemas/cms/2.0','xmlns');

		$this->_xpath = new DOMXPath($this->_doc);

		$timestampName = date('Ymd-His') . '_' . time();
		$this->_directoryName = '/' . $timestampName;
		if ($distributionProfile->sftpBaseDir)
			$this->_directoryName = '/' . trim($distributionProfile->sftpBaseDir, '/') . $this->_directoryName;

		$this->_metadataTempFileName = 'youtube_xml20_' . $timestampName . '.xml';
	}

	public static function initializeDefaultSubmitFeed(KalturaYouTubeDistributionProfile $distributionProfile, $fieldValues, $videoFilePath, $thumbnailFilePath, $captionAssetIds)
	{
		$identifier= $fieldValues[KalturaYouTubeDistributionField::ASSET_CUSTOM_ID];
		$videoTag = $identifier.'-video';
		$thumbnailTag = $identifier.'-thumbnail';
		$captionTag = $identifier.'-caption';

		$feed = new YouTubeDistributionRightsFeedHelper($distributionProfile);
		$feed->setNotificationEmail($fieldValues);
		$feed->setChannel($fieldValues);
		$feed->setMetadataByFieldValues($fieldValues);
		$feed->setByXpath('video/@tag', $videoTag);
		$feed->setByXpath('asset/@tag', $videoTag);

		// video file
		$urgentReference = $fieldValues[KalturaYouTubeDistributionField::URGENT_REFERENCE_FILE];
		$feed->appendFileElement('video', $urgentReference, pathinfo($videoFilePath, PATHINFO_BASENAME), $videoTag);

		// thumbnail file
		if (file_exists($thumbnailFilePath))
		{
			$feed->appendFileElement('image', false, pathinfo($thumbnailFilePath, PATHINFO_BASENAME), $thumbnailTag);
			$feed->appendVideoArtworkElement('custom_thumbnail', $thumbnailTag);
		}

		// Handle addition of caption asset items
		$captionAssetInfo = $feed->getCaptionAssetInfo($captionAssetIds);
		foreach($captionAssetInfo as $captionInfo)
		{
			if(file_exists($captionInfo['fileUrl']))
			{
				$captionTag = $captionTag . '-' . $captionInfo['language'];
				$feed->appendFileElement('timed_text', false, pathinfo($captionInfo['fileUrl'], PATHINFO_BASENAME), $captionTag);
				$feed->appendCaptionElement($captionTag, $captionInfo['fileExt'], $captionInfo['language']);
				$feed->appendRelationship(array("/feed/caption[@tag='$captionTag']", "/feed/file[@tag='$captionTag']"), array("/feed/video[@tag='$videoTag']"));
			}
		}
		
		$feed->appendVideoAssetFileRelationship($fieldValues, $videoTag);
		$feed->setAdParamsByFieldValues($fieldValues, $videoTag, $distributionProfile->enableAdServer);
		$feed->appendRightsAdminByFieldValues($fieldValues, $videoTag);

		return $feed;
	}

	public static function initializeDefaultUpdateFeed(KalturaYouTubeDistributionProfile $distributionProfile, $fieldValues, $videoFilePath, $thumbnailFilePath, YouTubeDistributionRemoteIdHandler $remoteIdHandler)
	{
		$identifier= $fieldValues[KalturaYouTubeDistributionField::ASSET_CUSTOM_ID];
		$videoTag = $identifier.'-video';
		$thumbnailTag = $identifier.'-thumbnail';

		$feed = new YouTubeDistributionRightsFeedHelper($distributionProfile);
		$feed->setNotificationEmail($fieldValues);

		if ($remoteIdHandler->getVideoId())
		{
			$feed->setByXpath('video/@tag', $videoTag);
			$feed->setVideoMetadataByFieldValues($fieldValues, $remoteIdHandler->getVideoId());
		}
		if ($remoteIdHandler->getAssetId())
		{
			$feed->setByXpath('asset/@tag', $videoTag);
			$feed->setAssetMetadataByFieldValues($fieldValues, $remoteIdHandler->getAssetId());
		}

		// thumbnail file
		if (file_exists($thumbnailFilePath))
		{
			$feed->appendFileElement('image', false, pathinfo($thumbnailFilePath, PATHINFO_BASENAME), $thumbnailTag);
			$feed->appendVideoArtworkElement('custom_thumbnail', $thumbnailTag);
		}

		$feed->setAdParamsByFieldValues($fieldValues, $videoTag, $distributionProfile->enableAdServer);

		return $feed;
	}

	public static function initializeDefaultDeleteFeed(KalturaYouTubeDistributionProfile $distributionProfile, $fieldValues, $videoFilePath, $thumbnailFilePath, YouTubeDistributionRemoteIdHandler $remoteIdHandler)
	{
		$feed = new YouTubeDistributionRightsFeedHelper($distributionProfile);
		$feed->setNotificationEmail($fieldValues);
		$feed->setByXpath('video/@action', 'delete');
		$feed->setByXpath('video/@id', $remoteIdHandler->getVideoId());

		if ($distributionProfile->deleteReference && $remoteIdHandler->getReferenceId())
		{
			$feed->setByXpath('reference/@action', 'delete');
			$feed->setByXpath('reference/@id', $remoteIdHandler->getReferenceId());
			if ($distributionProfile->releaseClaims)
				$feed->setByXpath('reference/@release_claims', 'True');
		}

		return $feed;
	}

	public function __toString()
	{
		return $this->_doc->saveXML();
	}

	/**
	 * Sets or creates element(s) and/or attribute(s) by xpath
	 * Examples:
	 *  - MyElement/@MyNewAttribute
	 *  - MyElement/MySubElement/AnotherSubElement
	 *  - MyElement/MySubElement/AnotherSubElement/@TheAttribute
	 *
	 * @param $xpath
	 * @param $value
	 */
	public function setByXpath($xpath, $value)
	{
		$xpathArray = explode('/', $xpath);

		/** @var $node DOMElement */
		$node = $this->_doc->firstChild;
		foreach($xpathArray as $xpathItem)
		{
			if (!$this->isAttribute($xpathItem))
			{
				$elements = $node->getElementsByTagName($xpathItem);
				if ($elements->length == 0)
					$node = $node->appendChild($this->_doc->createElement($xpathItem));
				else
					$node = $elements->item(0);
			}
		}

		if ($this->isAttribute($xpathItem))
			$node->setAttribute(str_replace('@', '', $xpathItem), htmlspecialchars($value, ENT_COMPAT, 'UTF-8')); // ENT_COMPAT to leave single-quotes as is
		else
			$node->nodeValue = htmlspecialchars($value, ENT_NOQUOTES, 'UTF-8'); // do not encode any quotes
	}

	public function setNotificationEmail(array $fieldValues)
	{
		$this->setByXpathFieldValueIfHasValue('@notification_email', $fieldValues, KalturaYouTubeDistributionField::NOTIFICATION_EMAIL);
	}

	public function setChannel(array $fieldValues)
	{
		$this->setByXpathFieldValueIfHasValue('@channel', $fieldValues, KalturaYouTubeDistributionField::VIDEO_CHANNEL);
	}
	
	public function setMetadataByFieldValues(array $fieldValues)
	{
		$this->setAssetMetadataByFieldValues($fieldValues);
		$this->setVideoMetadataByFieldValues($fieldValues);
	}

	public function setAssetMetadataByFieldValues($fieldValues, $assetId = null)
	{
		if ($assetId)
			$this->setByXpath('asset/@id', $assetId);

		$this->setByXpathFieldValueIfHasValue('asset/@type', $fieldValues, KalturaYouTubeDistributionField::ASSET_TYPE);
		$this->setByXpathFieldValueIfHasValue('asset/@override_manual_edits', $fieldValues, KalturaYouTubeDistributionField::ASSET_OVERRIDE_MANUAL_EDITS);

		$this->setByXpathFieldValueIfHasValue('asset/actor', $fieldValues, KalturaYouTubeDistributionField::ASSET_ACTOR);
		$this->setByXpathFieldValueIfHasValue('asset/broadcaster', $fieldValues, KalturaYouTubeDistributionField::ASSET_BROADCASTER);
		$this->setByXpathFieldValueIfHasValue('asset/content_type', $fieldValues, KalturaYouTubeDistributionField::ASSET_CONTENT_TYPE);
		$this->setByXpathFieldValueIfHasValue('asset/custom_id', $fieldValues, KalturaYouTubeDistributionField::ASSET_CUSTOM_ID);
		$this->setByXpathFieldValueIfHasValue('asset/description', $fieldValues, KalturaYouTubeDistributionField::ASSET_DESCRIPTION);
		$this->setByXpathFieldValueIfHasValue('asset/director', $fieldValues, KalturaYouTubeDistributionField::ASSET_DIRECTOR);
		$this->setByXpathFieldValueIfHasValue('asset/eidr', $fieldValues, KalturaYouTubeDistributionField::ASSET_EIDR);
		$this->setByXpathFieldValueIfHasValue('asset/end_year', $fieldValues, KalturaYouTubeDistributionField::ASSET_END_YEAR);
		$this->setByXpathFieldValueIfHasValue('asset/episode', $fieldValues, KalturaYouTubeDistributionField::ASSET_EPISODE);
		$this->setByXpathFieldValueIfHasValue('asset/genre', $fieldValues, KalturaYouTubeDistributionField::ASSET_GENRE);
		$this->setByXpathFieldValueIfHasValue('asset/grid', $fieldValues, KalturaYouTubeDistributionField::ASSET_GRID);
		$this->setByXpathFieldValueIfHasValue('asset/isan', $fieldValues, KalturaYouTubeDistributionField::ASSET_ISAN);
		$this->appendAssetKeywords($fieldValues);
		$this->setByXpathFieldValueIfHasValue('asset/original_release_date', $fieldValues, KalturaYouTubeDistributionField::ASSET_ORIGINAL_RELEASE_DATE);
		$this->setByXpathFieldValueIfHasValue('asset/original_release_medium', $fieldValues, KalturaYouTubeDistributionField::ASSET_ORIGINAL_RELEASE_MEDIUM);
		$this->setByXpathFieldValueIfHasValue('asset/producer', $fieldValues, KalturaYouTubeDistributionField::ASSET_PRODUCER);
		$this->setByXpathFieldValueIfHasValue('asset/rating/@system', $fieldValues, KalturaYouTubeDistributionField::ASSET_RATING_SYSTEM);
		$this->setByXpathFieldValueIfHasValue('asset/rating', $fieldValues, KalturaYouTubeDistributionField::ASSET_RATING_VALUE);
		$this->setByXpathFieldValueIfHasValue('asset/season', $fieldValues, KalturaYouTubeDistributionField::ASSET_SEASON);
		$this->setByXpathFieldValueIfHasValue('asset/shows_and_movies_programming', $fieldValues, KalturaYouTubeDistributionField::ASSET_SHOW_AND_MOVIE_PROGRAMMING);
		$this->setByXpathFieldValueIfHasValue('asset/show_title', $fieldValues, KalturaYouTubeDistributionField::ASSET_SHOW_TITLE);
		$this->setByXpathFieldValueIfHasValue('asset/spoken_language', $fieldValues, KalturaYouTubeDistributionField::ASSET_SPOKEN_LANGUAGE);
		$this->setByXpathFieldValueIfHasValue('asset/start_year', $fieldValues, KalturaYouTubeDistributionField::ASSET_START_YEAR);
		$this->setByXpathFieldValueIfHasValue('asset/subtitled_language', $fieldValues, KalturaYouTubeDistributionField::ASSET_SUBTITLED_LANGUAGE);
		$this->setByXpathFieldValueIfHasValue('asset/title', $fieldValues, KalturaYouTubeDistributionField::ASSET_TITLE);
		$this->setByXpathFieldValueIfHasValue('asset/tms_id', $fieldValues, KalturaYouTubeDistributionField::ASSET_TMS_ID);
		$this->setByXpathFieldValueIfHasValue('asset/upc', $fieldValues, KalturaYouTubeDistributionField::ASSET_UPC);
		$this->setByXpathFieldValueIfHasValue('asset/url', $fieldValues, KalturaYouTubeDistributionField::ASSET_URL);
		$this->setByXpathFieldValueIfHasValue('asset/writer', $fieldValues, KalturaYouTubeDistributionField::ASSET_WRITER);
		$this->appendWorldWideOwnership();
	}

	public function setVideoMetadataByFieldValues($fieldValues, $videoId = null)
	{
		if ($videoId)
			$this->setByXpath('video/@id', $videoId);

		$this->setByXpathFieldValueIfHasValue('video/allow_comment_rating', $fieldValues, KalturaYouTubeDistributionField::VIDEO_ALLOW_COMMENT_RATINGS);
		$this->setByXpathFieldValueIfHasValue('video/allow_comments', $fieldValues, KalturaYouTubeDistributionField::ALLOW_COMMENTS);
		$this->setByXpathFieldValueIfHasValue('video/allow_embedding', $fieldValues, KalturaYouTubeDistributionField::ALLOW_EMBEDDING);
		$this->setByXpathFieldValueIfHasValue('video/allow_ratings', $fieldValues, KalturaYouTubeDistributionField::ALLOW_RATINGS);
		$this->setByXpathFieldValueIfHasValue('video/allow_responses', $fieldValues, KalturaYouTubeDistributionField::ALLOW_RESPONSES);
		$this->setByXpathFieldValueIfHasValue('video/allow_syndication', $fieldValues, KalturaYouTubeDistributionField::VIDEO_ALLOW_SYNDICATION);
		$this->setByXpathFieldValueIfHasValue('video/channel', $fieldValues, KalturaYouTubeDistributionField::VIDEO_CHANNEL);
		$this->setByXpathFieldValueIfHasValue('video/description', $fieldValues, KalturaYouTubeDistributionField::MEDIA_DESCRIPTION);
		$this->setByXpathFieldValueIfHasValue('video/domain_blacklist', $fieldValues, KalturaYouTubeDistributionField::VIDEO_DOMAIN_BLACK_LIST);
		$this->setByXpathFieldValueIfHasValue('video/domain_whitelist', $fieldValues, KalturaYouTubeDistributionField::VIDEO_DOMAIN_WHITE_LIST);
		$this->setByXpathFieldValueIfHasValue('video/genre', $fieldValues, KalturaYouTubeDistributionField::MEDIA_CATEGORY);
		$this->setByXpathFieldValueIfHasValue('video/hide_view_count', $fieldValues, KalturaYouTubeDistributionField::VIDEO_HIDE_VIEW_COUNT);
		$this->appendVideoKeywords($fieldValues);
		$this->setByXpathFieldValueIfHasValue('video/notify_subscribers', $fieldValues, KalturaYouTubeDistributionField::VIDEO_NOTIFY_SUBSCRIBERS);
		$this->setByXpathFieldValueIfHasValue('video/public', $fieldValues, KalturaYouTubeDistributionField::VIDEO_PUBLIC);
		$this->setByXpathFieldValueIfHasValue('video/recorded/date', $fieldValues, KalturaYouTubeDistributionField::DATE_RECORDED);
		$this->setByXpathFieldValueIfHasValue('video/recorded/location', $fieldValues, KalturaYouTubeDistributionField::LOCATION_LOCATION_TEXT);
		$this->setByXpathFieldValueIfHasValue('video/recorded/country', $fieldValues, KalturaYouTubeDistributionField::LOCATION_COUNTRY);
		$this->setByXpathFieldValueIfHasValue('video/recorded/zip', $fieldValues, KalturaYouTubeDistributionField::LOCATION_ZIP_CODE);
		$this->setByXpathFieldValueIfHasValue('video/title', $fieldValues, KalturaYouTubeDistributionField::MEDIA_TITLE);

		$startTime = $this->getValueForField($fieldValues, KalturaYouTubeDistributionField::START_TIME);
		if ($startTime && intval($startTime))
			$this->setByXpath('video/start_time', date('c', intval($startTime)));

		$endTime = $this->getValueForField($fieldValues, KalturaYouTubeDistributionField::END_TIME);
		if ($endTime && intval($endTime))
			$this->setByXpath('video/end_time', date('c', intval($endTime)));
	}

	public function getValueForField(array $fieldValues ,$key)
	{
		if (isset($fieldValues[$key])) {
			return $fieldValues[$key];
		}
		return null;
	}

	public function setByXpathFieldValueIfHasValue($xpath, array $fieldValues, $key)
	{
		$value = $this->getValueForField($fieldValues, $key);
		if (!$value)
			return;
		$this->setByXpath($xpath, $value);
	}
	
	public function getCaptionAssetInfo($captionAssetIds)
	{
		$captionAssetInfo = array();
		
		$assetIdsArray = explode ( ',', $captionAssetIds );
			
		if (empty($assetIdsArray)) 
			return;
				
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

	public function appendFileElement($type, $urgentReference, $filename, $tag)
	{
		$file = $this->_doc->createElement('file');
		$file->setAttribute('type', $type);
		$file->setAttribute('tag', $tag);
		if ($urgentReference)
			$file->setAttribute('urgent_reference', $urgentReference);
		$file->appendChild($this->_doc->createElement('filename', $filename));
		$this->_doc->firstChild->appendChild($file);
		return $file;
	}
	
	public function appendCaptionElement($tag, $fileExt, $language)
	{
		$languageReflector = KalturaTypeReflectorCacher::get('KalturaLanguage');
		
		$captionElem = $this->_doc->createElement('caption');
		$captionElem->setAttribute('tag', $tag);
		$captionElem->appendChild($this->_doc->createElement('format', $fileExt));
		$captionElem->appendChild($this->_doc->createElement('language', $languageReflector->getConstantName($language)));
		
		$this->_doc->firstChild->appendChild($captionElem);
		
		return $captionElem;
	}

	public function appendVideoArtworkElement($type, $fileTag)
	{
		$this->setByXpath('video/artwork/@type', $type);
		$this->setByXpath('video/artwork/@path', "/feed/file[@tag='$fileTag']");
	}

	public function appendVideoAssetFileRelationship(array $fieldValues, $fileTag)
	{
		$itemsPaths = array(
			"/feed/file[@tag='$fileTag']",
		);

		$disableFingerprinting = $this->getValueForField($fieldValues, KalturaYouTubeDistributionField::DISABLE_FINGERPRINTING);
		$relatedItemsPaths = array();
		// when fingerprinting is disabled on the cms account, we shouldn't add the asset to the video / file relationship
		if (!$disableFingerprinting)
			$relatedItemsPaths[] = "/feed/asset[@tag='$fileTag']";

		$relatedItemsPaths[] = "/feed/video[@tag='$fileTag']";

		return $this->appendRelationship($itemsPaths, $relatedItemsPaths);
	}

	public function appendRelationship($itemsPaths, $relatedItemsPaths)
	{
		$relationshipDom = $this->_doc->firstChild->appendChild($this->_doc->createElement('relationship'));
		foreach($itemsPaths as $item)
		{
			$relationshipDom->appendChild($this->_doc->createElement('item'))->setAttribute('path', $item);
		}
		foreach($relatedItemsPaths as $relatedItemsPath)
		{
			$relationshipDom->appendChild($this->_doc->createElement('related_item'))->setAttribute('path', $relatedItemsPath);
		}
		return $relationshipDom;
	}

	public function appendKeywordsToElement(DOMElement $element, $keywordsStr)
	{
		$keywords = explode(',', $keywordsStr);
		foreach($keywords as $keyword)
		{
			if (trim($keyword))
				$element->appendChild($this->_doc->createElement('keyword', trim($keyword)));
		}
	}
	public function appendVideoKeywords(array $fieldValues)
	{
		$keywordsStr = $this->getValueForField($fieldValues, KalturaYouTubeDistributionField::MEDIA_KEYWORDS);
		$videoElement = $this->_xpath->query('/feed/video')->item(0);
		$this->appendKeywordsToElement($videoElement, $keywordsStr);
	}

	public function appendAssetKeywords(array $fieldValues)
	{
		$keywordsStr = $this->getValueForField($fieldValues, KalturaYouTubeDistributionField::ASSET_KEYWORDS);
		$videoElement = $this->_xpath->query('/feed/asset')->item(0);
		$this->appendKeywordsToElement($videoElement, $keywordsStr);
	}

	public function setAdParamsByFieldValues(array $fieldValues, $videoTag, $adServerEnabled = false)
	{
		if ($adServerEnabled)
		{
			$this->setByXpath('video_breaks/third_party_ad_server/ad_server_video_id', $this->getValueForField($fieldValues, KalturaYouTubeDistributionField::THIRD_PARTY_AD_SERVER_VIDEO_ID));
			$this->setByXpath('video_breaks/@tag', $videoTag);
			$this->appendRelationship(array("/feed/video[@tag='$videoTag']"), array("/feed/video_breaks[@tag='$videoTag']"));
		}

		$allowPreRolls = $this->getValueForField($fieldValues, KalturaYouTubeDistributionField::ADVERTISING_ALLOW_PRE_ROLL_ADS);
		$allowMidRolls = $this->getValueForField($fieldValues, KalturaYouTubeDistributionField::ADVERTISING_ALLOW_MID_ROLL_ADS);
		$allowPostRolls = $this->getValueForField($fieldValues, KalturaYouTubeDistributionField::ADVERTISING_ALLOW_POST_ROLL_ADS);

		if ($this->isAllowedValue($allowPreRolls))
			$this->setByXpath('ad_policy/instream/prerolls', 'Allow');
		elseif($this->isNotAllowedValue($allowPreRolls))
			$this->setByXpath('ad_policy/instream/prerolls', 'Deny');

		if ($this->isAllowedValue($allowMidRolls))
			$this->setByXpath('ad_policy/instream/midrolls', 'Allow');
		elseif($this->isNotAllowedValue($allowMidRolls))
			$this->setByXpath('ad_policy/instream/midrolls', 'Deny');

		if ($this->isAllowedValue($allowPostRolls))
			$this->setByXpath('ad_policy/instream/postrolls', 'Allow');
		elseif($this->isNotAllowedValue($allowPostRolls))
			$this->setByXpath('ad_policy/instream/postrolls', 'Deny');

		$adsenseForVideoValue = $this->getValueForField($fieldValues, KalturaYouTubeDistributionField::ADVERTISING_ADSENSE_FOR_VIDEO);
		if ($adsenseForVideoValue)
			$this->setByXpath('ad_policy/overlay/adsense_for_video', $adsenseForVideoValue);

		$invideoValue = $this->getValueForField($fieldValues, KalturaYouTubeDistributionField::ADVERTISING_INVIDEO);
		if ($invideoValue)
			$this->setByXpath('ad_policy/overlay/invideo', $invideoValue);

		$instreamStandardValue = $this->getValueForField($fieldValues, KalturaYouTubeDistributionField::ADVERTISING_INSTREAM_STANDARD);
		if ($instreamStandardValue )
			$this->setByXpath('ad_policy/instream/@standard', $instreamStandardValue );

		// append relationship if ad policy was added
		$adPolicyElement = $this->_xpath->query('/feed/ad_policy')->item(0);
		if ($adPolicyElement)
		{
			$adPolicyElement->setAttribute('tag', $videoTag);
			$this->appendRelationship(array("/feed/video[@tag='$videoTag']"), array("/feed/ad_policy[@tag='$videoTag']"));
		}
	}

	public function appendWorldWideOwnership()
	{
		$this->setByXpath('ownership', '');
		$this->appendRelationship(array('/feed/ownership[1]'), array('/feed/asset[1]'));
	}

	public function appendClaimElement(array $fieldValues, $videoTag, $rightAdminType, $policyName)
	{
		$this->_doc->firstChild
			->appendChild($this->_doc->createElement('claim'))
				->setAttribute('type', $this->getValueForField($fieldValues, KalturaYouTubeDistributionField::CLAIM_TYPE))->parentNode
				->setAttribute('video', "/feed/video[@tag='$videoTag']")->parentNode
				->setAttribute('asset', "/feed/asset[@tag='$videoTag']")->parentNode
				->setAttribute('rights_admin', "/feed/rights_admin[@type='$rightAdminType']")->parentNode
				->setAttribute('rights_policy', "/external/rights_policy[@name='$policyName']")->parentNode
		;
	}

	public function appendRightsAdminByFieldValues(array $fieldValues, $videoTag)
	{
		$commercialPolicy = $this->getValueForField($fieldValues, KalturaYouTubeDistributionField::POLICY_COMMERCIAL);
		$ugcPolicy = $this->getValueForField($fieldValues, KalturaYouTubeDistributionField::POLICY_UGC);
		$disableFingerprinting = $this->getValueForField($fieldValues, KalturaYouTubeDistributionField::DISABLE_FINGERPRINTING);

		$rightsAdminType = null;
		if ($commercialPolicy)
		{
			$this->appendRightsAdmin('usage', 'true');
			if (!$disableFingerprinting)
				$this->appendClaimElement($fieldValues, $videoTag, 'usage', $commercialPolicy);
		}

		if($ugcPolicy)
		{
			$this->appendRightsAdmin('match', 'true');
			$itemsPaths = array(
				"/feed/rights_admin[@type='match']",
				"/external/rights_policy[@name='$ugcPolicy']",
			);
			$relatedItemsPaths = array(
				"/feed/asset[@tag='$videoTag']"
			);
			if (!$disableFingerprinting)
				$this->appendRelationship($itemsPaths, $relatedItemsPaths);
		}
	}

	public function appendRightsAdmin($type, $owner)
	{
		$this->_doc->firstChild
			->appendChild($this->_doc->createElement('rights_admin'))
			->setAttribute('type', $type)->parentNode
			->setAttribute('owner', $owner)->parentNode
		;
	}

	public function appendRightsPolicy($name, $tag)
	{
		$this->_doc->firstChild
			->appendChild($this->_doc->createElement('rights_policy'))
				->setAttribute('tag', $tag)->parentNode
				->appendChild($this->_doc->createElement('name', $name))->parentNode
		;
	}


	public function getXml()
	{
		return $this->_doc->saveXML();
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

	private function isAttribute($path)
	{
		return strpos($path, '@') === 0;
	}
}