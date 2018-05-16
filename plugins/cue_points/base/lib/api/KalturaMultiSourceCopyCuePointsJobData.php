<?php
/**
 * @package plugins.cue_points
 * @subpackage api.objects
 */
class KalturaMultiSourceCopyCuePointsJobData extends KalturaCopyCuePointsJobData
{
    
    /**
     *  an array of source start time and duration
     * @var KalturaClipDescriptionArray
     */
    public $clipsDescriptionArray;

    private static $map_between_objects = array
    (
        'clipsDescriptionArray',
    );

    /* (non-PHPdoc)
     * @see KalturaObject::getMapBetweenObjects()
     */
    public function getMapBetweenObjects ( )
    {
        return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
    }

    /* (non-PHPdoc)
     * @see KalturaObject::toObject()
     */
    public function toObject($dbData = null, $props_to_skip = array())
    {
        if(is_null($dbData))
            $dbData = new kMultiSourceCopyCuePointsJobData();

        return parent::toObject($dbData, $props_to_skip);
    }
}