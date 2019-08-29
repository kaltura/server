<?php
/**
 * @package plugins.caption
 * @subpackage api.objects
 */
class KalturaConvertCaptionAssetJobData extends KalturaJobData
{
    /**
     * @var string
     */
    public $captionAssetId;

    /**
     * @var string
     */
    public $fileLocation;

    /**
     * @var string
     */
    public $fileEncryptionKey;

    /**
     * @var string
     */
    public $fromType;

    /**
     * @var string
     */
    public $toType;

    private static $map_between_objects = array
    (
        "captionAssetId",
        "fileLocation",
        "fileEncryptionKey",
        "fromType",
        "toType"
    );

    /* (non-PHPdoc)
     * @see KalturaObject::getMapBetweenObjects()
     */
    public function getMapBetweenObjects ( )
    {
      return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
    }
}
