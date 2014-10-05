<?php
/**
 * Akamai-specific live stream configuration object, containing information regarding the protocol and url of the live stream. 
 * 
 * @package Core
 * @subpackage model
 *
 */
class kLiveStreamAkamaiConfiguration extends kLiveStreamConfiguration
{
	/**
	 * @var string
	 */
	protected $akamaiUser;
	
	/**
	 * @var string
	 */
	protected $akamaiPassword;
	
	/**
	 * @var string
	 */
	protected $akamaiStreamId;
	
	
	/**
	 * @return the $akamaiUser
	 */
	public function getAkamaiUser() {
		return $this->akamaiUser;
	}

	/**
	 * @return the $akamaiPassword
	 */
	public function getAkamaiPassword() {
		return $this->akamaiPassword;
	}

	/**
	 * @return the $akamaiStreamId
	 */
	public function getAkamaiStreamId() {
		return $this->akamaiStreamId;
	}

	/**
	 * @param string $akamaiUser
	 */
	public function setAkamaiUser($akamaiUser) {
		$this->akamaiUser = $akamaiUser;
	}

	/**
	 * @param string $akamaiPassword
	 */
	public function setAkamaiPassword($akamaiPassword) {
		$this->akamaiPassword = $akamaiPassword;
	}

	/**
	 * @param string $akamaiStreamId
	 */
	public function setAkamaiStreamId($akamaiStreamId) {
		$this->akamaiStreamId = $akamaiStreamId;
	}

	

}