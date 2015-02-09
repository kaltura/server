<?php
/**
 * @package api
 * @subpackage objects
 * @abstract
 */
abstract class KalturaCompareCondition extends KalturaCondition
{
	/**
	 * Value to evaluate against the field and operator
	 * @var KalturaIntegerValue
	 */
	public $value;
	
	/**
	 * Comparing operator
	 * @var KalturaSearchConditionComparison
	 */
	public $comparison;
	
	private static $mapBetweenObjects = array
	(
		'comparison',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		/* @var $dbObject kCompareCondition */
		$dbObject->setValue($this->value->toObject());
			
		return parent::toObject($dbObject, $skip);
	}
	 
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function fromObject($dbObject, IResponseProfile $responseProfile = null)
	{
		/* @var $dbObject kFieldMatchCondition */
		parent::fromObject($dbObject, $responseProfile);
		
		$valueType = get_class($dbObject->getValue());
		KalturaLog::debug("Loading KalturaIntegerValue from type [$valueType]");
		switch ($valueType)
		{
			case 'kIntegerValue':
				$this->value = new KalturaIntegerValue();
				break;
				
			case 'kTimeContextField':
				$this->value = new KalturaTimeContextField();
				break;
				
			default:
				$this->value = KalturaPluginManager::loadObject('KalturaIntegerValue', $valueType);
				break;
		}
		
		if($this->value)
			$this->value->fromObject($dbObject->getValue());
	}
}
