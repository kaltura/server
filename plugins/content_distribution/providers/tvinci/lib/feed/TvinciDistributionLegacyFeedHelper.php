<?php
/**
 * @package plugins.youTubeDistribution
 * @subpackage lib
 */
class YouTubeDistributionLegacyFeedHelper
{

	/**
	 * @var DOMDocument
	 */
	protected $doc;

	/**
	 * @var DOMXPath
	 */
	protected $xpath;

	/**
	 * @var KalturaYouTubeDistributionProfile
	 */
	protected $distributionProfile;

	/**
	 * The timestamp used for the current directory
	 * @var string
	 */
	protected $timestampName;

	/**
	 * The current directory name
	 * @var string
	 */
	protected $directoryName;

	/**
	 * The current feed name
	 */
	protected $metadataTempFileName;

	/**
	 *
	 * Provider data object
	 * @var KalturaYouTubeDistributionJobProviderData
	 */
	protected $providerData;

	protected $fieldValues;


	/**
	 * @param $templateName
	 * @param $distributionProfile
	 */
	public function __construct($templateName, KalturaYouTubeDistributionProfile $distributionProfile, KalturaYouTubeDistributionJobProviderData $providerData)
	{
		$this->distributionProfile = $distributionProfile;
		$this->providerData = $providerData;
		$this->fieldValues = unserialize($providerData->fieldValues);
		if (!$this->fieldValues) {
			$this->fieldValues = array();
		}

		$xmlTemplate = realpath(dirname(__FILE__) . '/../../') . '/xml_templates/' . $templateName;
		$this->doc = new KDOMDocument();
		$this->doc->load($xmlTemplate);

		$this->xpath = new DOMXPath($this->doc);
		$this->xpath->registerNamespace('media', 'http://search.yahoo.com/mrss');
		$this->xpath->registerNamespace('yt', 'http://www.youtube.com/schemas/yt/0.2');

		$this->timestampName = date('Ymd-His') . '_' . time();
		$this->directoryName = '/' . $this->timestampName;
		if (!empty($distributionProfile->sftpBaseDir)) {
			$this->directoryName = '/' . trim($distributionProfile->sftpBaseDir,'/') . $this->directoryName;
		}
		$this->metadataTempFileName = 'youtube_' . $this->timestampName . '.xml';

		$startTime = $this->getValueForField(KalturaYouTubeDistributionField::START_TIME);
		if ($startTime && intval($startTime))
			$this->setStartTime(date('c', intval($startTime)));

		$endTime = $this->getValueForField(KalturaYouTubeDistributionField::END_TIME);
		if ($endTime && intval($endTime))
			$this->setEndTime(date('c', $endTime));

		$this->setNotificationEmail($this->getValueForField(KalturaYouTubeDistributionField::NOTIFICATION_EMAIL));
		$this->setUsername($this->getValueForField(KalturaYouTubeDistributionField::ACCOUNT_USERNAME));
		$this->setPassword($this->getValueForField(KalturaYouTubeDistributionField::ACCOUNT_PASSWORD));
		$this->setOwnerName($this->getValueForField(KalturaYouTubeDistributionField::OWNER_NAME));
		$this->setTarget($this->getValueForField(KalturaYouTubeDistributionField::TARGET));

		// community
		$this->setAllowComments($this->getValueForField(KalturaYouTubeDistributionField::ALLOW_COMMENTS));
		$this->setAllowEmbedding($this->getValueForField(KalturaYouTubeDistributionField::ALLOW_EMBEDDING));
		$this->setAllowRatings($this->getValueForField(KalturaYouTubeDistributionField::ALLOW_RATINGS));
		$this->setAllowResponses($this->getValueForField(KalturaYouTubeDistributionField::ALLOW_RESPONSES));

		// policies
		$this->setCommercialPolicy($this->getValueForField(KalturaYouTubeDistributionField::POLICY_COMMERCIAL));
		$this->setUGCPolicy($this->getValueForField(KalturaYouTubeDistributionField::POLICY_UGC));

		// urgent reference file
		$this->setUrgentReferenceFile($this->getValueForField(KalturaYouTubeDistributionField::URGENT_REFERENCE_FILE));

		// keep fingerprint
		$this->setKeepFingerprint($this->getValueForField(KalturaYouTubeDistributionField::KEEP_FINGERPRINT));
	}

