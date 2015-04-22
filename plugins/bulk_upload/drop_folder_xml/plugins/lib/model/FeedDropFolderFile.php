<?php
/**
 * @package plugins.FeedDropFolder
 * @subpackage model
 */
class FeedDropFolderFile extends DropFolderFile
{
	/**
	 * @return the $feedXmlPath
	 */
	public function getFeedXmlPath() {
		return $this->getFromCustomData('feedXmlPath');
	}

	/**
	 * @param string $feedXmlPath
	 */
	public function setFeedXmlPath($v) {
		$this->putInCustomData('feedXmlPath', $v);
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