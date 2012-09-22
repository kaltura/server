<?php
/**
 * @package plugins.attUverseDistribution
 * @subpackage model.data
 */
class kAttUverseDistributionJobProviderData extends kDistributionJobProviderData
{
		
	/**
	 * @var string
	 */
	public $assetLocalPaths;
	
	/**
	 * @var string
	 */
	public $thumbLocalPaths;
	
	/**
	 * @var string
	 */
	public $captionLocalPaths;
	
	/**
	 * The remote URL of the video asset that was distributed
	 * 
	 * @var string
	 */
	public $remoteAssetFileUrls;
	
	/**
	 * The remote URL of the thumbnail asset that was distributed
	 * 
	 * @var string
	 */
	public $remoteThumbnailFileUrls;
	
	/**
	 * The remote URL of the caption asset that was distributed
	 * 
	 * @var string
	 */
	public $remoteCaptionFileUrls;
	
	
	public function __construct(kDistributionJobData $distributionJobData = null)
	{
		parent::__construct($distributionJobData);
	}
	
	
	/**
	 * @return the $assetLocalPaths
	 */
	public function getAssetLocalPaths() {
		return $this->assetLocalPaths;
	}

	/**
	 * @param string $assetLocalPaths
	 */
	public function setAssetLocalPaths($assetLocalPaths) {
		$this->assetLocalPaths = $assetLocalPaths;
	}

	/**
	 * @return the $thumbLocalPaths
	 */
	public function getThumbLocalPaths() {
		return $this->thumbLocalPaths;
	}

	/**
	 * @param string $thumbLocalPaths
	 */
	public function setThumbLocalPaths($thumbLocalPaths) {
		$this->thumbLocalPaths = $thumbLocalPaths;
	}

	/**
	 * @return the $remoteAssetFileUrls
	 */
	public function getRemoteAssetFileUrls() {
		return $this->remoteAssetFileUrls;
	}

	/**
	 * @param string $remoteAssetFileUrls
	 */
	public function setRemoteAssetFileUrls($remoteAssetFileUrls) {
		$this->remoteAssetFileUrls = $remoteAssetFileUrls;
	}

	/**
	 * @return the $remoteThumbnailFileUrls
	 */
	public function getRemoteThumbnailFileUrls() {
		return $this->remoteThumbnailFileUrls;
	}

	/**
	 * @param string $remoteThumbnailFileUrls
	 */
	public function setRemoteThumbnailFileUrls($remoteThumbnailFileUrls) {
		$this->remoteThumbnailFileUrls = $remoteThumbnailFileUrls;
	}
	
	/**
	 * @return the $captionLocalPaths
	 */
	public function getCaptionLocalPaths() {
		return $this->captionLocalPaths;
	}

	/**
	 * @param string $captionLocalPaths
	 */
	public function setCaptionLocalPaths($captionLocalPaths) {
		$this->captionLocalPaths = $captionLocalPaths;
	}
	
	/**
	 * @return the $remoteCaptionFileUrls
	 */
	public function getRemoteCaptionFileUrls() {
		return $this->remoteCaptionFileUrls;
	}

	/**
	 * @param string $remoteCaptionFileUrls
	 */
	public function setRemoteCaptionFileUrls($remoteCaptionFileUrls) {
		$this->remoteCaptionFileUrls = $remoteCaptionFileUrls;
	}

	
	
}