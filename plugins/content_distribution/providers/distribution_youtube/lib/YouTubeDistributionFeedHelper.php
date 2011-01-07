<?php
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
	public function __construct($templateName, KalturaYouTubeDistributionProfile $distributionProfile)
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
		
		$this->setNotificationEmail($distributionProfile->notificationEmail);
		$this->setUsername($distributionProfile->username);
		$this->setOwnerName('Kaltura'); // FIXME!!!
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
	
	public function setContentUrl($value)
	{
		$this->setNodeValue('/rss/channel/item/media:content/@url', $value);
	}

	public function setWebCustomId($value)
	{
		$this->setNodeValue('/rss/channel/item/yt:web_metadata/yt:custom_id', $value);
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