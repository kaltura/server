<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFlavorTypeCondition extends KalturaCondition
{
	/**
	 * @var string comma separated
	 */
	public $flavorTypes;

	private static $mapBetweenObjects = array
	(
		'flavorTypes',
	);

	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = ConditionType::FLAVOR_TYPE;
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
			$dbObject = new kFlavorTypeCondition();

		$dbObject = parent::toObject($dbObject, $skip);

		if (!is_null($this->flavorTypes))
		{
			$flavorTypes = explode(',', $this->flavorTypes);
			$dbObject->setFlavorTypes($flavorTypes);
		}

		return $dbObject;
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function doFromObject($dbObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/** @var $dbObject kFlavorTypeCondition */
		parent::doFromObject($dbObject, $responseProfile);
		
		if($this->shouldGet('flavorTypes', $responseProfile))
			$this->flavorTypes = implode(',', $dbObject->getFlavorTypes());
	}
}
