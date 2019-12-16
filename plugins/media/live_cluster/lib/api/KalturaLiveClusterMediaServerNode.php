<?php
/**
 * @package plugins.liveCluster
 * @subpackage api.objects
 */
class KalturaLiveClusterMediaServerNode extends KalturaMediaServerNode
{
    private static $mapBetweenObjects = array
    (

    );

    /* (non-PHPdoc)
     * @see KalturaObject::validateForInsert()
     */
    public function validateForInsert($propertiesToSkip = array())
    {
        return parent::validateForInsertByType($propertiesToSkip, LiveClusterPlugin::getLiveClusterMediaServerTypeCoreValue(LiveClusterMediaServerNodeType::LIVE_CLUSTER_MEDIA_SERVER));
    }

    /* (non-PHPdoc)
     * @see KalturaObject::validateForUpdate()
     */
    public function validateForUpdate($sourceObject, $propertiesToSkip = array())
    {
        return parent::validateForUpdateByType($sourceObject, $propertiesToSkip, LiveClusterPlugin::getLiveClusterMediaServerTypeCoreValue(LiveClusterMediaServerNodeType::LIVE_CLUSTER_MEDIA_SERVER));
    }


    /* (non-PHPdoc)
     * @see KalturaObject::getMapBetweenObjects()
     */
    public function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
    }

    /* (non-PHPdoc)
     * @see KalturaObject::toInsertableObject()
     */
    public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
    {
        if(is_null($object_to_fill))
            $object_to_fill = new LiveClusterMediaServerNode();

        return parent::toInsertableObject($object_to_fill, $props_to_skip);
    }

    /* (non-PHPdoc)
     * @see KalturaObject::toObject()
     */
    public function toObject($dbObject = null, $skip = array())
    {
        if(!$dbObject)
            $dbObject = new LiveClusterMediaServerNode();

        return parent::toObject($dbObject, $skip);
    }

}