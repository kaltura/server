<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaUltraSearchOperator extends KalturaUltraSearchBaseItem {


    /**
     * @var KalturaUltraSearchOperatorType
     */
    public $operator;

    /**
     * @var bool
     */
    public $not;

    /**
     *  @var KalturaUltraSearchBaseItemArray
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
			$object_to_fill = new UltraSearchOperator();
		return parent::toObject($object_to_fill, $props_to_skip);
	}


}
