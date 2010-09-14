<?php

/** 
 * @package Core
 * @subpackage Batch
 */
class kConvertProfileJobData
{
	/**
	 * @var string
	 */
	private $inputFileSyncLocalPath;
	
	/**
	 * The height of last created thumbnail, will be used to comapare if this thumbnail is the best we can have
	 *  
	 * @var int
	 */
	private $thumbHeight;
	
	/**
	 * The bit rate of last created thumbnail, will be used to comapare if this thumbnail is the best we can have
	 *  
	 * @var int
	 */
	private $thumbBitrate;
	

	/**
	 * @var string
	 */
	private $flavorAssetId;
	

	/**
	 * @var bool
	 */
	private $createThumb = true;
	

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
	 * @return the $createThumb
	 */
	public function getCreateThumb() {
		return $this->createThumb;
	}

	/**
	 * @param $createThumb the $createThumb to set
	 */
	public function setCreateThumb($createThumb) {
		$this->createThumb = $createThumb;
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
	 * @param $thumbBitrate the $thumbBitrate to set
	 */
	public function setThumbBitrate($thumbBitrate)
	{
		$this->thumbBitrate = $thumbBitrate;
	}

	/**
	 * @param $thumbHeight the $thumbHeight to set
	 */
	public function setThumbHeight($thumbHeight)
	{
		$this->thumbHeight = $thumbHeight;
	}

	/**
	 * @return the $thumbBitrate
	 */
	public function getThumbBitrate()
	{
		return $this->thumbBitrate;
	}

	/**
	 * @return the $thumbHeight
	 */
	public function getThumbHeight()
	{
		return $this->thumbHeight;
	}


	/**
	 * @param $inputFileSyncLocalPath the $inputFileSyncLocalPath to set
	 */
	public function setInputFileSyncLocalPath($inputFileSyncLocalPath)
	{
		$this->inputFileSyncLocalPath = $inputFileSyncLocalPath;
	}
}

?>