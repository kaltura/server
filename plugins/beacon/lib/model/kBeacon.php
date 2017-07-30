<?php

/**
 * @package plugins.beacon
 * @subpackage model
 */

class kBeacon
{
	const RELATED_OBJECT_TYPE_STRING	= 'relatedObjectType';
	const EVENT_TYPE_STRING				= 'eventType';
	const OBJECT_ID_STRING				= 'objectId';
	const PRIVATE_DATA_STRING			= 'privateData';
	
	protected $relatedObjectType;
	protected $eventType;
	protected $objectId;
	protected $privateData;
	protected $partnerId;

	public function __construct()
	{
		$this->setPartnerId(kCurrentContext::getCurrentPartnerId());
	}
	
	public function setRelatedObjectType($relatedObjectType) 	{ $this->relatedObjectType = $relatedObjectType; }
	public function setEventType($eventType) 					{ $this->eventType = $eventType; }
	public function setObjectId($objectId)						{ $this->objectId = $objectId; }	
	public function setPrivateData($privateData) 				{ $this->privateData = $privateData; }
	public function setPartnerId($partnerId)					{ $this->partnerId = $partnerId; }
	
	public function getRelatedObjectType()						{ return $this->relatedObjectType; }
	public function getEventType()								{ return $this->eventType; }
	public function getObjectId()								{ return $this->objectId; }
	public function getPrivateData()							{ return $this->privateData; }
	public function getPartnerId()								{ return $this->partnerId; }
	
	public function index($shouldLog, $ttl)
	{
		$beaconObject = $this->prepareBeaconObject();
		$this->indexObjectState($beaconObject);
		if($shouldLog)
			$this->logObjectState($beaconObject, $ttl);
	}
	
	//Todo add map between objects
	public function indexObjectState(BeaconObject $beaconObject)
	{
		$id = md5($this->relatedObjectType.'_'. $this->eventType.'_'.$this->objectId);
		$beaconObject->indexObjectState($id);
	}
	
	public function logObjectState(BeaconObject $beaconObject, $ttl)
	{
		$ret = $beaconObject->log($ttl);
		return $ret;
	}
	
	private function prepareBeaconObject()
	{
		$indexObject=array();
		//$indexObject[self::PRIVATE_DATA_STRING] = json_decode($this->privateData,true);
		$indexObject[self::PRIVATE_DATA_STRING] = $this->privateData;
		$indexObject[self::RELATED_OBJECT_TYPE_STRING] = $this->relatedObjectType;
		$indexObject[self::EVENT_TYPE_STRING] = $this->eventType;
		$indexObject[self::OBJECT_ID_STRING] = $this->objectId;
		$beaconObject = new BeaconObject(kCurrentContext::getCurrentPartnerId(),$indexObject);
		return $beaconObject;
	}
}
