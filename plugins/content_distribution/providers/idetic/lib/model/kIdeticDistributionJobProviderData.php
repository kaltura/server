<?php
/**
 * @package plugins.ideticDistribution
 * @subpackage model.data
 */
class kIdeticDistributionJobProviderData extends kDistributionJobProviderData
{
	/**
	 * @var string
	 */
	private $thumbnailUrl;
	
	/**
	 * @var string
	 */
	private $flavorAssetUrl;
	
	
	/**
	 * @return the $thumbnailUrl
	 */
	public function getThumbnailUrl() {
		return $this->thumbnailUrl;
	}

	/**
	 * @return the $flavorAssetUrl
	 */
	public function getFlavorAssetUrl() {
		return $this->flavorAssetUrl;
	}

	/**
	 * @param string $thumbnailUrl
	 */
	public function setThumbnailUrl($thumbnailUrl) {
		$this->thumbnailUrl = $thumbnailUrl;
	}

	/**
	 * @param string $flavorAssetUrl
	 */
	public function setFlavorAssetUrl($flavorAssetUrl) {
		$this->flavorAssetUrl = $flavorAssetUrl;
	}

	
	
	
}