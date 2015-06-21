<?php
/**
 * @package plugins.tagSearch
 * @subpackage api.filters
 */
class KalturaTagFilter extends KalturaFilter
{
    /**
	 * 
	 * 
	 * @var KalturaTaggedObjectType
	 */
	public $objectTypeEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $tagEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $tagStartsWith;
	
	/**
	 * @var int
	 */
	public $instanceCountEqual;
	
	/**
	 * @var int
	 */
    public $instanceCountIn;
    
 	static private $map_between_objects = array
	(
		"objectTypeEqual" => "_eq_object_type",
	    "instanceCountEqual" => "_eq_instance_count",
	    "instanceCountIn" => "_in_instance_count", 
	);

	static private $order_by_map = array
	(
		"+instanceCount" => "+instance_count",
	    "-instanceCount" => "-instance_count",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), self::$order_by_map);
	}

	public function validate()
	{
		$this->leaveOnlyOneSpaceOnTheRight(array ('tagStartsWith'));
		$this->trimStringProperties(array('tagEqual'));
		$this->validatePropertyMinLength('tagStartsWith', TagSearchPlugin::MIN_TAG_SEARCH_LENGTH, true, false);
		$this->validatePropertyMinLength('tagEqual', TagSearchPlugin::MIN_TAG_SEARCH_LENGTH, true, true);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new TagFilter();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaFilter::toObject()
	 */
	public function toObject ($object = null, $props_to_skip = array())
	{
		/* @var $object TagFilter */
		$object->set ('_eq_tag', str_replace(kTagFlowManager::$specialCharacters, kTagFlowManager::$specialCharactersReplacement, $this->tagEqual));
		$object->set ('_likex_tag', str_replace(kTagFlowManager::$specialCharacters, kTagFlowManager::$specialCharactersReplacement, $this->tagStartsWith));
		
		return parent::toObject($object, $props_to_skip);
	}

	/*
	 * This method trims the spaces on the left. If there are spaces on the right it trims them and leaves exactly one.
	 */
	public function leaveOnlyOneSpaceOnTheRight(array $propertyNames)
	{
		foreach ($propertyNames as $propertyName)
		{
			if (!$this->isNull($propertyName))
			{
				$this->$propertyName = ltrim($this->$propertyName);
				$this->$propertyName = preg_replace("/\s+$/", " ", $this->$propertyName);
			}
		}
	}
}