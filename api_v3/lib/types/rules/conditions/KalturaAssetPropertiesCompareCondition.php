<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAssetPropertiesCompareCondition extends KalturaCondition
{
	/**
	 * Array of key/value objects that holds the property and the value to find and compare on an asset object
	 *
	 * @var KalturaKeyValueArray
	 */
	public $properties;

	private static $mapBetweenObjects = array
	(
		'properties',
	);

	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = ConditionType::ASSET_PROPERTIES_COMPARE;
	}

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kAssetPropertiesCompareCondition();

		$dbObject = parent::toObject($dbObject, $skip);

		if (!is_null($this->properties))
		{
			$properties = array();
			foreach($this->properties as $keyValue)
				$properties[$keyValue->key] = $keyValue->value;
			$dbObject->setProperties($properties);
		}

		return $dbObject;
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function fromObject($dbObject, KalturaResponseProfileBase $responseProfile = null)
	{
		/** @var $dbObject kAssetPropertiesCompareCondition */
		parent::fromObject($dbObject, $responseProfile);
		$this->properties = KalturaKeyValueArray::fromKeyValueArray($dbObject->getProperties());
	}
}
