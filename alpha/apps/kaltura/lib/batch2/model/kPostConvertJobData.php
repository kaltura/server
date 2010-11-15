<?php

/** 
 * @package Core
 * @subpackage Batch
 */
class kPostConvertJobData extends kConvartableJobData
{
	/**
	 * @var string
	 */
	private $flavorAssetId;
	
	/**
	 * Indicates if a thumbnail should be created
	 * 
	 * @var boolean
	 */
	private $createThumb;
	
	/**
	 * The path of the created thumbnail
	 *  
	 * @var string
	 */
	private $thumbPath;
	
	/**
	 * The position of the thumbnail in the media file
	 *  
	 * @var int
	 */
	private $thumbOffset;
	
	/**
	 * The height of the movie, will be used to comapare if this thumbnail is the best we can have
	 *  
	 * @var int
	 */
	private $thumbHeight;
	
	/**
	 * The bit rate of the movie, will be used to comapare if this thumbnail is the best we can have
	 *  
	 * @var int
	 */
	private $thumbBitrate;
	
	
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
	 * @param $thumbOffset the $thumbOffset to set
	 */
	public function setThumbOffset($thumbOffset)
	{
		$this->thumbOffset = $thumbOffset;
	}

	/**
	 * @return the $thumbOffset
	 */
	public function getThumbOffset()
	{
		return $this->thumbOffset;
	}

	/**
	 * @param $thumbPath the $thumbPath to set
	 */
	public function setThumbPath($thumbPath)
	{
		$this->thumbPath = $thumbPath;
	}

	/**
	 * @param $createThumb the $createThumb to set
	 */
	public function setCreateThumb($createThumb)
	{
		$this->createThumb = $createThumb;
	}

	/**
	 * @return the $thumbPath
	 */
	public function getThumbPath()
	{
		return $this->thumbPath;
	}

	/**
	 * @return the $createThumb
	 */
	public function getCreateThumb()
	{
		return $this->createThumb;
	}

	
	/**
	 * @param $flavorAssetId the $flavorAssetId to set
	 */
	public function setFlavorAssetId($flavorAssetId)
	{
		$this->flavorAssetId = $flavorAssetId;
	}

	/**
	 * @return the $flavorAssetId
	 */
	public function getFlavorAssetId()
	{
		return $this->flavorAssetId;
	}

	/**
	 * @return the ready behavior
	 */
	public function getReadyBehavior()
	{
		$flavorParamsOutput = $this->getFlavorParamsOutput();
		
		if(!$flavorParamsOutput)
			$flavorParamsOutput = flavorParamsOutputPeer::retrieveByPK($this->getFlavorParamsOutputId());
			
		if($flavorParamsOutput)
			return $flavorParamsOutput->getReadyBehavior();
			
		return null;
	}

}
