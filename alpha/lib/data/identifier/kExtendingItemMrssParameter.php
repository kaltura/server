<?php
/**
 * @package Core
 * @subpackage model	
 */
class kExtendingItemMrssParameter
{
	/**
	 * @var string
	 */
	protected $xpath;
	
	/**
	 * @var kObjectIdentifier
	 */
	protected $identifier;
	
	/**
	 * @var int
	 */
	protected $extensionMode;
	
	/**
	 * @return the $xpath
	 */
	public function getXpath() {
		return $this->xpath;
	}

	/**
	 * @param string $xpath
	 */
	public function setXpath($xpath) {
		$this->xpath = $xpath;
	}
	
	/**
	 * @return KObjectIdentifier
	 */
	public function getIdentifier() {
		return $this->identifier;
	}

	/**
	 * @param KObjectIdentifier $identifier
	 */
	public function setIdentifier($identifier) {
		$this->identifier = $identifier;
	}
	
	/**
	 * @return int
	 */
	public function getExtensionMode() {
		return $this->extensionMode;
	}

	/**
	 * @param int $extensionMode
	 */
	public function setExtensionMode($extensionMode) {
		$this->extensionMode = $extensionMode;
	}



}