<?php
/**
 * @package plugins.freewheelGenericDistribution
 * @subpackage model.data
 */
class kFreewheelGenericDistributionJobProviderData extends kDistributionJobProviderData
{
	/**
	 * Demonstrate storing array of paths in the job data
	 * 
	 * @var array<string>
	 */
	public $videoAssetFilePaths;
	
	/**
	 * Demonstrate storing single path in the job data
	 * 
	 * @var string
	 */
	public $thumbAssetFilePath;

	public function __construct(kDistributionJobData $distributionJobData = null)
	{
		parent::__construct($distributionJobData);
	}
	
	/**
	 * @return array<string> $videoAssetFilePaths
	 */
	public function getVideoAssetFilePaths()
	{
		return $this->videoAssetFilePaths;
	}

	/**
	 * @return string $thumbAssetFilePath
	 */
	public function getThumbAssetFilePath()
	{
		return $this->thumbAssetFilePath;
	}

	/**
	 * @param array<string> $videoAssetFilePaths
	 */
	public function setVideoAssetFilePaths(array $videoAssetFilePaths)
	{
		$this->videoAssetFilePaths = $videoAssetFilePaths;
	}

	/**
	 * @param string $thumbAssetFilePath
	 */
	public function setThumbAssetFilePath($thumbAssetFilePath)
	{
		$this->thumbAssetFilePath = $thumbAssetFilePath;
	}
}