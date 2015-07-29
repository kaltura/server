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

	private static $map_between_objects = array
	(
		"filename",
		"title",
		"format" => "containerFormat",
		"status",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

    public function toObject($object_to_fill = null, $props_to_skip = array())
    {
        if (!$object_to_fill)
        {
            $className = AttachmentPlugin::getObjectClass('asset', AttachmentPlugin::getAssetTypeCoreValue(AttachmentAssetType::ATTACHMENT));
            $object_to_fill = new $className();
        }

        return parent::toObject($object_to_fill, $props_to_skip);
    }

    public function getInstance()
    {
        $className = get_class($this);
        return new $className;
    }

}
