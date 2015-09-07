<?php
/**
 * @package infra
 * @subpackage Plugins
 */
class ParentObjectFeatureType implements IKalturaPluginEnum, ObjectFeatureType
{
    const PARENT = 'Parent';

    /* (non-PHPdoc)
     * @see IKalturaPluginEnum::getAdditionalValues()
     */
    public static function getAdditionalValues()
    {
        return array
        (
            'PARENT' => self::PARENT,
        );

    }

    /* (non-PHPdoc)
     * @see IKalturaPluginEnum::getAdditionalDescriptions()
     */
    public static function getAdditionalDescriptions() {
        return array();

    }
}