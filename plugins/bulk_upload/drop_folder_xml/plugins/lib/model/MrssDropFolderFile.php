<?php
/**
 * @package plugins.DropFolderMrss
 * @subpackage model
 */
class MrssDropFolderFile extends DropFolderFile
{
	/**
	 * @var string
	 */
	protected $hash;
	
	/**
	 * @var string
	 */
	protected $mrssXmlPath;
	
	/**
	 * @return the $mrssXmlPath
	 */
	public function getMrssXmlPath() {
		return $this->getFromCustomData('mrssXmlPath');
	}

	/**
	 * @param string $mrssXmlPath
	 */
	public function setMrssXmlPath($mrssXmlPath) {
		$this->putInCustomData('mrssXmlPath', $mrssXmlPath);
	}

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
}