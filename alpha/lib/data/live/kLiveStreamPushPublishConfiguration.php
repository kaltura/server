<?php
class kLiveStreamPushPublishConfiguration
{
	/**
	 * @var string
	 */
	protected $publishUrl;
	
	/**
	 * @var string
	 */
	protected $backupPublishUrl;
	
	/**
	 * @var string
	 */
	protected $port;
	/**
	 * @return the $publishUrl
	 */
	public function getPublishUrl() {
		return $this->publishUrl;
	}

	/**
	 * @return the $backupPublishUrl
	 */
	public function getBackupPublishUrl() {
		return $this->backupPublishUrl;
	}

	/**
	 * @return the $protocol
	 */
	public function getProtocol() {
		return $this->protocol;
	}

	/**
	 * @return the $port
	 */
	public function getPort() {
		return $this->port;
	}

	/**
	 * @param string $publishUrl
	 */
	public function setPublishUrl($publishUrl) {
		$this->publishUrl = $publishUrl;
	}

	/**
	 * @param string $backupPublishUrl
	 */
	public function setBackupPublishUrl($backupPublishUrl) {
		$this->backupPublishUrl = $backupPublishUrl;
	}

	/**
	 * @param string $protocol
	 */
	public function setProtocol($protocol) {
		$this->protocol = $protocol;
	}

	/**
	 * @param string $port
	 */
	public function setPort($port) {
		$this->port = $port;
	}

}