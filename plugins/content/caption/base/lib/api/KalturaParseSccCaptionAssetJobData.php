<?php
/**
 * @package plugins.caption
 * @subpackage api.objects
 */
class KalturaParseSccCaptionAssetJobData extends KalturaJobData
{
    /**
     * @var string
     */
    public $sccCaptionAssetId;

    /**
     * @var string
     */
    public $fileLocation;

    /**
     * @var string
     */
    public $fileEncryptionKey;

    private static $map_between_objects = array
    (
        "sccCaptionAssetId",
        "fileLocation",
        "fileEncryptionKey",
    );

    /* (non-PHPdoc)
     * @see KalturaObject::getMapBetweenObjects()
     */
    public function getMapBetweenObjects ( )
    {
      return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
    }
}
