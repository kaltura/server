<?php
/**
 * @package plugins.eventNotification
 * @subpackage api.objects
 */
class KalturaEventNotificationParameter extends KalturaObject
{
	/**
	 * The key in the subject and body to be replaced with the dynamic value
	 * @var string
	 */
	public $key;

	/**
	 * @var string
	 */
	public $description;
	
	/**
	 * The dynamic value to be placed in the final output
	 * @var KalturaStringValue
	 */
	public $value;
	
	private static $map_between_objects = array
	(
		'key',
		'description',
		'value',
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kEventNotificationParameter();
			
		return parent::toObject($dbObject, $skip);
	}
	 
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function fromObject($dbObject)
	{
		/* @var $dbObject kEventValueCondition */
		parent::fromObject($dbObject);
		
		$valueType = get_class($dbObject->getValue());
		KalturaLog::debug("Loading KalturaStringValue from type [$valueType]");
		switch ($valueType)
		{
			case 'kStringValue':
				$this->value = new KalturaStringValue();
				break;
				
			case 'kEvalStringField':
				$this->value = new KalturaEvalStringField();
				break;
				
			default:
				$this->value = KalturaPluginManager::loadObject('KalturaStringValue', $valueType);
				break;
		}
		
		if($this->value)
			$this->value->fromObject($dbObject->getValue());
	}
}