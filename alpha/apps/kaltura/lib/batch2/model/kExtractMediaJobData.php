<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kExtractMediaJobData extends kConvartableJobData
{
	/**
	 * @var string
	 */
	private $flavorAssetId;
	
	/**
	 * @var bool
	 */
	public $calculateComplexity;
	
	/**
	 * @var bool
	 */
	public $extractId3Tags;
	
	/**
	 * @var string
	 */
	public $destDataFilePath;
	
	/**
	 * @var int
	 */
	public $detectGOP;
	
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
	 * @return the $calculateComplexity
	 */
	public function getCalculateComplexity()
	{
		return $this->calculateComplexity;
	}
	
	/**
	 * @param $calculateComplexity the $calculateComplexity to set
	 */
	public function setCalculateComplexity($calculateComplexity)
	{
		$this->calculateComplexity = $calculateComplexity;
	}
	
	/**
	 * @return the $calculateComplexity
	 */
	public function getExtractId3Tags()
	{
		return $this->extractId3Tags;
	}
	
	/**
	 * @param $extractId3Tags the $extractId3Tags to set
	 */
	public function setExtractId3Tags($extractId3Tags)
	{
		$this->extractId3Tags = $extractId3Tags;
	}
	
	/**
	 * @return the $destDataFilePath
	 */
	public function getDestDataFilePath()
	{
		return $this->destDataFilePath;
	}
	
	/**
	 * @param $destDataFilePath the $destDataFilePath to set
	 */
	public function setDestDataFilePath($destDataFilePath)
	{
		$this->destDataFilePath = $destDataFilePath;
	}

		/*
		 * When set, the ExtractMedia job should attempt to detect the source file GOP interval 
		 * using the 'detectGOP' value as the max calculated period
		 */
	public function getDetectGOP() { return $this->detectGOP; }
	public function setDetectGOP($v) { $this->detectGOP = $v; }
}
