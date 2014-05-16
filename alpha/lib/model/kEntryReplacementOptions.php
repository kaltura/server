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
}