<?php
/**
 * @package plugins.unicornDistribution
 * @subpackage model.data
 */
class kUnicornDistributionJobProviderData extends kDistributionJobProviderData
{
	/**
	 * The Catalog GUID the video is in or will be ingested into.
	 * 
	 * @var string
	 */
	protected $catalogGUID;
	
	/**
	 * The Title assigned to the video. The Foreign Key will be used if no title is provided.
	 * 
	 * @var string
	 */
	protected $title;
	
	/**
	 * @return string
	 */
	public function getCatalogGUID()
	{
		return $this->catalogGUID;
	}
	
	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}
	
	/**
	 * @param string $catalogGUID
	 */
	public function setCatalogGUID($catalogGUID)
	{
		$this->catalogGUID = $catalogGUID;
	}
	
	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}
}