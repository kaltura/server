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
	 * @param $templateName
	 * @param $distributionProfile
	 */
	public function __construct($templateName, KalturaYouTubeDistributionProfile $distributionProfile, KalturaEntryDistribution $entryDistribution)
	{
		$this->distributionProfile = $distributionProfile;
		
		$xmlTemplate = realpath(dirname(__FILE__) . '/../') . '/xml_templates/' . $templateName;
		$this->doc = new DOMDocument();
		$this->doc->load($xmlTemplate);
		
		$this->xpath = new DOMXPath($this->doc);
		$this->xpath->registerNamespace('media', 'http://search.yahoo.com/mrss');
		$this->xpath->registerNamespace('yt', 'http://www.youtube.com/schemas/yt/0.2');
		
		$this->timestampName = date('Ymd-His') . '_' . time();
		$this->directoryName = '/' . $this->timestampName;
		$this->metadataTempFileName = 'youtube_' . $this->timestampName . '.xml';
		
		if ($entryDistribution->sunrise)
			$this->setStartTime(date('c', $entryDistribution->sunrise));
		else
			$this->setStartTime(date('c', time() - 24*60*60)); // yesterday, to make the video public by default
			
		if ($entryDistribution->sunset)
			$this->setEndTime(date('c', $entryDistribution->sunset));
		
		$this->setNotificationEmail($distributionProfile->notificationEmail);
		$this->setUsername($distributionProfile->username);
		if ($distributionProfile->ownerName)
			$this->setOwnerName($distributionProfile->ownerName);
		$this->setTarget($distributionProfile->target);
		$this->setCategory($distributionProfile->defaultCategory);
		
		// community
		$this->setAllowComments($distributionProfile->allowComments);
		$this->setAllowEmbedding($distributionProfile->allowEmbedding);
		$this->setAllowRatings($distributionProfile->allowRatings);
		$this->setAllowResponses($distributionProfile->allowResponses);
		
		// policies
		$this->setCommercialPolicy($distributionProfile->commercialPolicy);
		$this->setUGCPolicy($distributionProfile->ugcPolicy);
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
	
	public function setAction($value)
	{
		$this->setNodeValue('/rss/channel/item/yt:action', $value);
	}
	
	public function setTarget($value)
	{
		$this->setNodeValue('/rss/channel/item/yt:target', $value);
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
	
	public function setMetadataFromEntry(KalturaMediaEntry $entry)
	{
		$this->setTitle($entry->name);
		$this->setDescription($entry->description);
		$this->setKeywords($entry->tags);
		$this->setWebCustomId($entry->id);
	}
	
}