	private function getValueForField($fieldName)
	{
		if (isset($this->fieldValues[$fieldName])) {
			return $this->fieldValues[$fieldName];
		}
		return null;
	}

	/**
	 * Sends the feed using the sftpMgr connection
	 * @param sftpMgr $sftpManager
	 */
	public function sendFeed(sftpMgr $sftpManager)
	{
		$xml = $this->doc->saveXML();
		$path = $this->directoryName . '/' . $this->metadataTempFileName;
		$sftpManager->filePutContents($path, $xml);
	}

	public function getXml()
	{
		return $this->doc->saveXML();
	}

	/**
	 * Uploads the empty delivery.complete marker file
	 * @param sftpMgr $sftpManager
	 */
	public function setDeliveryComplete(sftpMgr $sftpManager)
	{
		$path = $this->directoryName . '/' . 'delivery.complete';
		$sftpManager->filePutContents($path, '');
	}

	/**
	 * @return string
	 */
	public function getDirectoryName()
	{
		return $this->directoryName;
	}

	/**
	 * @return string
	 */
	public function getMetadataTempFileName()
	{
		return $this->metadataTempFileName;
	}

	/**
	 * @param string $xpath
	 * @param string $value
	 */
	public function setNodeValue($xpath, $value)
	{
		$node = $this->xpath->query($xpath)->item(0);
		if (!is_null($node))
		{
			// if CDATA inside, set the value of CDATA
			if ($node->childNodes->length > 0 && $node->childNodes->item(0)->nodeType == XML_CDATA_SECTION_NODE)
				$node->childNodes->item(0)->nodeValue = $value;
			else
				$node->nodeValue = $value;
		}
	}

	/**
	 * @param string $xpath
	 * @param DOMNode $contextnode
	 */
	public function removeNode($xpath, DOMNode $contextnode = null)
	{
		if ($contextnode)
		{
			$node = $this->xpath->query($xpath, $contextnode)->item(0);
		}
		else
		{
			$node = $this->xpath->query($xpath)->item(0);
		}
		if (!is_null($node))
		{
			$node->parentNode->removeChild($node);
		}
	}

	/**
	 * @param string $xpath
	 * @param string $value
	 */
	public function getNodeValue($xpath)
	{
		$node = $this->xpath->query($xpath)->item(0);
		if (!is_null($node))
			return $node->nodeValue;
		else
			return null;
	}

	public function setNodeValueOrRemove($xpath, $value)
	{
		if ($value)
			kXml::setNodeValue($this->xpath,$xpath, $value);
		else
			$this->removeNode($xpath);
	}

	public function setAction($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/yt:action', $value);
	}

	public function setTarget($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/yt:target', $value);
	}


	public function getTarget()
	{
		return $this->getNodeValue('/rss/channel/item/yt:target');
	}

	public function setNotificationEmail($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/yt:notification_email', $value);
	}

	public function setUsername($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/yt:account/yt:username', $value);
	}

	public function setPassword($value)
	{
		$this->setNodeValueOrRemove('/rss/channel/yt:account/yt:password', $value);
	}

	public function setOwnerName($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/yt:owner_name', $value);
	}

	public function setStartTime($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/yt:start_time', $value);
	}

	public function setEndTime($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/yt:end_time', $value);
	}

	public function setTitle($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/media:title', $value);
	}

	public function setDescription($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/media:content/media:description', $value);
	}

	public function setKeywords($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/media:content/media:keywords', $value);
	}

	public function setDateRecorded($value)
	{
		if ($value)
			$this->setNodeValueOrRemove('/rss/channel/item/yt:date_recorded', date('c', intval($value)));
		else
			$this->setNodeValueOrRemove('/rss/channel/item/yt:date_recorded', null);
	}

	public function setCategory($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/media:content/media:category', $value);
	}

	public function setContentUrl($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/media:content/@url', $value);
	}

	public function setThumbnailUrl($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/media:thumbnail/@url', $value);
	}

	public function setWebCustomId($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/yt:web_metadata/yt:custom_id', $value);
	}

	public function setWebNotes($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/yt:web_metadata/yt:notes', $value);
	}

