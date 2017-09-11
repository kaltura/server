<?php

/**
 * @package plugins.beacon
 * @subpackage model
 */
class kBeacon
{
	
	const ELASTIC_BEACONS_INDEX_NAME = "beaconindex";
	
	const BEACONS_QUEUE_NAME = 'beacons';
	const BEACONS_EXCHANGE_NAME = 'beacon_exchange';
	
	const ELASTIC_ACTION_KEY = '_action';
	const ELASTIC_INDEX_ACTION_VALUE = 'index';
	const ELASTIC_DELETE_ACTION_VALUE = 'delete';
	
	const ELASTIC_INDEX_KEY = '_index';
	const ELASTIC_INDEX_TYPE_KEY = '_type';
	const ELASTIC_DOCUMENT_ID_KEY = '_id';
	
	const FIELD_CREATED_AT = 'createdAt';
	const FIELD_UPDATED_AT = 'updatedAt';
	const FIELD_RELATED_OBJECT_TYPE = 'relatedObjectType';
	const FIELD_EVENT_TYPE = 'eventType';
	const FIELD_OBJECT_ID = 'objectId';
	const FIELD_PRIVATE_DATA = 'privateData';
	const FIELD_RAW_DATA = 'rawData';
	const FIELD_PARTNER_ID = 'partnerId';
	
	protected $id;
	protected $relatedObjectType;
	protected $eventType;
	protected $objectId;
	protected $privateData;
	protected $rawData;
	protected $partnerId;
	protected $createdAt;
	protected $updatedAt;
	
	public function __construct($partnerId = null)
	{
		if($partnerId)
			$this->setPartnerId($partnerId);
		else
			$this->setPartnerId(kCurrentContext::getCurrentPartnerId());
	}
	
	public function setId($id)
	{
		$this->id = $id;
	}
	
	public function setRelatedObjectType($relatedObjectType)
	{
		$this->relatedObjectType = $relatedObjectType;
	}
	
	public function setEventType($eventType)
	{
		$this->eventType = $eventType;
	}
	
	public function setObjectId($objectId)
	{
		$this->objectId = $objectId;
	}
	
	public function setPrivateData($privateData)
	{
		$this->privateData = $privateData;
	}
	
	public function setRawData($rawData)
	{
		$this->rawData = $rawData;
	}
	
	public function setPartnerId($partnerId)
	{
		$this->partnerId = $partnerId;
	}
	
	public function setCreatedAt($createdAt)
	{
		$this->createdAt = $createdAt;
	}
	
	public function setUpdatedAt($updatedAt)
	{
		$this->updatedAt = $updatedAt;
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function getRelatedObjectType()
	{
		return $this->relatedObjectType;
	}
	
	public function getEventType()
	{
		return $this->eventType;
	}
	
	public function getObjectId()
	{
		return $this->objectId;
	}
	
	public function getPrivateData()
	{
		return $this->privateData;
	}
	
	public function getRawData()
	{
		return $this->rawData;
	}
	
	public function getPartnerId()
	{
		return $this->partnerId;
	}
	
	public function getCreatedAt()
	{
		return $this->createdAt;
	}
	
	public function getUpdatedAt()
	{
		return $this->updatedAt;
	}
	
	public function index($shouldLog = false, $queueProvider = null)
	{
		kApiCache::disableConditionalCache();
		
		// get instance of activated queue provider to send message
		$queueProvider = $queueProvider ? $queueProvider : $this->getQueueProvider();
		
		//Get current time to add to indexed object info
		$currTime = time();
		
		//Create base object for index
		$indexBaseObject = $this->createIndexBaseObject($currTime);
		
		//Modify base object to index to State 
		$stateIndexObjectJson = $this->getIndexObjectForState($indexBaseObject, $currTime);
		$queueProvider->send(self::BEACONS_QUEUE_NAME, $stateIndexObjectJson);
		
		//Sent to log index of requested
		if ($shouldLog) 
		{
			$logIndexObjectJson = $this->getIndexObjectForLog($indexBaseObject, $currTime);
			$queueProvider->send(self::BEACONS_QUEUE_NAME, $logIndexObjectJson);
		}
		
		return true;
	}
	
	private function getQueueProvider()
	{
		$constructorArgs = array();
		$constructorArgs['exchangeName'] = self::BEACONS_EXCHANGE_NAME;
		
		/* @var $queueProvider RabbitMQProvider */
		return QueueProvider::getInstance(null, $constructorArgs);
	}
	
	public function getIndexObjectForState($indexObject, $currTime)
	{
		$docId = md5($this->relatedObjectType . '_' . $this->eventType . '_' . $this->objectId);
		
		$indexObject[self::ELASTIC_DOCUMENT_ID_KEY] = $docId;
		$indexObject[self::ELASTIC_INDEX_TYPE_KEY] = BeaconIndexType::STATE;
		$indexObject[self::FIELD_CREATED_AT] = $this->getDocCreatedAt($docId, $currTime);
		
		return json_encode($indexObject);
	}
	
	public function getIndexObjectForLog($indexObject, $currTime)
	{
		$indexObject[self::FIELD_CREATED_AT] = $currTime;
		$indexObject[self::ELASTIC_INDEX_TYPE_KEY] = BeaconIndexType::LOG;
		
		return json_encode($indexObject);
	}
	
	private function getDocCreatedAt($docId, $currTime)
	{
		$searchObject = array();
		$searchObject[elasticClient::ELASTIC_ID_KEY] = $docId;
		$searchObject[elasticClient::ELASTIC_TYPE_KEY] = BeaconIndexType::STATE;
		$searchObject[elasticClient::ELASTIC_INDEX_KEY] = self::ELASTIC_BEACONS_INDEX_NAME;
		
		$searchMgr = new kBeaconSearchQueryManger();
		$response = $searchMgr->get($searchObject);
		
		if ($response['found'] == false)
			return $currTime;
		
		$doc = $response['_source'];
		if (!$doc[self::FIELD_CREATED_AT])
			return $currTime;
		
		return $doc[self::FIELD_CREATED_AT];
	}
	
	public function createIndexBaseObject($currTime)
	{
		$indexObject = array();
		
		//Set Action Name and Index Name and calculated docuemtn id
		$indexObject[self::ELASTIC_ACTION_KEY] = self::ELASTIC_INDEX_ACTION_VALUE;
		$indexObject[self::ELASTIC_INDEX_KEY] = self::ELASTIC_BEACONS_INDEX_NAME;
		
		//Set values provided in input
		$indexObject[self::FIELD_RELATED_OBJECT_TYPE] = $this->relatedObjectType;
		$indexObject[self::FIELD_EVENT_TYPE] = $this->eventType;
		$indexObject[self::FIELD_OBJECT_ID] = $this->objectId;
		
		$privateDataObject = $this->privateData ? json_decode($this->privateData) : json_decode("{}");
		$indexObject[self::FIELD_PRIVATE_DATA] = $privateDataObject;
		
		$indexObject[self::FIELD_RAW_DATA] = $this->rawData;
		$indexObject[self::FIELD_PARTNER_ID] = $this->partnerId;
		$indexObject[self::FIELD_UPDATED_AT] = $currTime;
		
		return $indexObject;
	}
}
