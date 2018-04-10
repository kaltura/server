<?php
/**
 * @package plugins.facebookDistribution
 * @subpackage model.data
 */
class kFacebookDistributionJobProviderData extends kConfigurableDistributionJobProviderData
{
	/**
	 * @var string
	 */
	private $videoAssetFilePath;
	
	/**
	 * @var string
	 */
	private $thumbAssetId;
	
	/**
	 * @var KalturaFacebookCaptionDistributionInfoArray
	 */
	private $captionsInfo;
	
	/**
	 * @var int
	 */
	private $distributionProfileId;

	/**
	 * @return int $distributionProfileId
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
	public function getThumbAssetId()
	{
		return $this->thumbAssetId;
	}

	/**
	 * @param string $thumbAssetId
	 */
	public function setThumbAssetId($thumbAssetId)
	{
		$this->thumbAssetId = $thumbAssetId;
	}	
	
	/**
	 * @return KalturaFacebookCaptionDistributionInfoArray $captionsInfo
	 */
	public function getCaptionsInfo()
	{
		return $this->captionsInfo;
	}

	/**
	 * @param KalturaFacebookCaptionDistributionInfoArray $captionsInfo
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