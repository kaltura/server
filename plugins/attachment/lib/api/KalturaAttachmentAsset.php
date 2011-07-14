<?php
/**
 * @package plugins.attachment
 * @subpackage api.objects
 */
class KalturaAttachmentAsset extends KalturaAsset  
{
	/**
	 * The filename of the attachment asset content
	 * @var string
	 */
	public $filename;
	
	/**
	 * Attachment asset title
	 * @var string
	 */
	public $title;
	
	/**
	 * Friendly description
	 * @var string
	 */
	public $description;
	
	/**
	 * The attachment format
	 * @var KalturaAttachmentType
	 * @filter eq,in
	 * @insertonly
	 */
	public $format;
	
	private static $map_between_objects = array
	(
		"filename",
		"title",
		"description",
		"format" => "containerFormat",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
