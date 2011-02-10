<?php
/**
 * @package plugins.youTubeDistribution
 * @subpackage lib
 */
class YouTubeDistributionStatusParser
{
	/**
	 * @var DOMDocument
	 */
	protected $doc;
	
	/**
	 * @var DOMXpath
	 */
	protected $xpath;
	
	/**
	 * @param string $xml
	 */
	public function __construct($xml)
	{
		$this->doc = new DOMDocument();
		$this->doc->loadXML($xml);
		$this->xpath = new DOMXPath($this->doc);
	}
	
	/**
	 * @param string $command
	 * @return string
	 */
	public function getStatusForCommand($command)
	{
		$actionNode = $this->xpath->query("//*/item_status/action/command[text()='".$command."']/..")->item(0);
		if (is_null($actionNode))
			return null;
			
		$statusNode = $this->xpath->query("status", $actionNode)->item(0);
		if (is_null($statusNode))
			return null;
			
		return $statusNode->nodeValue;
	}
	
	/**
	 * @param string $command
	 * @return string
	 */
	public function getStatusDetailForCommand($command)
	{
		$actionNode = $this->xpath->query("//*/item_status/action/command[text()='".$command."']/..")->item(0);
		if (is_null($actionNode))
			return null;

		$statusDetailNode = $this->xpath->query("status_detail", $actionNode)->item(0);
		if (is_null($statusDetailNode))
			return null;
			
		return $statusDetailNode->nodeValue;
	}
	
	/**
	 * @return string
	 */
	public function getRemoteId()
	{
		$videoIdNode = $this->xpath->query("//*/item_status/id[@type='video_id']")->item(0);
		if (is_null($videoIdNode))
			return null;
			
		return $videoIdNode->nodeValue;
	}
}