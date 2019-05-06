<?php
/**
 * Enable face cue point objects management on entry objects
 * @package plugins.FaceCuePoint
 */
class FaceCuePointPlugin extends BaseCuePointPlugin implements IKalturaCuePoint, IKalturaEventConsumers
{
    const PLUGIN_NAME = 'faceCuePoint';
    const CUE_POINT_VERSION_MAJOR = 1;
    const CUE_POINT_VERSION_MINOR = 0;
    const CUE_POINT_VERSION_BUILD = 0;
    const CUE_POINT_NAME = 'cuePoint';

    const FACE_CUE_POINT_CONSUMER = 'kFaceCuePointConsumer';

    /* (non-PHPdoc)
     * @see IKalturaPlugin::getPluginName()
     */
    public static function getPluginName()
    {
        return self::PLUGIN_NAME;
    }

    /* (non-PHPdoc)
     * @see IKalturaPermissions::isAllowedPartner()
     */
    public static function isAllowedPartner($partnerId)
    {
        return true;
    }

    /* (non-PHPdoc)
     * @see IKalturaEnumerator::getEnums()
     */
    public static function getEnums($baseEnumName = null)
    {
        if(is_null($baseEnumName))
            return array('FaceCuePointType');

        if($baseEnumName == 'CuePointType')
            return array('FaceCuePointType');

        return array();
    }

    /* (non-PHPdoc)
     * @see IKalturaPending::dependsOn()
     */
    public static function dependsOn()
    {
        $cuePointVersion = new KalturaVersion(
            self::CUE_POINT_VERSION_MAJOR,
            self::CUE_POINT_VERSION_MINOR,
            self::CUE_POINT_VERSION_BUILD);

        $dependency = new KalturaDependency(self::CUE_POINT_NAME, $cuePointVersion);
        return array($dependency);
    }

    /* (non-PHPdoc)
     * @see IKalturaObjectLoader::loadObject()
     */
    public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
    {
        if($baseClass == 'KalturaCuePoint' && $enumValue == self::getCuePointTypeCoreValue(FaceCuePointType::FACE))
            return new KalturaFaceCuePoint();
    }

    /* (non-PHPdoc)
     * @see IKalturaObjectLoader::getObjectClass()
     */
    public static function getObjectClass($baseClass, $enumValue)
    {
        if($baseClass == 'CuePoint' && $enumValue == self::getCuePointTypeCoreValue(FaceCuePointType::FACE))
            return 'FaceCuePoint';
    }

    /* (non-PHPdoc)
     * @see IKalturaCuePoint::getCuePointTypeCoreValue()
     */
    public static function getCuePointTypeCoreValue($valueName)
    {
        $value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
        return kPluginableEnumsManager::apiToCore('CuePointType', $value);
    }

    /* (non-PHPdoc)
     * @see IKalturaCuePoint::getApiValue()
     */
    public static function getApiValue($valueName)
    {
        return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
    }

    public static function contributeToSchema($type)
    {
        return null;
    }

    /* (non-PHPdoc)
     * @see IKalturaEventConsumers::getEventConsumers()
    */
    public static function getEventConsumers()
        {
            return array(
                self::FACE_CUE_POINT_CONSUMER
            );
        }

    public static function getTypesToIndexOnEntry()
    {
        return array();
    }

    public static function shouldCloneByProperty(entry $entry)
    {
        return false;
    }

    public static function getTypesToElasticIndexOnEntry()
    {
        return array();
    }

}