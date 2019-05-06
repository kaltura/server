<?php
/**
 * @package plugins.faceCuePoint
 * @subpackage lib.enum
 */
class FaceCuePointType implements IKalturaPluginEnum, CuePointType
{
    const FACE = 'Face';

    public static function getAdditionalValues()
    {
        return array(
            'FACE' => self::FACE,
        );
    }

    /**
     * @return array
     */
    public static function getAdditionalDescriptions()
    {
        return array();
    }
}
