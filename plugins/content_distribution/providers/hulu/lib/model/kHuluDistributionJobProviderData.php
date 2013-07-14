<?php
/**
 * @package plugins.huluDistribution
 * @subpackage model.data
 */
class kHuluDistributionJobProviderData extends kDistributionJobProviderData
{
	/**
	 * @var string
	 */
	public $videoAssetFilePath;
	
	/**
	 * @var string
	 */
	public $thumbAssetFilePath;
	
	/**
	 * @var string
	 */
	public $fileBaseName;

	public function __construct(kDistributionJobData $distributionJobData = null)
	{
		parent::__construct($distributionJobData);
	}
	
	/**
	 * @return string $thumbAssetFilePath
	 */
	public function getVideoAssetFilePath()
	{
		return $this->videoAssetFilePath;
	}

	/**
	 * @return string $thumbAssetFilePath
	 */
	public function getThumbAssetFilePath()
	{
		return $this->thumbAssetFilePath;
	}

	/**
	 * @return string $fileBaseName
	 */
	public function getFileBaseName()
	{
		return $this->fileBaseName;
	}

	/**
	 * @param string $thumbAssetFilePath
	 */
	public function setVideoAssetFilePath($videoAssetFilePath)
	{
		$this->videoAssetFilePath = $videoAssetFilePath;
	}

	/**
	 * @param string $thumbAssetFilePath
	 */
	public function setThumbAssetFilePath($thumbAssetFilePath)
	{
		$this->thumbAssetFilePath = $thumbAssetFilePath;
	}

	/**
	 * @param string $fileBaseName
	 */
	public function setFileBaseName($fileBaseName)
	{
		$this->fileBaseName = $fileBaseName;
	}
}