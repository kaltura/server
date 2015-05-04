<?php
/**
 * @package plugins.FeedDropFolder
 * @subpackage model
 */
class FeedItemInfo
{
	/**
	 * @var string
	 */
	protected $itemXPath;
	
	/**
	 * @var string
	 */
	protected $itemPublishDateXPath;
	
	/**
	 * @var string
	 */
	protected $itemUniqueIdentifierXPath;
	
	/**
	 * @var string
	 */
	protected $itemContentFileSizeXPath;
	
	/**
	 * @var string
	 */
	protected $itemContentUrlXPath;
	
	/**
	 * @var string
	 */
	protected $itemHashXPath;
	
	/**
	 * @var string
	 */
	protected $itemContentXpath;
	
	/**
	 * @var string
	 */
	protected $contentBitrateAttributeName;
	
	/**
	 * @var string
	 */
	protected $itemContentBitrateXPath;
	
	/**
	 * @return the $itemContentBitrateXPath
	 */
	public function getItemContentBitrateXPath() {
		return $this->itemContentBitrateXPath;
	}

	/**
	 * @param string $itemContentBitrateXPath
	 */
	public function setItemContentBitrateXPath($itemContentBitrateXPath) {
		$this->itemContentBitrateXPath = $itemContentBitrateXPath;
	}

	/**
	 * @return the $contentBitrateAttributeName
	 */
	public function getContentBitrateAttributeName() {
		return $this->contentBitrateAttributeName;
	}

	/**
	 * @param string $contentBitrateAttributeName
	 */
	public function setContentBitrateAttributeName($contentBitrateAttributeName) {
		$this->contentBitrateAttributeName = $contentBitrateAttributeName;
	}

	/**
	 * @return the $itemContentXpath
	 */
	public function getItemContentXpath() {
		return $this->itemContentXpath;
	}

	/**
	 * @param string $itemContentXpath
	 */
	public function setItemContentXpath($itemContentXpath) {
		$this->itemContentXpath = $itemContentXpath;
	}

	/**
	 * @return the $itemXPath
	 */
	public function getItemXPath() {
		return $this->itemXPath;
	}

	/**
	 * @return the $itemPublishDateXPath
	 */
	public function getItemPublishDateXPath() {
		return $this->itemPublishDateXPath;
	}

	/**
	 * @return the $itemUniqueIdentifierXPath
	 */
	public function getItemUniqueIdentifierXPath() {
		return $this->itemUniqueIdentifierXPath;
	}

	/**
	 * @return the $itemContentFileSizeXPath
	 */
	public function getItemContentFileSizeXPath() {
		return $this->itemContentFileSizeXPath;
	}

	/**
	 * @return the $itemContentUrlXPath
	 */
	public function getItemContentUrlXPath() {
		return $this->itemContentUrlXPath;
	}

	/**
	 * @return the $itemHashXPath
	 */
	public function getItemHashXPath() {
		return $this->itemHashXPath;
	}

	/**
	 * @param string $itemXPath
	 */
	public function setItemXPath($itemXPath) {
		$this->itemXPath = $itemXPath;
	}

	/**
	 * @param string $itemPublishDateXPath
	 */
	public function setItemPublishDateXPath($itemPublishDateXPath) {
		$this->itemPublishDateXPath = $itemPublishDateXPath;
	}

	/**
	 * @param string $itemUniqueIdentifierXPath
	 */
	public function setItemUniqueIdentifierXPath($itemUniqueIdentifierXPath) {
		$this->itemUniqueIdentifierXPath = $itemUniqueIdentifierXPath;
	}

	/**
	 * @param string $itemContentFileSizeXPath
	 */
	public function setItemContentFileSizeXPath($itemContentFileSizeXPath) {
		$this->itemContentFileSizeXPath = $itemContentFileSizeXPath;
	}

	/**
	 * @param string $itemContentUrlXPath
	 */
	public function setItemContentUrlXPath($itemContentUrlXPath) {
		$this->itemContentUrlXPath = $itemContentUrlXPath;
	}

	/**
	 * @param string $itemHashXPath
	 */
	public function setItemHashXPath($itemHashXPath) {
		$this->itemHashXPath = $itemHashXPath;
	}

}