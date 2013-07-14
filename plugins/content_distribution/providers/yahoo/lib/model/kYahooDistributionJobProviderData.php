<?php
/**
 * @package plugins.yahooDistribution
 * @subpackage model.data
 */
class kYahooDistributionJobProviderData extends kDistributionJobProviderData
{
	
	/**
	 * @var string
	 */
	private $smallThumbPath;
	
	/**
	 * @var string
	 */
	private $largeThumbPath;
	
	/**
	 * @var string
	 */
	private $videoAssetFilePath;
	
	
	
	/**
	 * @return the $smallThumbPath
	 */
	public function getSmallThumbPath() {
		return $this->smallThumbPath;
	}

	/**
	 * @param string $smallThumbPath
	 */
	public function setSmallThumbPath($smallThumbPath) {
		$this->smallThumbPath = $smallThumbPath;
	}

	/**
	 * @return the $largeThumbPath
	 */
	public function getLargeThumbPath() {
		return $this->largeThumbPath;
	}

	/**
	 * @param string $largeThumbPath
	 */
	public function setLargeThumbPath($largeThumbPath) {
		$this->largeThumbPath = $largeThumbPath;
	}

	/**
	 * @return the $videoAssetFilePath
	 */
	public function getVideoAssetFilePath() {
		return $this->videoAssetFilePath;
	}

	/**
	 * @param string $videoAssetFilePath
	 */
	public function setVideoAssetFilePath($videoAssetFilePath) {
		$this->videoAssetFilePath = $videoAssetFilePath;
	}

	public function __construct(kDistributionJobData $distributionJobData = null)
	{
		parent::__construct($distributionJobData);
	}
}