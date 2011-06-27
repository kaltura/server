<?php
/**
 * @package plugins.youTubeDistribution
 * @subpackage lib
 */
class YouTubeDistributionFeedHelper
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
		
		$xmlTemplate = realpath(dirname(__FILE__) . '/../') . '/xml_templates/' . $templateName;
		$this->doc = new DOMDocument();
		$this->doc->load($xmlTemplate);
		
		$this->xpath = new DOMXPath($this->doc);
		$this->xpath->registerNamespace('media', 'http://search.yahoo.com/mrss');
		$this->xpath->registerNamespace('yt', 'http://www.youtube.com/schemas/yt/0.2');
		
		$this->timestampName = date('Ymd-His') . '_' . time();
		$this->directoryName = '/' . $this->timestampName;
		$this->metadataTempFileName = 'youtube_' . $this->timestampName . '.xml';
		
		$startTime = $this->getValueForField(KalturaYouTubeDistributionField::START_TIME);
		if (is_null($startTime)) {
		    $startTime = time() - 24*60*60;  // yesterday, to make the video public by default
		}
		$this->setStartTime(date('c', intval($startTime)));
		
		$endTime = $this->getValueForField(KalturaYouTubeDistributionField::END_TIME);
		if ($endTime && intval($endTime)) {
            $this->setEndTime(date('c', $endTime));
		}		
		
		$this->setNotificationEmail($this->getValueForField(KalturaYouTubeDistributionField::NOTIFICATION_EMAIL));
		$this->setUsername($this->getValueForField(KalturaYouTubeDistributionField::ACCOUNT_USERNAME));
		$this->setOwnerName($this->getValueForField(KalturaYouTubeDistributionField::OWNER_NAME));
		$this->setTarget($this->getValueForField(KalturaYouTubeDistributionField::TARGET));
		$this->setCategory($this->getValueForField(KalturaYouTubeDistributionField::MEDIA_CATEGORY));
		
		// community
		$this->setAllowComments($this->getValueForField(KalturaYouTubeDistributionField::ALLOW_COMMENTS));
		$this->setAllowEmbedding($this->getValueForField(KalturaYouTubeDistributionField::ALLOW_EMBEDDING));
		$this->setAllowRatings($this->getValueForField(KalturaYouTubeDistributionField::ALLOW_RATINGS));
		$this->setAllowResponses($this->getValueForField(KalturaYouTubeDistributionField::ALLOW_RESPONSES));
		
		// policies
		$this->setCommercialPolicy($this->getValueForField(KalturaYouTubeDistributionField::POLICY_COMMERCIAL));
		$this->setUGCPolicy($this->getValueForField(KalturaYouTubeDistributionField::POLICY_UGC));
		$this->setLanguage($this->getValueForField(KalturaYouTubeDistributionField::LANGUAGE));		
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
	
	public function setAction($value)
	{
		$this->setNodeValue('/rss/channel/item/yt:action', $value);
	}
	
	public function setTarget($value)
	{
		$this->setNodeValue('/rss/channel/item/yt:target', $value);
	}
	

	public function getTarget()
	{
		return $this->getNodeValue('/rss/channel/item/yt:target');
	}
	
	public function setNotificationEmail($value)
	{
		$this->setNodeValue('/rss/channel/yt:notification_email', $value);
	}
	
	public function setUsername($value)
	{
		$this->setNodeValue('/rss/channel/yt:account/yt:username', $value);
	}
	
	public function setOwnerName($value)
	{
		$this->setNodeValue('/rss/channel/yt:owner_name', $value);
	}
	
	public function setStartTime($value)
	{
		$this->setNodeValue('/rss/channel/item/yt:start_time', $value);
	}
	
	public function setEndTime($value)
	{
		$this->setNodeValue('/rss/channel/item/yt:end_time', $value);
	}
	
	public function setTitle($value)
	{
		$this->setNodeValue('/rss/channel/item/media:title', $value);
	}
	
	public function setDescription($value)
	{
		$this->setNodeValue('/rss/channel/item/media:content/media:description', $value);
	}
	
	public function setKeywords($value)
	{
		$this->setNodeValue('/rss/channel/item/media:content/media:keywords', $value);
	}
	
	public function setCategory($value)
	{
		$this->setNodeValue('/rss/channel/item/media:content/media:category', $value);
	}
	
	public function setContentUrl($value)
	{
		$this->setNodeValue('/rss/channel/item/media:content/@url', $value);
	}
	
	public function setThumbnailUrl($value)
	{
		$this->setNodeValue('/rss/channel/item/media:thumbnail/@url', $value);
	}

	public function setWebCustomId($value)
	{
		$this->setNodeValue('/rss/channel/item/yt:web_metadata/yt:custom_id', $value);
	}
	
	public function setAllowComments($value)
	{
		$this->setNodeValue('/rss/channel/item/yt:community/yt:allow_comments', $value);
	}
	
	public function setAllowResponses($value)
	{
		$this->setNodeValue('/rss/channel/item/yt:community/yt:allow_responses', $value);
	}
	
	public function setAllowRatings($value)
	{
		$this->setNodeValue('/rss/channel/item/yt:community/yt:allow_ratings', $value);
	}
	
	public function setAllowEmbedding($value)
	{
		$this->setNodeValue('/rss/channel/item/yt:community/yt:allow_embedding', $value);
	}
	
	public function setLanguage($value)
	{
		$this->setNodeValue('/rss/channel/item/yt:language', $value);
	}
	
	public function setRating($value)
	{
		$this->setNodeValue('/rss/channel/item/media:content/media:rating', $value);
	}	
	
	public function setTvCustomId($value)
	{
		$this->setNodeValue('/rss/channel/item/yt:tv_metadata/yt:custom_id', $value);
	}
	
	public function setTvEpisode($value)
	{
		$this->setNodeValue('/rss/channel/item/yt:tv_metadata/yt:episode', $value);
	}
	
	public function setTvEpisodeTitle($value)
	{
		$this->setNodeValue('/rss/channel/item/yt:tv_metadata/yt:episode_title', $value);
	}
	
	public function setTvShowTitle($value)
	{
		$this->setNodeValue('/rss/channel/item/yt:tv_metadata/yt:show_title', $value);
	}
	
	public function setTvSeason($value)
	{
		$this->setNodeValue('/rss/channel/item/yt:tv_metadata/yt:season', $value);
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
		$this->setWebCustomId($this->getValueForField(KalturaYouTubeDistributionField::WEB_METADATA_CUSTOM_ID));
		$this->setDescription($this->getValueForField(KalturaYouTubeDistributionField::MEDIA_DESCRIPTION));
		$this->setRating($this->getValueForField(KalturaYouTubeDistributionField::MEDIA_RATING));
		$this->setKeywords($this->getValueForField(KalturaYouTubeDistributionField::MEDIA_KEYWORDS));
		$this->setTvMetadata();
	}
	
	protected function setTvMetadata()
	{
	    $this->setTvCustomId($this->getValueForField(KalturaYouTubeDistributionField::TV_METADATA_CUSTOM_ID));
	    $this->setTvEpisode($this->getValueForField(KalturaYouTubeDistributionField::TV_METADATA_EPISODE));
	    $this->setTvEpisodeTitle($this->getValueForField(KalturaYouTubeDistributionField::TV_METADATA_EPISODE_TITLE));
	    $this->setTvShowTitle($this->getValueForField(KalturaYouTubeDistributionField::TV_METADATA_SHOW_TITLE));
	    $this->setTvSeason($this->getValueForField(KalturaYouTubeDistributionField::TV_METADATA_SEASON));
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
	
}