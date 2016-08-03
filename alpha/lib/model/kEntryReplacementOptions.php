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
	
	private $items;
	
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
	 * @return the $items
	 */
	public function getItems() 
	{
		return $this->items;
	}

	/**
	 * @param field_type $items
	 */
	public function setItems($items) 
	{
		$this->items = $items;
	}
}
