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
	private $shouldCopyMetadata;
	
	/**
	 * @return the $shouldCopyMetadata
	 */
	public function getShouldCopyMetadata() 
	{
		return $this->shouldCopyMetadata;
	}

	/**
	 * @param field_type $shouldCopyMetadata
	 */
	public function setShouldCopyMetadata($shouldCopyMetadata) 
	{
		$this->shouldCopyMetadata = $shouldCopyMetadata;
	}	
}
