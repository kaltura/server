<?php
/**
 * Represents the current request http headers context
 *
 * @package api
 * @subpackage objects
 */

class KalturaHttpHeaderContextField extends KalturaStringField
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


	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kHttpHeaderContextField();

		return parent::toObject($dbObject, $skip);
	}

}