<?php
/**
 * Evaluates PHP statement, depends on the execution context
 * 
 * @package plugins.httpNotification
 * @subpackage api.objects
 */
class KalturaHttpNotificationObjectField extends KalturaEvalStringField
{
	/**
	 * Kaltura API object type
	 * @var string
	 */
	public $apiObjectType;
	
	/**
	 * Data format
	 * @var KalturaResponseType
	 */
	public $format;
	
	/**
	 * Ignore null attributes during serialization
	 * @var bool
	 */
	public $ignoreNull;

	private $map_between_objects = array
	(
		'apiObjectType' => 'objectType',
		'format',
	);

	/* (non-PHPdoc)
	 * @see KalturaValue::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$this->apiObjectType || !is_subclass_of($this->apiObjectType, 'KalturaObject'))
			throw new KalturaAPIException(KalturaHttpNotificationErrors::HTTP_NOTIFICATION_INVALID_OBJECT_TYPE);
			
		if(!$dbObject)
			$dbObject = new kHttpNotificationObjectField();
			
		return parent::toObject($dbObject, $skip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject($source_object)
	 */
	public function fromObject($sourceObject)
	{
		parent::fromObject($sourceObject);
		
		if(!$this->value)
			return;
			
		$coreObject = unserialize($this->value);
		$apiObject = new $this->apiObjectType;
		/* @var $apiObject KalturaObject */
		$apiObject->fromObject($coreObject);
		
		switch ($this->format)
		{
			case KalturaResponseType::RESPONSE_TYPE_XML:
				$serializer = new KalturaXmlSerializer($this->ignoreNull);
				$this->value = $serializer->serialize($apiObject);
				break;
				
			case KalturaResponseType::RESPONSE_TYPE_PHP:
				$serializer = new KalturaPhpSerializer($this->ignoreNull);
				$serializer->serialize($apiObject);
				$this->value = $serializer->getSerializedData();
				break;
				
			case KalturaResponseType::RESPONSE_TYPE_JSON:
				$serializer = new KalturaJsonSerializer($this->ignoreNull);
				$serializer->serialize($apiObject);
				$this->value = $serializer->getSerializedData();
				break;
		}
	}
}