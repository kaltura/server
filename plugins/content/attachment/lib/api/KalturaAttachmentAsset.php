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
	 * The attachment format
	 * @var KalturaAttachmentType
	 * @filter eq,in
	 */
	public $format;
	
	/**
	 * The status of the asset
	 * 
	 * @var KalturaAttachmentAssetStatus
	 * @readonly 
	 * @filter eq,in,notin
	 */
	public $status;

	/**
	 * The language of the attachment
	 * @var KalturaLanguageCode
	 */
	public $language;

	private static $map_between_objects = array
	(
		"filename",
		"title",
		"format" => "containerFormat",
		"status",
		"language",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

    public function getCoreInstance()
    {
        return AttachmentPlugin::getObjectClass('asset', AttachmentPlugin::getAssetTypeCoreValue(AttachmentAssetType::ATTACHMENT));
    }
}
