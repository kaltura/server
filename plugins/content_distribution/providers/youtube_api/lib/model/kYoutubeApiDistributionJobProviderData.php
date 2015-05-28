<?php
/**
 * @package plugins.youtubeApiDistribution
 * @subpackage model.data
 */
class kYoutubeApiDistributionJobProviderData extends kConfigurableDistributionJobProviderData
{
	/**
	 * @var string
	 */
	private $videoAssetFilePath;
	
	/**
	 * @var string
	 */
	private $thumbAssetFilePath;
	
	/**
	 * @var KalturaYouTubeApiCaptionDistributionInfoArray
	 */
	private $captionsInfo;
	
	/**
	 * @var int
	 */
	private $distributionProfileId;

	/**
	 * @return the $distributionProfileId
	 */
	public function getDistributionProfileId()
	{
		return $this->distributionProfileId;
	}

	/**
	 * @param int $distributionProfileId
	 */
	public function setDistributionProfileId($distributionProfileId)
	{
		$this->distributionProfileId = $distributionProfileId;
	}

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
	
	/**
	 * @return string $thumbAssetFilePath
	 */
	public function getThumbAssetFilePath()
	{
		return $this->thumbAssetFilePath;
	}

	/**
	 * @param string $thumbAssetFilePath
	 */
	public function setThumbAssetFilePath($thumbAssetFilePath)
	{
		$this->thumbAssetFilePath = $thumbAssetFilePath;
	}	
	
	/**
	 * @return KalturaYouTubeApiCaptionDistributionInfoArray $captionsInfo
	 */
	public function getCaptionsInfo()
	{
		return $this->captionsInfo;
	}

	/**
	 * @param KalturaYouTubeApiCaptionDistributionInfoArray $captionsInfo
	 */
	public function setCaptionsInfo($captionsInfo)
	{
		$this->captionsInfo = $captionsInfo;
	}	
	
    
	public function __construct(kDistributionJobData $distributionJobData = null)
	{
		parent::__construct($distributionJobData);
	}
}