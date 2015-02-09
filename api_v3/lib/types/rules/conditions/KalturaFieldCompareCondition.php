<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFieldCompareCondition extends KalturaCompareCondition
{
	/**
	 * Field to evaluate
	 * @var KalturaIntegerField
	 */
	public $field;
	 
	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = ConditionType::FIELD_COMPARE;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kFieldCompareCondition();
	
		/* @var $dbObject kFieldCompareCondition */
		$dbObject->setField($this->field->toObject());
			
		return parent::toObject($dbObject, $skip);
	}
	 
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function fromObject($dbObject, IResponseProfile $responseProfile = null)
	{
		/* @var $dbObject kFieldMatchCondition */
		parent::fromObject($dbObject, $responseProfile);
		
		$fieldType = get_class($dbObject->getField());
		KalturaLog::debug("Loading KalturaIntegerField from type [$fieldType]");
		switch ($fieldType)
		{
			case 'kTimeContextField':
				$this->field = new KalturaTimeContextField();
				break;
				
			default:
				$this->field = KalturaPluginManager::loadObject('KalturaIntegerField', $fieldType);
				break;
		}
		
		if($this->field)
			$this->field->fromObject($dbObject->getField());
	}
}
