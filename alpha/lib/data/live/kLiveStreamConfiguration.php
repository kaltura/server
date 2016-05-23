<?php
/**
 * Live stream configuration object, containing information regarding the protocol and url of the live stream. 
 * 
 * @package Core
 * @subpackage model
 *
 */
class kLiveStreamConfiguration
{
	/**
	 * @var string
	 */
	protected $protocol;
	
	/**
	 * @var string
	 */
	protected $url;
	
	/**
	 * @var string
	 */
	protected $backupUrl;
	
	
	/**
	 * @var string
	 */
	protected $publishUrl;
	
	/**
	 * @var string
	 */
	protected $streamName;
	
	/**
	 * @var LiveEntryServerNode
	 */
	protected $primaryLiveEntryServerNode = null;
	
	/**
	 * @var LiveEntryServerNode
	 */
	protected $backupLiveEntryServerNode = null;
	
	/**
	 * @return the $streamName
	 */
	public function getStreamName() {
		return $this->streamName;
	}

	/**
	 * @param string $streamName
	 */
	public function setStreamName($streamName) {
		$this->streamName = $streamName;
	}

	/**
	 * @return the $backupUrl
	 */
	public function getBackupUrl()
	{
		return $this->backupUrl;
	}

	/**
	 * @param string $backupUrl
	 */
	public function setBackupUrl($backupUrl)
	{
		$this->backupUrl = $backupUrl;
	}

	/**
	 * @return string $protocol
	 */
	public function getProtocol() {
		return $this->protocol;
	}

	/**
	 * @param string $protocol
	 */
	public function setProtocol($protocol) {
		$this->protocol = $protocol;
	}
	
	/**
	 * @return string $url
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url) {
		$this->url = $url;
	}

	/**
	 * @return string $publishUrl
	 */
	public function getPublishUrl() {
		return $this->publishUrl;
	}

	/**
	 * @param string $publishUrl
	 */
	public function setPublishUrl($publishUrl) {
		$this->publishUrl = $publishUrl;
	}
	
	public function setPrimaryLiveEntryServerNode($v) {
		$this->primaryLiveEntryServerNode = $v;
	}
	
	public function getPrimaryLiveEntryServerNode() {
		return $this->primaryLiveEntryServerNode;
	}
	
	public function setBackupLiveEntryServerNode($v) {
		$this->backupLiveEntryServerNode = $v;
	}
	
	public function getBackupLiveEntryServerNode() {
		return $this->backupLiveEntryServerNode;
	}
}