<?php
/**
 * @package plugins.faceCuePoint
 * @subpackage api.objects
 */
class KalturaFaceCuePoint extends KalturaCuePoint
{
    /**
     * @var string
     */
    public $thumbUrl;

    /**
     * @var int
     */
    public $kuserId;

    /**
     * @var string
     */
    public $puserId;

    /**
     * @var int
     */
    public $endTime;

    /**
     * @var string
     */
    public $assetId;

    public function __construct()
    {
        $this->cuePointType = FaceCuePointPlugin::getApiValue(FaceCuePointType::FACE);
    }
    private static $map_between_objects = array
    (
        "thumbUrl",
        "kuserId",
        "puserId",
        "endTime",
        "assetId"
    );

    /* (non-PHPdoc)
     * @see KalturaCuePoint::getMapBetweenObjects()
     */
    public function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
    }

    /* (non-PHPdoc)
     * @see KalturaObject::toInsertableObject()
     */
    public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
    {
        if(is_null($object_to_fill))
            $object_to_fill = new FaceCuePoint();

        return parent::toInsertableObject($object_to_fill, $props_to_skip);
    }

}
