<?php

/**
 * @package plugins.kafka
 */
class KafkaPlugin extends KalturaPlugin implements IKalturaPending, IKalturaObjectLoader, IKalturaQueuePlugin
{
    const PLUGIN_NAME = 'kafka';
    const QUEUE_PLUGIN_NAME = 'queue';
    const QUEUE_PLUGIN_VERSION_MAJOR = 1;
    const QUEUE_PLUGIN_VERSION_MINOR = 0;
    const QUEUE_PLUGIN_VERSION_BUILD = 0;

    /*
     * (non-PHPdoc)
     * @see IKalturaPlugin::getPluginName()
     */
    public static function getPluginName()
    {
        return self::PLUGIN_NAME;
    }

    /*
     * (non-PHPdoc)
     * @see IKalturaObjectLoader::getObjectClass()
     */
    public static function getObjectClass($baseClass, $enumValue)
    {
        if ($baseClass == 'QueueProvider' && $enumValue == self::getKafakaQueueProviderTypeCoreValue(KafkaProviderType::KAFKA)) {
            if (!kConf::hasMap('kafka')) {
                throw new kCoreException("Kafka configuration file (kafka.ini) wasn't found!");
            }
            $kafkaConfig = kConf::getMap('kafka');

            if (isset($kafkaConfig['multiple_dcs']) && $kafkaConfig['multiple_dcs']) {
                return 'MultiCentersKafkaProvider';
            }

            return 'KafkaProvider';
        }
    }

    /*
     * (non-PHPdoc)
     * @see IKalturaObjectLoader::loadObject()
     */
    public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
    {
        if ($baseClass == 'QueueProvider' && $enumValue == self::getKafakaQueueProviderTypeCoreValue(KafkaProviderType::KAFKA)) {
            if (!kConf::hasMap('kafka')) {
                throw new kCoreException("Kafka configuration file (kafka.ini) wasn't found!");
            }

            $kafkaConfig = kConf::getMap('kafka');

            return new KafkaProvider($kafkaConfig, $constructorArgs);
        }

        return null;
    }

    /**
     *
     * @return int id of dynamic enum in the DB.
     */
    public static function getKafakaQueueProviderTypeCoreValue($valueName)
    {
        $value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
        return kPluginableEnumsManager::apiToCore('QueueProviderType', $value);
    }

    /*
     * (non-PHPdoc)
     * @see IKalturaPending::dependsOn()
     */
    public static function dependsOn()
    {
        $minVersion = new KalturaVersion(self::QUEUE_PLUGIN_VERSION_MAJOR, self::QUEUE_PLUGIN_VERSION_MINOR, self::QUEUE_PLUGIN_VERSION_BUILD);
        $dependency = new KalturaDependency(self::QUEUE_PLUGIN_NAME, $minVersion);

        return array($dependency);
    }
}
