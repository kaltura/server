<?php
/**
 * @package plugins.tagSearch
 * @subpackage api.filters
 */
class KalturaTagFilter extends KalturaFilter
{
 	private $map_between_objects = array
	(
		"objectTypeEqual" => "_eq_object_type",
		"tagEqual" => "_eq_tag",
		"tagStartsWith" => "_likex_tag",
	    "instanceCountEqual" => "_eq_instance_count",
	    "instanceCountIn" => "in_instance_count", 
	);

	private $order_by_map = array
	(
		"+instanceCount" => "+instance_count",
	    "-instanceCount" => "-instance_count",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), $this->order_by_map);
	}

	public function validate()
	{
	    KalturaLog::debug("In TagFilter validation");
		$this->validatePropertyMinLength('tagStartsWith', TagSearchPlugin::MIN_TAG_SEARCH_LENGTH, true);
		$this->validatePropertyMinLength('tagEqual', TagSearchPlugin::MIN_TAG_SEARCH_LENGTH, true);
		$this->validatePropertyNotNull(array ('tagEqual','tagStartsWith'), true);
		
	}


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
   
}