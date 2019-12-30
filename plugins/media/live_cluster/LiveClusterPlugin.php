<?php
/**
 * Enable using the new live cluster
 * @package plugins.liveCluster
 */
class LiveClusterPlugin extends KalturaPlugin implements IKalturaObjectLoader, IKalturaEnumerator
{
    const PLUGIN_NAME = 'liveCluster';

    /* (non-PHPdoc)
     * @see IKalturaPlugin::getPluginName()
     */
    public static function getPluginName()
    {
        return self::PLUGIN_NAME;
    }

    /* (non-PHPdoc)
     * @see IKalturaEnumerator::getEnums()
     */
    public static function getEnums($baseEnumName = null)
    {
        if(is_null($baseEnumName) || $baseEnumName === 'serverNodeType')
        {
            return array('LiveClusterMediaServerNodeType');
        }
        return array();
    }

    /* (non-PHPdoc)
     * @see IKalturaObjectLoader::loadObject()
     */
    public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
    {
        if($baseClass == 'KalturaServerNode' && $enumValue == self::getLiveClusterMediaServerTypeCoreValue(LiveClusterMediaServerNodeType::LIVE_CLUSTER_MEDIA_SERVER))
            return new KalturaLiveClusterMediaServerNode();
    }

    /* (non-PHPdoc)
     * @see IKalturaObjectLoader::getObjectClass()
     */
    public static function getObjectClass($baseClass, $enumValue)
    {
        if($baseClass == 'ServerNode' && $enumValue == self::getLiveClusterMediaServerTypeCoreValue(LiveClusterMediaServerNodeType::LIVE_CLUSTER_MEDIA_SERVER))
            return 'LiveClusterMediaServerNode';
    }

    /* (non-PHPdoc)
     * @see IKalturaCuePoint::getCuePointTypeCoreValue()
     */
    public static function getLiveClusterMediaServerTypeCoreValue($valueName)
    {
        $value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
        return kPluginableEnumsManager::apiToCore('serverNodeType', $value);
    }
}