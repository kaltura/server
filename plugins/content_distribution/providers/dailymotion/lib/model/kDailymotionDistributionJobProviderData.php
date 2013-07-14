<?php
/**
 * @package plugins.dailymotionDistribution
 * @subpackage model.data
 */
class kDailymotionDistributionJobProviderData extends kDistributionJobProviderData
{
	/**
	 * @var string
	 */
	public $videoAssetFilePath;

		/**
	 * @return string $videoAssetFilePath
	 */
	public function getVideoAssetFilePath()
	{
		return $this->videoAssetFilePath;
	}

	/**
	 * @param string $videoAssetFilePath
	 */
	public function setVideoAssetFilePath($videoAssetFilePath)
	{
		$this->videoAssetFilePath = $videoAssetFilePath;
	}
}