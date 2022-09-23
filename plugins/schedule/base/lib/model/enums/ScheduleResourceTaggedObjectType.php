<?php

/**
 * @package plugins.MicrosoftTeamsDropFolder
 * @subpackage lib
 */

class ScheduleResourceTaggedObjectType implements IKalturaPluginEnum, taggedObjectType
{
    const SCHEDULE_RESOURCE = 'SCHEDULE_RESOURCE';

    /**
     * @inheritDoc
     */
    public static function getAdditionalValues()
    {
        return array(
            'SCHEDULE_RESOURCE' => self::SCHEDULE_RESOURCE,
        );
    }

    /**
     * @inheritDoc
     */
    public static function getAdditionalDescriptions()
    {
        return array(
            SchedulePlugin::getApiValue(self::SCHEDULE_RESOURCE) => 'Schedule resource',
        );
    }
}