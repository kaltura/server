<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFieldMatchCondition extends KalturaMatchCondition
{
	/**
	 * Field to evaluate
	 * @var KalturaStringField
	 */
	public $field;
	
	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = ConditionType::FIELD_MATCH;
	}
	 
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kFieldMatchCondition();
	
		/* @var $dbObject kFieldMatchCondition */
		$dbObject->setField($this->field->toObject());
			
		return parent::toObject($dbObject, $skip);
	}
	 
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function doFromObject($dbObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/* @var $dbObject kFieldMatchCondition */
		parent::doFromObject($dbObject, $responseProfile);
		
		$fieldType = get_class($dbObject->getField());
		KalturaLog::debug("Loading KalturaStringField from type [$fieldType]");
		switch ($fieldType)
		{
			case 'kCountryContextField':
				$this->field = new KalturaCountryContextField();
				break;
				
			case 'kIpAddressContextField':
				$this->field = new KalturaIpAddressContextField();
				break;
				
			case 'kUserAgentContextField':
				$this->field = new KalturaUserAgentContextField();
				break;
				
			case 'kUserEmailContextField':
				$this->field = new KalturaUserEmailContextField();
				break;
				
			case 'kCoordinatesContextField':
				$this->field = new KalturaCoordinatesContextField();
				break;

			case 'kAnonymousIPContextField':
				$this->field = new KalturaAnonymousIPContextField();
				break;

			case 'kEvalStringField':
			    $this->field = new KalturaEvalStringField();
			    break;
			
			case 'kObjectIdField':
			    $this->field = new KalturaObjectIdField();
			    break;				
				
			case 'kEvalStringField':
				$this->field = new KalturaEvalStringField();
				break;
				
			case 'kObjectIdField':
				$this->field = new KalturaObjectIdField();
				break;
				
			default:
				$this->field = KalturaPluginManager::loadObject('KalturaStringField', $fieldType);
				break;
		}
		
		if($this->field)
			$this->field->fromObject($dbObject->getField());
	}
}
