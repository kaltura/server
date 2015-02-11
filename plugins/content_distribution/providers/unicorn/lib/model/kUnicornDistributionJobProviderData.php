<?php
/**
 * @package plugins.unicornDistribution
 * @subpackage model.data
 */
class kUnicornDistributionJobProviderData extends kDistributionJobProviderData
{
	const CUSTOM_DATA_FLAVOR_ASSET_OLD_VERSION = 'flavorAssetOldVersion';
	
	/**
	 * The Catalog GUID the video is in or will be ingested into.
	 * 
	 * @var string
	 */
	protected $catalogGuid;
	
	/**
	 * The Title assigned to the video. The Foreign Key will be used if no title is provided.
	 * 
	 * @var string
	 */
	protected $title;
	
	/**
	 * Indicates that the media content changed and therefore the job should wait for HTTP callback notification to be closed.
	 * 
	 * @var bool
	 */
	protected $mediaChanged;
	
	/**
	 * Flavor asset version.
	 * 
	 * @var string
	 */
	protected $flavorAssetVersion;
	
	/**
	 * @return string
	 */
	public function getCatalogGuid()
	{
		return $this->catalogGuid;
	}
	
	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}
	
	/**
	 * @param string $catalogGuid
	 */
	public function setCatalogGuid($catalogGuid)
	{
		$this->catalogGuid = $catalogGuid;
	}
	
	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	/**
	 * @return the $mediaChanged
	 */
	public function getMediaChanged()
	{
		return $this->mediaChanged;
	}

	/**
	 * @return the $flavorAssetVersion
	 */
	public function getFlavorAssetVersion()
	{
		return $this->flavorAssetVersion;
	}

	/**
	 * @param bool $mediaChanged
	 */
	public function setMediaChanged($mediaChanged)
	{
		$this->mediaChanged = $mediaChanged;
	}

	/**
	 * @param string $flavorAssetVersion
	 */
	public function setFlavorAssetVersion($flavorAssetVersion)
	{
		$this->flavorAssetVersion = $flavorAssetVersion;
	}
}