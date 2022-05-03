<?php

/**
 * @package plugins.kafkaNotification
 * @subpackage api.objects
 */

class KalturaKafkaEventNotificationParameter extends KalturaEventNotificationParameter
{
    /**
     * @var string
     */
    public $topicName;

    /**
     * @var string
     */
    public $partitionName;

    private static $map_between_objects = array('topicName', 'partitionName');

    /* (non-PHPdoc)
     * @see KalturaObject::getMapBetweenObjects()
     */
    public function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
    }

    /* (non-PHPdoc)
   * @see KalturaObject::toObject()
   */
    public function toObject($dbObject = null, $propertiesToSkip = array())
    {
        if (is_null($dbObject))
            $dbObject = new kPushEventNotificationParameter();

        return parent::toObject($dbObject, $propertiesToSkip);
    }
}