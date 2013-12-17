<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kConcatJobData extends kJobData
{
	/**
	 * @var array
	 */
	private $srcFiles;
	
	/**
	 * @var string
	 */
	private $destFilePath;
	
	/**
	 * @var string
	 */
	private $flavorAssetId;
	
	/**
	 * @var float
	 */
	private $offset;
	
	/**
	 * @var float
	 */
	private $duration;
	
	/**
	 * @return array $srcFiles
	 */
	public function getSrcFiles()
	{
		return $this->srcFiles;
	}

	/**
	 * @return string $flavorAssetId
	 */
	public function getFlavorAssetId()
	{
		return $this->flavorAssetId;
	}

	/**
	 * @param array $srcFiles
	 */
	public function setSrcFiles(array $srcFiles)
	{
		$this->srcFiles = $srcFiles;
	}

	/**
	 * @param string $flavorAssetId
	 */
	public function setFlavorAssetId($flavorAssetId)
	{
		$this->flavorAssetId = $flavorAssetId;
	}
	
	/**
	 * @return string $destFilePath
	 */
	public function getDestFilePath()
	{
		return $this->destFilePath;
	}

	/**
	 * @param string $destFilePath
	 */
	public function setDestFilePath($destFilePath)
	{
		$this->destFilePath = $destFilePath;
	}
	
	/**
	 * @return float $offset
	 */
	public function getOffset()
	{
		return $this->offset;
	}

	/**
	 * @return float $duration
	 */
	public function getDuration()
	{
		return $this->duration;
	}

	/**
	 * @param float $offset
	 */
	public function setOffset($offset)
	{
		$this->offset = $offset;
	}

	/**
	 * @param float $duration
	 */
	public function setDuration($duration)
	{
		$this->duration = $duration;
	}
}