	public function setAllowComments($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/yt:community/yt:allow_comments', $value);
	}

	public function setAllowResponses($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/yt:community/yt:allow_responses', $value);
	}

	public function setAllowRatings($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/yt:community/yt:allow_ratings', $value);
	}

	public function setAllowEmbedding($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/yt:community/yt:allow_embedding', $value);
	}

	public function setLanguage($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/yt:language', $value);
	}

	public function setRating($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/media:content/media:rating', $value);
	}

	public function setTvCustomId($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/yt:tv_metadata/yt:custom_id', $value);
	}

	public function setTvEpisode($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/yt:tv_metadata/yt:episode', $value);
	}

	public function setTvEpisodeTitle($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/yt:tv_metadata/yt:episode_title', $value);
	}

	public function setTvShowTitle($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/yt:tv_metadata/yt:show_title', $value);
	}

	public function setTvSeason($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/yt:tv_metadata/yt:season', $value);
	}

	public function setTvNotes($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/yt:tv_metadata/yt:notes', $value);
	}

	public function setTvTmsId($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/yt:tv_metadata/yt:tms_id', $value);
	}

	public function setMovieCustomId($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/yt:movie_metadata/yt:custom_id', $value);
	}

	public function setMovieDirector($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/yt:movie_metadata/yt:director', $value);
	}

	public function setMovieNotes($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/yt:movie_metadata/yt:notes', $value);
	}

	public function setMovieTitle($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/yt:movie_metadata/yt:title', $value);
	}

