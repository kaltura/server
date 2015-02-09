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
	
	/**
	 * Mode of extension - append to MRSS or replace the xpath content.
	 * @var KalturaMrssExtensionMode
	 */
	public $extensionMode;
	
	
	private static $map_between_objects = array(
			"xpath",
			"identifier",
			"extensionMode"
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
	public function toObject($dbObject = null, $propsToSkip = array())
	{
		$this->validate();
		if (!$dbObject)
			$dbObject = new kExtendingItemMrssParameter();

		return parent::toObject($dbObject, $propsToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject($source_object)
	 */
	public function fromObject($dbObject, IResponseProfile $responseProfile = null)
	{
		parent::fromObject($dbObject, $responseProfile);
		
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
		
		if ($this->identifier)
			$this->identifier->fromObject($dbObject->getIdentifier());
	}
	
	protected function validate ()
	{
		//Should not allow any extending object but entries to be added in APPEND mode
		if ($this->extensionMode == KalturaMrssExtensionMode::APPEND && get_class($this->identifier) !== 'KalturaEntryIdentifier')
		{
			throw new KalturaAPIException(KalturaErrors::EXTENDING_ITEM_INCOMPATIBLE_COMBINATION);
		}
		
		if (!$this->xpath)
		{
			throw new KalturaAPIException(KalturaErrors::EXTENDING_ITEM_MISSING_XPATH);
		}
	}
}