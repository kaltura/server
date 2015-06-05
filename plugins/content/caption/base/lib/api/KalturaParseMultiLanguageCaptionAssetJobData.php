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
    public $parentCaptionAssetId;

    /**
     * @var string
     */
    public $entryId;

    /**
     * @var string
     */
    public $fileLocation;

    private static $map_between_objects = array
    (
        "parentCaptionAssetId",
        "entryId",
        "fileLocation",
    );

    /* (non-PHPdoc)
     * @see KalturaObject::getMapBetweenObjects()
     */
    public function getMapBetweenObjects ( )
    {
      return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
    }
}
