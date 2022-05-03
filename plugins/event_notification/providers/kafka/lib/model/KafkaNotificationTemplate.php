<?php
/**
 * @package plugins.kafkaNotification
 * @subpackage model
 */
class KafkaNotificationTemplate extends EventNotificationTemplate
{
    const CUSTOM_DATA_TOPIC_NAME_PARAMETERS = 'topicNameParameters';
    const CUSTOM_DATA_PARTITION_KEY = 'partitionKey';

    public function __construct()
    {
        $this->setType(KafkaNotificationPlugin::getKafkaNotificationTemplateTypeCoreValue(KafkaNotificationTemplateType::KAFKA));
        parent::__construct();
    }

    public function fulfilled(kEventScope $scope)
    {
        if (!kCurrentContext::$serializeCallback)
            return false;

        if (!parent::fulfilled($scope))
            return false;

        return true;
    }

    public function setTopicNameParameters($value)
    {
        return $this->putInCustomData(self::CUSTOM_DATA_TOPIC_NAME_PARAMETERS, $value);
    }

    public function getTopicNameParameters()
    {
        return $this->getFromCustomData(self::CUSTOM_DATA_TOPIC_NAME_PARAMETERS);
    }

    public function setPartitionKey($value)
    {
        return $this->putInCustomData(self::CUSTOM_DATA_PARTITION_KEY, $value);
    }

    public function getPartitionKey()
    {
        return $this->getFromCustomData(self::CUSTOM_DATA_PARTITION_KEY);
    }


    public function dispatch(kScope $scope)
    {
        KalturaLog::debug("Dispatching event notification with name [{$this->getName()}] systemName [{$this->getSystemName()}]");
        if (!$scope || !($scope instanceof kEventScope)) {
            KalturaLog::err('Failed to dispatch due to incorrect scope [' . $scope . ']');
            return;
        }

        $uniqueId = (string)new UniqueId();
        $eventTime = date('Y-m-d H:i:s');
        $eventType = $_REQUEST[action];
        $objectType = $_REQUEST[service];
        $objectArray = $scope->getEvent();
        $object = $objectArray->getObject();
        $objectArray = $object->toArray();
        $oldValues = $this->oldColumnsValues;

        $msg = json_encode(array(
            "uniqueId" => $uniqueId,
            "eventTime" => $eventTime,
            "eventType" => $eventType,
            "objectType" => $objectType,
            "object" => $objectArray,
            "oldValues" => $oldValues
        ));

        try {
            $topicNameParameters = $this->getTopicNameParameters();
            $partitionKey = $this->getPartitionKey();

            $queueProvider = QueueProvider::getInstance(KafkaPlugin::getKafakaQueueProviderTypeCoreValue('Kafka'));

            if(in_array($partitionKey,(array)$object ))
            {
                $queueProvider->sendWithPartitionKey($topicNameParameters, $partitionKey, $msg);
            }
            else
            {
                KalturaLog::err("partition key [$partitionKey] doen't exists in topic [$topicNameParameters]");
            }

        } catch (PhpAmqpLib\Exception\AMQPRuntimeException $e) {
            KalturaLog::debug("Failed to send message with error [" . $e->getMessage() . "]");
            throw $e;
        }
    }

    public function create($queueKey)
    {
        // get instance of activated queue proivder and create queue with given name
        $queueProvider = QueueProvider::getInstance();
        $queueProvider->create($queueKey);
    }

    public function exists($queueKey)
    {
        // get instance of activated queue proivder and check whether given queue exists
        $queueProvider = QueueProvider::getInstance();
        return $queueProvider->exists($queueKey);
    }
}
