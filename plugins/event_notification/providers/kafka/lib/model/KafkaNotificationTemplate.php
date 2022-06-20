<?php
//require_once(dirname(__file__) . "/../../../../../../vendor/avro/flix-tech/confluent-schema-registry-api/vendor/autoload.php");
//
//use FlixTech\SchemaRegistryApi\Registry\BlockingRegistry;
//use FlixTech\SchemaRegistryApi\Registry\PromisingRegistry;
//use GuzzleHttp\Client;

/**
 * @package plugins.kafkaNotification
 * @subpackage model
 */
class KafkaNotificationTemplate extends EventNotificationTemplate
{
	const CUSTOM_DATA_TOPIC_NAME = 'topicName';
	const CUSTOM_DATA_PARTITION_KEY = 'partitionKey';
	const CUSTOM_DATA_MESSAGE_FORMAT = 'messageFormat';
	const CUSTOM_DATA_API_OBJECT_TYPE = 'apiObjectType';
	
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
	
	public function setApiObjectType($value)
	{
		return $this->putInCustomData(self::CUSTOM_DATA_API_OBJECT_TYPE, $value);
	}
	
	public function getApiObjectType()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_API_OBJECT_TYPE);
	}
	
	public function dispatch(kScope $scope)
	{
		KalturaLog::debug("Dispatching event notification with name [{$this->getName()}] systemName [{$this->getSystemName()}]");
		if (!$scope || !($scope instanceof kEventScope))
		{
			KalturaLog::err('Failed to dispatch due to incorrect scope [' . $scope . ']');
			return;
		}
		
		if (!kConf::hasMap('kafka'))
		{
			KalturaLog::debug("Kafka configuration file (kafka.ini) wasn't found!");
			return;
		}
		
		$object = $scope->getEvent()->getObject();
		if(!$object)
		{
			KalturaLog::debug("Object not found breaking event handling flow");
			return;
		}
		
		
		$partitionKey = $this->getPartitionKey();
		$getter = "get" . ucfirst($partitionKey);
		if(!is_callable(array($object, $getter)))
		{
			KalturaLog::debug("Partition key getter not found on object");
			return;
		}
		
		$partitionKeyValue = $object->$getter();
		if(!$partitionKeyValue)
		{
			KalturaLog::debug("Partition key value [$partitionKeyValue] empty or not found on object");
			return;
		}
		
		$oldValues = array();
		if($scope->getEvent() instanceof kObjectChangedEvent)
		{
			$oldValues = $scope->getEvent()->getModifiedColumns();
		}
		
		$apiObjectType = $this->getApiObjectType();
		$apiObject = call_user_func(kCurrentContext::$serializeCallback, $object, $apiObjectType, 1);
		
		$msg = array(
			"uniqueId" => (string)new UniqueId(),
			"eventTime" => date('Y-m-d H:i:s'),
			"eventType" => get_class($scope->getEvent()),
			"objectType" => get_class($object),
			"virtualEventId" => kCurrentContext::$virtual_event_id,
			"object" => $apiObject,
			"oldValues" => $oldValues
		);
		KalturaLog::debug("TTT: object " . print_r($msg, true));
		
		try
		{
			$topicName = $this->getTopicName();
			$messageFormat = $this->getMessageFormat();
			$queueProvider = QueueProvider::getInstance(KafkaPlugin::getKafakaQueueProviderTypeCoreValue('Kafka'));
			$kafkaPayload = $this->getKafkaPayload();
			
			if(!$kafkaPayload)
			{
				KalturaLog::debug("Failed to build Kafka payload");
				return;
			}
			
			KalturaLog::debug("TTT: topicName [$topicName] partitionKeyValue [$partitionKeyValue] Payload is " . print_r($kafkaPayload, true));
			$queueProvider->send($topicName, $kafkaPayload, array( "partitionKey" => $partitionKeyValue));
		}
		catch (Exception $e)
		{
			KalturaLog::debug("Failed to send message with error [" . $e->getMessage() . "]");
			return;
		}
	}
	
	private function getKafkaPayload($topicName, $data)
	{
		$kafkaPayload = null;
		
		if($messageFormat == KafkaNotificationFormat::AVRO)
		{
			$kafkaPayload = $this->getAvroPayload($topicName, $data);
			$queueProvider->produce($topicName, $partitionKey, $kafkaPayload);
			
		}
		elseif($messageFormat == KafkaNotificationFormat::JSON)
		{
			$kafkaPayload = json_encode($msg, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
			
		}
		else
		{
			KalturaLog::debug("Unknown notification message format [$messageFormat]");
		}
		
		return $kafkaPayload;
	}
	
	/**
	 * @param string $subject
	 * @param string $schema
	 * @param array $data
	 * @return string
	 * @throws AvroIOException
	 * @throws AvroSchemaParseException
	 * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
	 * @throws kCoreException
	 */
	private function getAvroPayload($topicName, $data)
	{
		list($schema, $schemaId) = $this->getSchemaInfo($topicName . '-value');
		if(!($schema && $schemaId))
		{
			return null;
		}
		return $this->buildAvroPayload($schemaId, $schema, $data);
	}
	
	private function getSchemaInfo($subject)
	{
		
		$schemaRegistryConfig = kConf::get("schema_registry", "kafka", array());
		$schemaRegistryServer = isset($schemaRegistryConfig['schema_registry_server']) ? $schemaRegistryConfig['schema_registry_server'] : null;
		$schemaRegistryPort = isset($schemaRegistryConfig['schema_registry_port']) ? $schemaRegistryConfig['schema_registry_port'] : null;
		if(!($schemaRegistryServer && $schemaRegistryPort))
		{
			KalturaLog::debug("Message configured with Avro payload but cannot find schema registry settings!");
			return array(null, null);
		}
		
		$schemaRegistry = new BlockingRegistry(
			new PromisingRegistry(
				new Client(['base_uri' => $schemaRegistryServer . ":" . $schemaRegistryPort])
			)
		);
		if(!$schemaRegistry)
		{
			return array(null, null);
		}
		
		$schema = $schemaRegistry->latestVersion($subject);
		if(!$schema)
		{
			KalturaLog::debug("Missing schema for subject [$subject]!");
			return array(null, null);
		}
		
		$schemaId = $schemaRegistry->schemaId($subject, $schema);
		if(!$schemaId)
		{
			KalturaLog::debug("Missing schema ID for subject [$subject] and schema [$schema]!");
			return array(null, null);
		}
		
		return array($schemaId, $schema);
	}
	
	/**
	 * @param int $schemaId
	 * @param AvroSchema $schema
	 * @param array $data
	 * @return string
	 * @throws AvroIOException
	 */
	private function buildAvroPayload($schemaId, $schema, $data)
	{
		$io = new \AvroStringIO();
		$io->write(pack('C', 0));
		$io->write(pack('N', $schemaId));
		$encoder = new \AvroIOBinaryEncoder($io);
		$writer = new \AvroIODatumWriter($schema);
		$writer->write($data, $encoder);
		return $io->string();
	}
}