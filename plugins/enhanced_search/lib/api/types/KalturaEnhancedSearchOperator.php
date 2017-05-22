<?php
/**
 * @package plugins.enhancedSearch
 * @subpackage api.objects
 */
class KalturaEnhancedSearchOperator extends KalturaEnhancedSearchBaseItem {


    /**
     * @var KalturaEnhancedSearchOperatorType
     */
    public $operator;

    /**
     * @var bool
     */
    public $not;

    /**
     *  @var KalturaEnhancedSearchBaseItemArray
     */
    public $searchItems;

	private static $map_between_objects = array(
		'operator',
		'not',
		'searchItems',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = new EnhancedSearchOperator();
		return parent::toObject($object_to_fill, $props_to_skip);
	}


}
