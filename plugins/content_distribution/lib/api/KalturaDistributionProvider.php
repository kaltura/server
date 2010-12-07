<?php
/**
 * @abstract
 */
class KalturaDistributionProvider extends KalturaObject implements IFilterable
{
	/**
	 * @readonly
	 * @var KalturaDistributionProviderType
	 * @filter eq,in
	 */
	public $type;

	/**
	 * @var string
	 */
	public $name;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	 (
		'type',
		'name',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function getExtraFilters()
	{
		return array(
		);
	}
	
	public function getFilterDocs()
	{
		return array(
		);
	}
}