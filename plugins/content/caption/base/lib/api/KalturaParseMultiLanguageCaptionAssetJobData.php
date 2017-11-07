<?php
/**
 * @package plugins.caption
 * @subpackage api.objects
 */
class KalturaParseMultiLanguageCaptionAssetJobData extends KalturaJobData
{
    /**
     * @var string
     */
    public $multiLanaguageCaptionAssetId;

    /**
     * @var string
     */
    public $entryId;

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
        "multiLanaguageCaptionAssetId",
        "entryId",
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
