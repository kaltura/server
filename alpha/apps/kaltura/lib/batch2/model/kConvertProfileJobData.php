<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kConvertProfileJobData extends kJobData
{
	/**
	 * @var string
	 */
	private $inputFileSyncLocalPath;

	/**
	 * @var string
	 */
	private $flavorAssetId;
	
	/**
	 * @var bool
	 */
	private $extractMedia = true;
	
	/**
	 * @return the $extractMedia
	 */
	public function getExtractMedia() {
		return $this->extractMedia;
	}

	/**
	 * @param $extractMedia the $extractMedia to set
	 */
	public function setExtractMedia($extractMedia) {
		$this->extractMedia = $extractMedia;
	}

	/**
	 * @return the $flavorAssetId
	 */
	public function getFlavorAssetId()
	{
		return $this->flavorAssetId;
	}

	/**
	 * @param $flavorAssetId the $flavorAssetId to set
	 */
	public function setFlavorAssetId($flavorAssetId)
	{
		$this->flavorAssetId = $flavorAssetId;
	}
	
	/**
	 * @return the $inputFileSyncLocalPath
	 */
	public function getInputFileSyncLocalPath()
	{
		return $this->inputFileSyncLocalPath;
	}

	/**
	 * @param $inputFileSyncLocalPath the $inputFileSyncLocalPath to set
	 */
	public function setInputFileSyncLocalPath($inputFileSyncLocalPath)
	{
		$this->inputFileSyncLocalPath = $inputFileSyncLocalPath;
	}
}
