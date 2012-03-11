<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kEntryContextDataResult 
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
	private $previewLength = null;
	
	/**
	 * Array of messages as received from the access control rules that invalidated
	 * @var array<string>
	 */
	private $accessControlMessages = array();
	
	/**
	 * Array of actions as received from the access control rules that invalidated
	 * @var array<kAccessControlAction>
	 */
	private $accessControlActions = array();
	
	/**
	 * @return array<string>
	 */
	public function getAccessControlMessages() 
	{
		return $this->accessControlMessages;
	}

	/**
	 * @return array<kAccessControlAction>
	 */
	public function getAccessControlActions() 
	{
		return $this->accessControlActions;
	}

	/**
	 * @param string $accessControlMessage
	 */
	public function addAccessControlMessage($accessControlMessage) 
	{
		$this->accessControlMessages[] = $accessControlMessage;
	}

	/**
	 * @param kAccessControlAction $accessControlAction
	 */
	public function addAccessControlAction(kAccessControlAction $accessControlAction) 
	{
		$this->accessControlActions[] = $accessControlAction;
	}
	
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
}
