<?php
//ToDo add support for avro
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
	const CUSTOM_DATA_RESPONSE_PROFILE_SYSTEM_NAME = 'responseProfileSystemName';
	const CUSTOM_DATA_REQUIRES_PERMISSIONS = 'requiresPermissions';
	const SCHEMA_ID = 'schemaId';
	const SCHEMA = 'schema';
	
	public function __construct()
	{
		$this->setType(KafkaNotificationPlugin::getKafkaNotificationTemplateTypeCoreValue(KafkaNotificationTemplateType::KAFKA));
		parent::__construct();
	}
	
	public function fulfilled(kEventScope $scope)
	{
		if(!kCurrentContext::$serializeCallback)
			return false;
		
		if(!parent::fulfilled($scope))
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

	public function setResponseProfileSystemName($value)
	{
		return $this->putInCustomData(self::CUSTOM_DATA_RESPONSE_PROFILE_SYSTEM_NAME, $value);
	}

	public function getResponseProfileSystemName()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_RESPONSE_PROFILE_SYSTEM_NAME);
	}

	public function setRequiresPermissions($value)
	{
		return $this->putInCustomData(self::CUSTOM_DATA_REQUIRES_PERMISSIONS, $value);
	}

	public function getRequiresPermissions()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_REQUIRES_PERMISSIONS, null, "");
	}
	
	public function dispatch(kScope $scope)
	{
		KalturaLog::debug("Dispatching event notification with name [{$this->getName()}] systemName [{$this->getSystemName()}]");
		if(!$scope || !($scope instanceof kEventScope))
		{
			KalturaLog::err('Failed to dispatch due to incorrect scope [' . $scope . ']');
			return;
		}
		
		if(!kConf::hasMap(kConfMapNames::KAFKA))
		{
			KalturaLog::debug("Kafka configuration file (kafka.ini) wasn't found!");
			return;
		}

		$partnerId = $scope->getPartnerId();
		$requiredPermissions = explode(",", $this->getRequiresPermissions());
		if(count(array_filter($requiredPermissions, 'strlen')))
		{
			KalturaLog::debug("Checking if partner has permissions required to dispatch [{$this->getRequiresPermissions()}]");
			$found = false;
			foreach($requiredPermissions as $requiredPermission)
			{
				$found = PermissionPeer::isValidForPartner($requiredPermission, $partnerId);
				if($found)
				{
					break;
				}
			}

			if(!$found)
			{
				KalturaLog::debug("Failed to find required permission to dispatch!");
				return;
			}
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
		
		$modifiedColumns = array();
		if($scope->getEvent() instanceof kObjectChangedEvent)
		{
			$modifiedColumns = $scope->getEvent()->getModifiedColumns();
			$modifiedColumns = $this->buildMessageOldValues($modifiedColumns);
		}

		$responseProfile = null;
		if($this->getResponseProfileSystemName())
		{
			$responseProfile = ResponseProfilePeer::retrieveBySystemName($this->getResponseProfileSystemName());
		}

		$apiObjectType = $this->getApiObjectType();
		$apiObject = call_user_func(kCurrentContext::$serializeCallback, $object, $apiObjectType, 1, $responseProfile);
		$apiObject = json_decode($apiObject, true);
		
		$apiObjectAdditionalParams = $this->getContentParameters();
		foreach ($apiObjectAdditionalParams as $apiObjectAdditionalParam)
		{
			/* @var $apiObjectAdditionalParam kEventNotificationParameter */
			$value = $apiObjectAdditionalParam->getValue();
			if($scope && $value instanceof kStringField)
				$value->setScope($scope);
			
			$key = $apiObjectAdditionalParam->getKey();
			$apiObject[$key] = $value->getValue();
		}
		
		$apiObject = json_encode($apiObject);
		$msg = array(
			"uniqueId" => (string)new UniqueId(),
			"eventTime" => date('Y-m-d H:i:s'),
			"eventType" => get_class($scope->getEvent()),
			"objectType" => $apiObjectType,
			"virtualEventId" => kCurrentContext::$virtual_event_id,
			"currentPartnerId" => kCurrentContext::getCurrentPartnerId(),
			"partnerId" => $partnerId,
			"object" => $apiObject,
			"modifiedColumns" => $modifiedColumns
		);
		
		try
		{
			$topicName = $this->getTopicName();
			$messageFormat = $this->getMessageFormat();
			$queueProvider = QueueProvider::getInstance(KafkaPlugin::getKafakaQueueProviderTypeCoreValue('Kafka'));
			$kafkaPayload = $this->getKafkaPayload($topicName, $msg, $messageFormat);
			
			if(!$kafkaPayload)
			{
				KalturaLog::debug("Failed to build Kafka payload");
				return;
			}
			
			KalturaLog::debug("topicName [$topicName] partitionKeyValue [$partitionKeyValue] Payload is " . print_r($kafkaPayload, true));
			$queueProvider->send($topicName, $kafkaPayload, array("partitionKey" => $partitionKeyValue));
		} catch (Exception $e)
		{
			KalturaLog::debug("Failed to send message with error [" . $e->getMessage() . "]");
			return;
		}
	}
	
	protected function getKafkaPayload($topicName, $msg, $messageFormat)
	{
		$kafkaPayload = null;
		
		if($messageFormat == KafkaNotificationFormat::AVRO)
		{
			$kafkaPayload = $this->getAvroPayload($topicName, $msg);
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
	 * @param array $msg
	 * @return string
	 * @throws AvroIOException
	 * @throws AvroSchemaParseException
	 * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
	 * @throws kCoreException
	 */
	protected function getAvroPayload($topicName, $msg)
	{
		list($schema, $schemaId) = $this->getSchemaInfo($topicName);
		if(!($schema && $schemaId))
		{
			return null;
		}
		return $this->buildAvroPayload($schemaId, $schema, $msg);
	}
	
	protected function getSchemaInfo($subject)
	{
		$schemaId = null;
		$schema = null;
		
		if(!kConf::hasMap(kConfMapNames::AVRO_SCHEMA_REGISTRY))
		{
			throw new kCoreException("schema registry configuration file (schemaRegistry.ini) wasn't found!");
		}
		
		$schemaRegistryConfig = kConf::getMap('schemaRegistry');
		
		$schemaRegistryServer = isset($schemaRegistryConfig['schema_registry_server']) ? $schemaRegistryConfig['schema_registry_server'] : null;
		$schemaRegistryPort = isset($schemaRegistryConfig['schema_registry_port']) ? $schemaRegistryConfig['schema_registry_port'] : null;
		if(!($schemaRegistryServer && $schemaRegistryPort))
		{
			KalturaLog::debug("Message configured with Avro payload but cannot find schema registry settings!");
			return array(null, null);
		}
		
		$currentSchemaVersion = $schemaRegistryConfig[$subject];
		if(!$currentSchemaVersion)
		{
			KalturaLog::debug("Missing schema version for [$subject]!");
			return array(null, null);
		}
		
		$key = $subject . '_' . $currentSchemaVersion;
		
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_AVRO_SCHEMAS);
		if($cache)
		{
			$schemaInfo = $cache->get($key);
			if($schemaInfo)
			{
				$schemaId = $schemaInfo[self::SCHEMA_ID];
				$schema = $schemaInfo[self::SCHEMA];
			}
		}
		
		if(!$schema)
		{
			$schemaRegistry = new BlockingRegistry(
				new PromisingRegistry(
					new Client(['base_uri' => $schemaRegistryServer . ":" . $schemaRegistryPort])
				)
			);
			
			if(!$schemaRegistry)
			{
				return array(null, null);
			}
			
			$schema = $schemaRegistry->schemaForSubjectAndVersion($subject, $currentSchemaVersion);
			
			if(!$schema)
			{
				KalturaLog::debug("Missing schema in schema registry for [$subject]!");
				return array(null, null);
			}
			
			$schemaId = $schemaRegistry->schemaId($subject, $schema);
			if(!$schemaId)
			{
				KalturaLog::debug("Missing schema ID for subject [$subject] and schema [$schema]!");
				return array(null, null);
			}
			
			if($cache)
			{
				$schemaInfo = array(
					self::SCHEMA_ID => $schemaId,
					self::SCHEMA => $schema
				);
				
				$result = $cache->add($key, $schemaInfo);
				if(!$result)
				{
					KalturaLog::debug("Avro schema [$subject] did not save to cache [$key]");
				}
			}
		}
		
		return array($schema, $schemaId);
	}
	
	/**
	 * @param int $schemaId
	 * @param AvroSchema $schema
	 * @param array $msg
	 * @return string
	 * @throws AvroIOException
	 */
	protected function buildAvroPayload($schemaId, $schema, $msg)
	{
		$io = new \AvroStringIO();
		$io->write(pack('C', 0));
		$io->write(pack('N', $schemaId));
		$encoder = new \AvroIOBinaryEncoder($io);
		$writer = new \AvroIODatumWriter($schema);
		$writer->write($msg, $encoder);
		return $io->string();
	}
	
	protected function buildMessageOldValues($oldValues)
	{
		$result = array();
		foreach ($oldValues as $key => $value)
		{
			if(is_numeric($key))
			{
				list($object, $field) = explode(".", $value);
			}
			$result[] = $field;
		}
		
		if(isset($oldValues["CUSTOM_DATA"]))
		{
			foreach ($oldValues["CUSTOM_DATA"] as $key => $value)
			{
				$result = array_merge($result, array_keys($value));
			}
		}
		
		return $result;
	}
}
