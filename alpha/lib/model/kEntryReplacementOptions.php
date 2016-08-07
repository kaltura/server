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
}
