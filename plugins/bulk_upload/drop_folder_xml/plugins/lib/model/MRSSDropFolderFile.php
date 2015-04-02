<?php
/**
 * @package plugins.dropFolder
 * @subpackage model
 */
class MRSSDopFolderFile extends DropFolderFile
{
	/**
	 * @var string
	 */
	protected $xmlLocalPath;
	
	/**
	 * @var string
	 */
	protected $hash;
	
	/**
	 * @var bool
	 */
	protected $contentUpdateRequired;
	
	/**
	 * @return string
	 */
	public function getHash() {
		return $this->getFromCustomData('hash');
	}

	/**
	 * @param string $hash
	 */
	public function setHash($hash) {
		$this->putInCustomData('hash', $hash);
	}

	/**
	 * @return string
	 */
	public function getXmlLocalPath() {
		return $this->getFromCustomData('xmlLocalPath');
	}

	/**
	 * @param string $xmlLocalPath
	 */
	public function setXmlLocalPath($xmlLocalPath) {
		$this->putInCustomData('xmlLocalPath', $xmlLocalPath);
	}
	
/**
	 * @return string
	 */
	public function getContentUpdateRequired() {
		return $this->getFromCustomData('contentUpdateRequired',null, true);
	}

	/**
	 * @param string $xmlLocalPath
	 */
	public function setContentUpdateRequired($value) {
		$this->putInCustomData('contentUpdateRequired', $value);
	}

}