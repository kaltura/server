<?php
/**
 * @package plugins.youtube_apiDistribution
 * @subpackage model.data
 */
class kYoutube_apiDistributionJobProviderData extends kDistributionJobProviderData
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
	 * @return string $videoAssetFilePath
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
	 * @param string $videoAssetFilePath
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

	public function __construct(kDistributionJobData $distributionJobData = null)
	{
		parent::__construct($distributionJobData);
	}
}