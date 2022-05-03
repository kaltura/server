<?php

/**
 * @package plugins.kafka
 * @subpackage lib.enum
 */
class KafkaProviderType implements IKalturaPluginEnum, QueueProviderType
{
    const KAFKA = 'Kafka';

    public static function getAdditionalValues()
    {
        return array(
            'KAFKA' => self::KAFKA,
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
