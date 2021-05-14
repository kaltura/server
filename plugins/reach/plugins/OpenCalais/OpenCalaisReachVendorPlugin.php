<?php
/**
 * Enable open calais reach vendor feature.
 * @package plugins.openCalaisReachVendor
 */

class OpenCalaisReachVendorPlugin extends KalturaPlugin implements IKalturaPending, IKalturaEnumerator, IKalturaObjectLoader
{
    const PLUGIN_NAME = 'OpenCalaisReachVendor';
    /**
     * @inheritDoc
     */
    public static function getPluginName()
    {
        return self::PLUGIN_NAME;
    }

    /**
     * @inheritDoc
     */
    public static function dependsOn()
    {
        $reachPluginDependency = new KalturaDependency(ReachPlugin::getPluginName());
        $reachInternalPluginDependency = new KalturaDependency(ReachInternalPlugin::PLUGIN_NAME);
        $transcriptPluginDependency = new KalturaDependency(TranscriptPlugin::PLUGIN_NAME);
        $metadataPluginDependency = new KalturaDependency(MetadataPlugin::PLUGIN_NAME);

        return array($reachPluginDependency, $reachInternalPluginDependency, $transcriptPluginDependency, $metadataPluginDependency);
    }

    /**
     * @inheritDoc
     */
    public static function getEnums($baseEnumName = null)
    {
        if(is_null($baseEnumName))
            return array('OpenCalaisVendorEngineType');

        if($baseEnumName == 'ReachVendorEngineType')
            return array('OpenCalaisVendorEngineType');

        return array();
    }

    /**
     * @return int id of dynamic enum in the DB.
     */
    public static function getCoreValue($type, $valueName)
    {
        $value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
        return kPluginableEnumsManager::apiToCore($type, $value);
    }

    /**
     * @return int id of dynamic enum in the DB.
     */
    public static function getVendorEngineTypeCoreValue($valueName)
    {
        $value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
        return kPluginableEnumsManager::apiToCore('VendorEngineType', $value);
    }

    /**
     * @return string external API value of dynamic enum.
     */
    public static function getApiValue($valueName)
    {
        return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
    }

    /**
     * @inheritDoc
     */
    public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
    {
        if ($baseClass === 'kReachVendorTaskProcessorEngine' && $enumValue == KalturaReachVendorEngineType::OPEN_CALAIS)
        {
            return new kReachVendorTaskOpenCalaisProcessorEngine();
        }
    }

    /**
     * @inheritDoc
     */
    public static function getObjectClass($baseClass, $enumValue)
    {
        // TODO: Implement getObjectClass() method.
    }
}