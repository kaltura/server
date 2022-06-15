<?php
require_once(dirname(__file__) . "/../../../../../../vendor/avro/flix-tech/confluent-schema-registry-api/vendor/autoload.php");

use FlixTech\SchemaRegistryApi\Registry\BlockingRegistry;
use FlixTech\SchemaRegistryApi\Registry\PromisingRegistry;
use GuzzleHttp\Client;

/**
 * @package plugins.kafkaNotification
 * @subpackage model
 */
class KafkaNotificationTemplate extends EventNotificationTemplate
{
	const CUSTOM_DATA_TOPIC_NAME = 'topicName';
	const CUSTOM_DATA_PARTITION_KEY = 'partitionKey';
	const CUSTOM_DATA_MESSAGE_FORMAT = 'messageFormat';
	
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
	
	public function setTopicName($value)
	{
		return $this->putInCustomData(self::CUSTOM_DATA_TOPIC_NAME, $value);
	}
	
	public function getTopicName()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_TOPIC_NAME);
	}
	
	public function setPartitionKey($value)
	{
		return $this->putInCustomData(self::CUSTOM_DATA_PARTITION_KEY, $value);
	}
	
	public function getPartitionKey()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_PARTITION_KEY);
	}
	
	public function setMessageFormat($value)
	{
		return $this->putInCustomData(self::CUSTOM_DATA_MESSAGE_FORMAT, $value);
	}
	
	public function getMessageFormat()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_MESSAGE_FORMAT);
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
			$topicName = $this->getTopicName();
			$subject = $topicName . '-value';
			$messageFormat = $this->getMessageFormat();
			
			$partitionKey = $this->getPartitionKey();
			$queueProvider = QueueProvider::getInstance(KafkaPlugin::getKafakaQueueProviderTypeCoreValue('Kafka'));
			
			if (in_array($partitionKey, (array)$object)) {
				if ($messageFormat == '2') {
					$schemaRegistry = new BlockingRegistry(
						new PromisingRegistry(
							new Client(['base_uri' => '192.168.56.1:8081'])
						)
					);
					
					$schemaId = $schemaRegistry->schemaId("schemaName");
					$schema = $schemaRegistry->schemaForId($schemaId);
					
					$io = new \AvroStringIO();
					$io->write(pack('C', 0));
					$io->write(pack('N', $schemaId));
					$encoder = new \AvroIOBinaryEncoder($io);
					$writer = new \AvroIODatumWriter($schema);
					$writer->write($msg, $encoder);
					$kafkaPayload = $io->string();
					
					$queueProvider->sendWithPartitionKeyAvro($topicName, $partitionKey, $kafkaPayload);
				} else {
					$queueProvider->sendWithPartitionKey($topicName, $partitionKey, $msg);
				}
			} else {
				KalturaLog::err("partition key [$partitionKey] doen't exists in topic [$topicName]");
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
