<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaHttpHeaderCondition extends KalturaRegexCondition
{

	/**
	 * header name
	 * @var string
	 */
	public $headerName;

	private static $mapBetweenObjects = array
	(
		'headerName',
	);


	/* (non-PHPdoc)
	 * @see KalturaMatchCondition::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}


	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUsage()
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUsage($sourceObject, $propertiesToSkip);

		$this->validatePropertyNotNull('headerName');
	}


	/**
	 * Init object type
	 */
	public function __construct()
	{
		$this->type = ConditionType::HTTP_HEADER;
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kHttpHeaderCondition();

		return parent::toObject($dbObject, $skip);
	}

}