<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
abstract class KalturaESearchAbstractEntryItem extends KalturaESearchEntryBaseItem
{

	/**
	 * @var string
	 */
	public $searchTerm;
	
	/**
	 * @var KalturaESearchItemType
	 */
	public $itemType;
	
	/**
	 * @var KalturaESearchRange
	 */
	public $range;

	/**
	 * @var bool
	 */
	public $addHighlight;

	private static $map_between_objects = array(
		'searchTerm',
		'itemType',
		'range',
		'addHighlight',
	);
	
	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	abstract protected function getItemFieldName();
	
	abstract protected function getDynamicEnumMap();
	
	abstract protected function getFieldEnumMap();
	
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		list($object_to_fill, $props_to_skip) =
			KalturaESearchItemImpl::eSearchComplexItemToObjectImpl($this, $this->getDynamicEnumMap(), $this->getItemFieldName(), $this->getFieldEnumMap(), $object_to_fill, $props_to_skip);

		if ($object_to_fill instanceof ESearchOperator)
		{
			$searchItems = $object_to_fill->getSearchItems();
			foreach ($searchItems as $searchItem)
			{
				$searchItem = parent::toObject($searchItem, $props_to_skip);
			}
			return $object_to_fill;
		}
		else
		{
			return parent::toObject($object_to_fill, $props_to_skip);
		}
	}
	
}
