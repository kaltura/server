<?php
class KalturaBeacon extends KalturaObject implements IRelatedFilterable{

    /**
     * @var KalturaBeaconObjectTypes
     */
    public $relatedObjectType;

    /**
     * @var string
     */
    public $eventType;

    /**
     * @var string
     */
    public $objectId;

    /**
     * @var string
     */
    public $privateData;

    private static $map_between_objects = array
    (
        "entryId",
        "userId" => "puserId",
        "createdAt"
    );

    public function getMapBetweenObjects ( )
    {
        return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
    }

    public function toObject($object_to_fill = null, $props_to_skip = array())
    {
        if (!$object_to_fill)
            $object_to_fill = new KalturaBeacon();

        return parent::toObject($object_to_fill, $props_to_skip);
    }

    public function getExtraFilters()
    {
        return array();
    }

    public function getFilterDocs()
    {
        return array();
    }


}