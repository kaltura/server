<?php
/**
 * Advanced replacement options
 *
 *
 *
 * @package Core
 * @subpackage model
 */
class kEntryReplacementOptions
{
	private $keepManualThumbnails;
	
	private $pluginOptionItems;
	
	private $keepOldAssets;
	
	/**
	 * @return the $keepManualThumbnails
	 */
	public function getKeepManualThumbnails() 
	{
		return $this->keepManualThumbnails;
	}

	/**
	 * @param field_type $keepManualThumbnails
	 */
	public function setKeepManualThumbnails($keepManualThumbnails) 
	{
		$this->keepManualThumbnails = $keepManualThumbnails;
	}	
	
	/**
	 * @return the $pluginOptionItems
	 */
	public function getPluginOptionItems() 
	{
		return $this->pluginOptionItems;
	}

	/**
	 * @param field_type $pluginOptionItems
	 */
	public function setPluginOptionItems($pluginOptionItems) 
	{
		$this->pluginOptionItems = $pluginOptionItems;
	}
	
	/**
	 * @return the $keepOldAssets
	 */
	public function getKeepOldAssets()
	{
		return $this->keepOldAssets;
	}
	
	/**
	 * @param field_type $keepOldAssets
	 */
	public function setKeepOldAssets($keepOldAssets)
	{
		$this->keepOldAssets = $keepOldAssets;
	}
}
