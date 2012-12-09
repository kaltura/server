<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaExtendingItemMrssParameter extends KalturaObject
{
	/**
	 * XPath for the extending item
	 * @var string
	 */
	public $xpath;
	
	/**
	 * Object identifier
	 * @var KalturaObjectIdentifier
	 */
	public $identifier;
	
	private static $map_between_objects = array(
			"xpath",
			"identifier",
		);
		
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($dbObject = null, $propsToSkip = null)
	{
		if (!$dbObject)
			$dbObject = new KExtendingItemMrssParameter();
			
		return parent::toObject($dbObject, $propsToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject($source_object)
	 */
	public function fromObject ($dbObject)
	{
		parent::fromObject($dbObject);
		
		/* @var $dbObject kExtendingItemMrssParameter */
		$identifierType = get_class($dbObject->getIdentifier());
		KalturaLog::info("Creating identifier for DB identifier type $identifierType");
		switch ($identifierType)
		{
			case 'kEntryIdentifier':
				$this->identifier = new KalturaEntryIdentifier();
				break;
			case 'kCategoryIdentifier':
				$this->identifier = new KalturaCategoryIdentifier();
		}
		
		$this->identifier->fromObject($dbObject->getIdentifier());
		
	}
}