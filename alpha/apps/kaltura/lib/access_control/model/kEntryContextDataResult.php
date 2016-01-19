<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kEntryContextDataResult extends kContextDataResult
{
	/**
	 * @var bool
	 * @deprecated
	 */
	private $isSiteRestricted = false;
	
	/**
	 * @var bool
	 * @deprecated
	 */
	private $isCountryRestricted = false;
	
	/**
	 * @var bool
	 * @deprecated
	 */
	private $isSessionRestricted = false;
	
	/**
	 * @var bool
	 * @deprecated
	 */
	private $isIpAddressRestricted = false;
	
	/**
	 * @var bool
	 * @deprecated
	 */
	private $isUserAgentRestricted = false;
	
	/**
	 * @var int
	 * @deprecated
	 */
	private $previewLength = -1; //changed to -1 for backward compatibility
	
	/**
	 * @var int
	 */
	private $msDuration = 0;
	
	/**
	 * @return bool
	 * @deprecated
	 */
	public function getIsSiteRestricted() {
		return $this->isSiteRestricted;
	}

	/**
	 * @return bool
	 * @deprecated
	 */
	public function getIsCountryRestricted() {
		return $this->isCountryRestricted;
	}

	/**
	 * @return bool
	 * @deprecated
	 */
	public function getIsSessionRestricted() {
		return $this->isSessionRestricted;
	}

	/**
	 * @return bool
	 * @deprecated
	 */
	public function getIsIpAddressRestricted() {
		return $this->isIpAddressRestricted;
	}

	/**
	 * @return bool
	 * @deprecated
	 */
	public function getIsUserAgentRestricted() {
		return $this->isUserAgentRestricted;
	}

	/**
	 * @return int
	 * @deprecated
	 */
	public function getPreviewLength() {
		return $this->previewLength;
	}

	/**
	 * @return int
	 */
	public function getMsDuration() {
		return $this->msDuration;
	}
	
	/**
	 * @param bool $isSiteRestricted
	 * @deprecated
	 */
	public function setIsSiteRestricted($isSiteRestricted) {
		$this->isSiteRestricted = $isSiteRestricted;
	}

	/**
	 * @param bool $isCountryRestricted
	 * @deprecated
	 */
	public function setIsCountryRestricted($isCountryRestricted) {
		$this->isCountryRestricted = $isCountryRestricted;
	}

	/**
	 * @param bool $isSessionRestricted
	 * @deprecated
	 */
	public function setIsSessionRestricted($isSessionRestricted) {
		$this->isSessionRestricted = $isSessionRestricted;
	}

	/**
	 * @param bool $isIpAddressRestricted
	 * @deprecated
	 */
	public function setIsIpAddressRestricted($isIpAddressRestricted) {
		$this->isIpAddressRestricted = $isIpAddressRestricted;
	}

	/**
	 * @param bool $isUserAgentRestricted
	 * @deprecated
	 */
	public function setIsUserAgentRestricted($isUserAgentRestricted) {
		$this->isUserAgentRestricted = $isUserAgentRestricted;
	}

	/**
	 * @param int $previewLength
	 * @deprecated
	 */
	public function setPreviewLength($previewLength) {
		$this->previewLength = $previewLength;
	}

	/**
	 * @param int $msDuration
	 */
	public function setMsDuration($msDuration) {
		$this->msDuration = $msDuration;
	}
}
