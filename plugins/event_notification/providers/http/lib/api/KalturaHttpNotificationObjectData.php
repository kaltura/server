<?php
/**
 * Evaluates PHP statement, depends on the execution context
 * 
 * @package plugins.httpNotification
 * @subpackage api.objects
 */
class KalturaHttpNotificationObjectData extends KalturaHttpNotificationData
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
	
	/**
	 * PHP code
	 * @var string
	 */
	public $code;
	
	/**
	 * Serialized object, protected on purpose, used by getData
	 * @see KalturaHttpNotificationObjectData::getData()
	 * @var string
	 */
	protected $coreObject;

	static private $map_between_objects = array
	(
		'apiObjectType' => 'objectType',
		'format',
		'ignoreNull',
		'code',
	);

	/* (non-PHPdoc)
	 * @see KalturaValue::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$this->apiObjectType || !is_subclass_of($this->apiObjectType, 'KalturaObject'))
			throw new KalturaAPIException(KalturaHttpNotificationErrors::HTTP_NOTIFICATION_INVALID_OBJECT_TYPE);
			
		if(!$dbObject)
			$dbObject = new kHttpNotificationObjectData();
			
		return parent::toObject($dbObject, $skip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject($srcObj)
	 */
	public function fromObject($srcObj)
	{
		/* @var $srcObj kHttpNotificationObjectData */
		parent::fromObject($srcObj);
		$this->coreObject = $srcObj->getCoreObject();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaHttpNotificationData::getData()
	 */
	public function getData(kHttpNotificationDispatchJobData $jobData = null)
	{
		$coreObject = unserialize($this->coreObject);
		$apiObject = new $this->apiObjectType;
		/* @var $apiObject KalturaObject */
		$apiObject->fromObject($coreObject);
		
		$httpNotificationTemplate = EventNotificationTemplatePeer::retrieveByPK($jobData->getTemplateId());
		
		$notification = new KalturaHttpNotification();
		$notification->object = $apiObject;
		$notification->eventObjectType = $httpNotificationTemplate->getObjectType();
		$notification->eventNotificationJobId = $jobData->getJobId();
		$notification->templateId = $httpNotificationTemplate->getId();
		$notification->templateName = $httpNotificationTemplate->getName();
		$notification->templateSystemName = $httpNotificationTemplate->getSystemName();
		$notification->eventType = $httpNotificationTemplate->getEventType();;

		$data = '';
		switch ($this->format)
		{
			case KalturaResponseType::RESPONSE_TYPE_XML:
				$serializer = new KalturaXmlSerializer($this->ignoreNull);
				$data = '<notification>' . $serializer->getSerializedData($notification) . '</notification>';
				break;
				
			case KalturaResponseType::RESPONSE_TYPE_PHP:
				$serializer = new KalturaPhpSerializer($this->ignoreNull);
				$serializer->serialize($notification);
				$data = $serializer->getSerializedData();
				break;
				
			case KalturaResponseType::RESPONSE_TYPE_JSON:
				$serializer = new KalturaJsonSerializer($this->ignoreNull);
				$serializer->serialize($notification);
				$data = $serializer->getSerializedData();
				break;
		}
		
		return "data=$data";
	}
}