<?php
/**
 * RTMP-specific live stream configuration object, containing information regarding the protocol and url of the live stream. 
 * 
 * @package Core
 * @subpackage model
 *
 */
class kLiveStreamRTMPConfiguration extends kLiveStreamConfiguration
{
	/**
	 * @var string
	 */
	protected $userId;
	
	/**
	 * @var string
	 */
	protected $password;
	
	/**
	 * @var string
	 */
	protected $streamName;
	
	/**
	 * @var string
	 */
	protected $applicationName;
	
	/**
	 * @return the $applicationName
	 */
	public function getApplicationName() {
		return $this->applicationName;
	}

	/**
	 * @param string $applicationName
	 */
	public function setApplicationName($applicationName) {
		$this->applicationName = $applicationName;
	}

	/**
	 * @return the $userId
	 */
	public function getUserId() {
		return $this->userId;
	}

	/**
	 * @return the $password
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * @return the $streamName
	 */
	public function getStreamName() {
		return $this->streamName;
	}

	/**
	 * @param string $userId
	 */
	public function setUserId($userId) {
		$this->userId = $userId;
	}

	/**
	 * @param string $password
	 */
	public function setPassword($password) {
		$this->password = $password;
	}

	/**
	 * @param string $streamName
	 */
	public function setStreamName($streamName) {
		$this->streamName = $streamName;
	}

	

}