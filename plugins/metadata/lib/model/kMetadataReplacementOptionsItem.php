<?php
/**
 * Advanced metadata replacement options
 *
 *
 *
 * @package plugins.metadata
 * @subpackage model
 */
class kMetadataReplacementOptionsItem
{
	private $shouldTransferMetadata;
	
	/**
	 * @return the $shouldTransferMetadata
	 */
	public function getShouldTransferMetadata() 
	{
		return $this->shouldTransferMetadata;
	}

	/**
	 * @param field_type $shouldTransferMetadata
	 */
	public function setShouldTransferMetadata($shouldTransferMetadata) 
	{
		$this->shouldTransferMetadata = $shouldTransferMetadata;
	}	
}
