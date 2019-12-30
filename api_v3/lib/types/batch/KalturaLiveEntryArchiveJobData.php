<?php

/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveEntryArchiveJobData extends KalturaJobData
{

    /**
     * @var string
     */
    public $liveEntryId;

    /**
     * @var string
     */
    public $nonDeletedCuePointsTags;

    private static $map_between_objects = array
    (
        'liveEntryId',
        'nonDeletedCuePointsTags'
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
            $dbData = new kLiveEntryArchiveJobData();

        return parent::toObject($dbData, $props_to_skip);
    }
}