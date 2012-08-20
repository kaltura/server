<?php
/**
 * Used to ingest string content.
 * 
 * @package api
 * @subpackage objects
 */
class KalturaStringResource extends KalturaContentResource 
{
	/**
	 * Textual content
	 * @var string
	 */
	public $content;

	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new kLocalFileResource();
			
		file_put_contents($fname = tempnam(myContentStorage::getFSUploadsPath(), "KFR"), $content);
		$object_to_fill->setLocalFilePath($fname);
		$object_to_fill->setSourceType(entry::ENTRY_MEDIA_SOURCE_TEXT);

		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
