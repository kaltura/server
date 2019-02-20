<?php
/**
 * @package plugins.booleanNotification
 * @subpackage model.enum
 */
class BooleanNotificationTemplateType implements IKalturaPluginEnum, EventNotificationTemplateType
{
    const BOOLEAN = 'Boolean';

    /* (non-PHPdoc)
     * @see IKalturaPluginEnum::getAdditionalValues()
     */
    public static function getAdditionalValues()
    {
        return array(
            'BOOLEAN' => self::BOOLEAN,
        );
    }

    /* (non-PHPdoc)
     * @see IKalturaPluginEnum::getAdditionalDescriptions()
     */
    public static function getAdditionalDescriptions()
    {
        return array(
            self::BOOLEAN => 'Boolean event notification',
        );
    }
}
