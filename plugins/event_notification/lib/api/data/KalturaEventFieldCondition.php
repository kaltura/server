<?php
/**
 * @package plugins.eventNotification
 * @subpackage api.objects
 */
class KalturaEventFieldCondition extends KalturaCondition
{	
	/**
	 * The field to be evaluated at runtime
	 * @var KalturaBooleanField
	 */
	public $field;

	private static $map_between_objects = array
	(
		'field' ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	/**
	 * Init object type
	 */
	public function __construct()
	{
		$this->type = EventNotificationPlugin::getConditionTypeCoreValue(EventNotificationConditionType::EVENT_NOTIFICATION_FIELD);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kEventFieldCondition();
	
		return parent::toObject($dbObject, $skip);
	}
	 
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function doFromObject($dbObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/* @var $dbObject kEventFieldCondition */
		parent::doFromObject($dbObject, $responseProfile);
		
		$fieldType = get_class($dbObject->getField());
		KalturaLog::debug("Loading KalturaBooleanField from type [$fieldType]");
		switch ($fieldType)
		{
			case 'kEvalBooleanField':
				$this->field = new KalturaEvalBooleanField();
				break;
				
			default:
				$this->field = KalturaPluginManager::loadObject('KalturaBooleanField', $fieldType);
				break;
		}
		
		if($this->field)
			$this->field->fromObject($dbObject->getField());
	}
}
