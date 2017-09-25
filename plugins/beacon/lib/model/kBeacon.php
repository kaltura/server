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
	
	const FIELD_UPDATED_AT = 'updated_at';
	const FIELD_RELATED_OBJECT_TYPE = 'related_object_type';
	const FIELD_EVENT_TYPE = 'event_type';
	const FIELD_OBJECT_ID = 'object_id';
	const FIELD_PRIVATE_DATA = 'private_data';
	const FIELD_RAW_DATA = 'raw_data';
	const FIELD_PARTNER_ID = 'partner_id';
	
	protected $id;
	protected $relatedObjectType;
	protected $eventType;
	protected $objectId;
	protected $privateData;
	protected $rawData;
	protected $partnerId;
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
	
	public function getUpdatedAt()
	{
		return $this->updatedAt;
	}
	
	public function index($shouldLog = false, $queueProvider = null)
	{
		kApiCache::disableConditionalCache();
		
		// get instance of activated queue provider to send message
		$queueProvider = $queueProvider ? $queueProvider : $this->getQueueProvider();
		if(!$queueProvider)
			return false;
		
		//Create base object for index
		$indexBaseObject = $this->createIndexBaseObject();
		
		//Modify base object to index to State 
		$stateIndexObjectJson = $this->getIndexObjectForState($indexBaseObject);
		$queueProvider->send(self::BEACONS_QUEUE_NAME, $stateIndexObjectJson);
		
		//Sent to log index of requested
		if ($shouldLog) 
		{
			$logIndexObjectJson = $this->getIndexObjectForLog($indexBaseObject);
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
	
	private function getIndexObjectForState($indexObject)
	{
		$docId = md5($this->relatedObjectType . '_' . $this->eventType . '_' . $this->objectId);
		
		$indexObject[self::ELASTIC_DOCUMENT_ID_KEY] = $docId;
		$indexObject[self::ELASTIC_INDEX_TYPE_KEY] = BeaconIndexType::STATE;
		
		return json_encode($indexObject);
	}
	
	private function getIndexObjectForLog($indexObject)
	{
		$indexObject[self::ELASTIC_INDEX_TYPE_KEY] = BeaconIndexType::LOG;
		
		return json_encode($indexObject);
	}
	
	private function createIndexBaseObject()
	{
		$indexObject = array();
		
		//Set Action Name and Index Name and calculated document id
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
		
		//Get current time to add to indexed object info
		$indexObject[self::FIELD_UPDATED_AT] = time();
		
		return $indexObject;
	}
}