	public function setMovieTmsId($value)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/item/yt:movie_metadata/yt:tms_id', $value);
	}

	public function setCommercialPolicy($value)
	{
		$this->setPolicyById('commercial', $value);
	}

	public function setUGCPolicy($value)
	{
		$this->setPolicyById('ugc', $value);
	}

	public function setPolicyById($id, $value)
	{
		$savedPolicyNode = $this->xpath->query("//*/yt:saved_policies/yt:saved_policy/yt:content_type[text()='".$id."']/..")->item(0);
		$polocyIdNode = $this->xpath->query("yt:saved_policy_id", $savedPolicyNode)->item(0);
		$polocyIdNode->nodeValue = $value;
	}

	public function setUrgentReferenceFile($value)
	{
		$this->setNodeValueOrRemove('/rss/channel/item/yt:urgent_reference_file', $value);
	}

	public function setKeepFingerprint($value)
	{
		$this->setNodeValueOrRemove('/rss/channel/item/yt:keep_fingerprint', $value);
	}

	public function setLocationCountry($value)
	{
		$this->setNodeValueOrRemove('/rss/channel/item/yt:location/yt:country', $value);
	}

	public function setLocationLocationText($value)
	{
		$this->setNodeValueOrRemove('/rss/channel/item/yt:location/yt:location_text', $value);
	}

	public function setLocationZipCode($value)
	{
		$this->setNodeValueOrRemove('/rss/channel/item/yt:location/yt:zip_code', $value);
	}

	public function setDistributionRestrictionRule($value)
	{
		$this->setNodeValueOrRemove('/rss/channel/item/yt:distribution_restriction/yt:distribution_rule', $value);
	}

	public function setVideoId($value)
	{
		$videoIdNode = $this->doc->createElement('yt:id', $value);
		$videoIdNode->setAttribute('type', 'video_id');

		$actionNode = $this->xpath->query('/rss/channel/item/yt:action')->item(0);
		$actionNode->parentNode->insertBefore($videoIdNode, $actionNode);
	}

	public function setMetadataFromEntry()
	{
		$this->setTitle($this->getValueForField(KalturaYouTubeDistributionField::MEDIA_TITLE));
		$this->setDescription($this->getValueForField(KalturaYouTubeDistributionField::MEDIA_DESCRIPTION));
		$this->setRating($this->getValueForField(KalturaYouTubeDistributionField::MEDIA_RATING));
		$this->setKeywords($this->getValueForField(KalturaYouTubeDistributionField::MEDIA_KEYWORDS));
		$this->setDateRecorded($this->getValueForField(KalturaYouTubeDistributionField::DATE_RECORDED));
		$this->setCategory($this->getValueForField(KalturaYouTubeDistributionField::MEDIA_CATEGORY));
		$this->setLanguage($this->getValueForField(KalturaYouTubeDistributionField::LANGUAGE));

		// yt:location
		$this->setLocationCountry($this->getValueForField(KalturaYouTubeDistributionField::LOCATION_COUNTRY));
		$this->setLocationLocationText($this->getValueForField(KalturaYouTubeDistributionField::LOCATION_LOCATION_TEXT));
		$this->setLocationZipCode($this->getValueForField(KalturaYouTubeDistributionField::LOCATION_ZIP_CODE));

		// yt:distribution_restriction
		$this->setDistributionRestrictionRule($this->getValueForField(KalturaYouTubeDistributionField::DISTRIBUTION_RESTRICTION_DISTRIBUTION_RULE));

		// yt:*_metadata
		$this->setWebMetadata();
		$this->setTvMetadata();
		$this->setMovieMetadata();
	}

	protected function setWebMetadata()
	{
		$this->setWebCustomId($this->getValueForField(KalturaYouTubeDistributionField::WEB_METADATA_CUSTOM_ID));
		$this->setWebNotes($this->getValueForField(KalturaYouTubeDistributionField::WEB_METADATA_NOTES));
	}

	protected function setTvMetadata()
	{
		$this->setTvCustomId($this->getValueForField(KalturaYouTubeDistributionField::TV_METADATA_CUSTOM_ID));
		$this->setTvEpisode($this->getValueForField(KalturaYouTubeDistributionField::TV_METADATA_EPISODE));
		$this->setTvEpisodeTitle($this->getValueForField(KalturaYouTubeDistributionField::TV_METADATA_EPISODE_TITLE));
		$this->setTvShowTitle($this->getValueForField(KalturaYouTubeDistributionField::TV_METADATA_SHOW_TITLE));
		$this->setTvSeason($this->getValueForField(KalturaYouTubeDistributionField::TV_METADATA_SEASON));
		$this->setTvNotes($this->getValueForField(KalturaYouTubeDistributionField::TV_METADATA_NOTES));
		$this->setTvTmsId($this->getValueForField(KalturaYouTubeDistributionField::TV_METADATA_TMS_ID));
	}

	protected function setMovieMetadata()
	{
		$this->setMovieCustomId($this->getValueForField(KalturaYouTubeDistributionField::MOVIE_METADATA_CUSTOM_ID));
		$this->setMovieDirector($this->getValueForField(KalturaYouTubeDistributionField::MOVIE_METADATA_DIRECTOR));
		$this->setMovieNotes($this->getValueForField(KalturaYouTubeDistributionField::MOVIE_METADATA_NOTES));
		$this->setMovieTitle($this->getValueForField(KalturaYouTubeDistributionField::MOVIE_METADATA_TITLE));
		$this->setMovieTmsId($this->getValueForField(KalturaYouTubeDistributionField::MOVIE_METADATA_TMS_ID));
	}

	/**
	 * @param array<KalturaString> $currentPlaylists
	 */
	public function setPlaylists($currentPlaylists)
	{
		$newPlaylists = $this->getValueForField(KalturaYouTubeDistributionField::PLAYLISTS);
		KalturaLog::debug('Current playlists: ' . $currentPlaylists);
		KalturaLog::debug('New playlists: ' . $newPlaylists);
		$currentPlaylistsArray = explode(',', $currentPlaylists);
		$newPlaylistsArray = explode(',', $newPlaylists);
		sort($currentPlaylistsArray);
		sort($newPlaylistsArray);

		if (count($currentPlaylists) == 0 && count($newPlaylists) == 0)
			return;

		if (var_export($currentPlaylistsArray, true) === var_export($newPlaylistsArray, true)) // nothing changed
			return;

		$playlistsNode = $this->doc->createElement('yt:playlists');
		$playlistsArray = array();

		// playlists we need to add
		foreach($newPlaylistsArray as $playlist)
		{
			if ($playlist && !in_array($playlist, $currentPlaylistsArray))
			{
				$playlistNode = $this->doc->createElement('yt:playlist');
				$actionNode = $this->doc->createElement('yt:action', 'Insert');
				$nameNode = $this->doc->createElement('yt:name', $playlist);
				$playlistNode->appendChild($actionNode);
				$playlistNode->appendChild($nameNode);
				$playlistsNode->appendChild($playlistNode);
			}
		}

		// playlists we need to remove
		foreach($currentPlaylistsArray as $playlist)
		{
			if ($playlist && !in_array($playlist, $newPlaylistsArray))
			{
				$playlistNode = $this->doc->createElement('yt:playlist');
				$actionNode = $this->doc->createElement('yt:action', 'Delete');
				$nameNode = $this->doc->createElement('yt:name', $playlist);
				$playlistNode->appendChild($actionNode);
				$playlistNode->appendChild($nameNode);
				$playlistsNode->appendChild($playlistNode);
			}
		}

		$itemNode = $this->xpath->query('/rss/channel/item')->item(0);
		$itemNode->appendChild($playlistsNode);

		// finally add the playlist target
		$target = $this->getTarget();
		$targetArray = explode(',', $target);
		$targetArray[] = 'playlist';
		$this->setTarget(implode(',', $targetArray));
		return $newPlaylists;
	}


	public function setAdParams()
	{
		$advertisingNode = $this->doc->createElement('yt:advertising');

		if ($this->distributionProfile->enableAdServer)
		{
			$thirdPartyAdsNode = $this->doc->createElement('yt:third_party_ads');
			$adTypeNode = $this->doc->createElement('yt:ad_type_id', $this->getValueForField(KalturaYouTubeDistributionField::THIRD_PARTY_AD_SERVER_AD_TYPE));
			$partnerIdNode = $this->doc->createElement('yt:partner_id', $this->getValueForField(KalturaYouTubeDistributionField::THIRD_PARTY_AD_SERVER_PARTNER_ID));
			$videoIdNode = $this->doc->createElement('yt:video_id', $this->getValueForField(KalturaYouTubeDistributionField::THIRD_PARTY_AD_SERVER_VIDEO_ID));

			$thirdPartyAdsNode->appendChild($adTypeNode);
			$thirdPartyAdsNode->appendChild($partnerIdNode);
			$thirdPartyAdsNode->appendChild($videoIdNode);
			$advertisingNode->appendChild($thirdPartyAdsNode);
		}

		$allowPreRolls = $this->getValueForField(KalturaYouTubeDistributionField::ADVERTISING_ALLOW_PRE_ROLL_ADS);
		$allowPostRolls = $this->getValueForField(KalturaYouTubeDistributionField::ADVERTISING_ALLOW_POST_ROLL_ADS);

		$allowPreRolls = in_array($allowPreRolls, array('true', 'True', '1'));
		$allowPostRolls = in_array($allowPostRolls, array('true', 'True', '1'));

		if ( $allowPreRolls || $allowPostRolls)
		{
			$inStreamAdsNode = $this->doc->createElement('yt:instream_ads', 'Allow');
			$adSlotsNode = $this->doc->createElement('yt:ad_slots');
			$preRollAdsNode = $this->doc->createElement('yt:has_pre_roll', $allowPreRolls ? 'true' : 'false');
			$postRollAdsNode = $this->doc->createElement('yt:has_post_roll', $allowPostRolls ? 'true' : 'false');

			$adSlotsNode->appendChild($preRollAdsNode);
			$adSlotsNode->appendChild($postRollAdsNode);
			$advertisingNode->appendChild($inStreamAdsNode);
			$advertisingNode->appendChild($adSlotsNode);
		}

		// yt:adsense_for_video
		$adsenseForVideoValue = $this->getValueForField(KalturaYouTubeDistributionField::ADVERTISING_ADSENSE_FOR_VIDEO);
		if ($adsenseForVideoValue)
		{
			$adsenseForVideoNode = $this->doc->createElement('yt:adsense_for_video', $adsenseForVideoValue);
			$advertisingNode->appendChild($adsenseForVideoNode);
		}

		// yt:invideo
		$invideoValue = $this->getValueForField(KalturaYouTubeDistributionField::ADVERTISING_INVIDEO);
		if ($invideoValue)
		{
			$invideoNode = $this->doc->createElement('yt:invideo', $invideoValue);
			$advertisingNode->appendChild($invideoNode);
		}

		$itemNode = $this->xpath->query('/rss/channel/item')->item(0);
		$itemNode->appendChild($advertisingNode);

		return true;
	}

}