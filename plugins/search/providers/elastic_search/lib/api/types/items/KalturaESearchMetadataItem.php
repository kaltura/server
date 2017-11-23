<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchMetadataItem extends KalturaESearchItem
{
	/**
	 * @var string
	 */
	public $xpath;

	/**
	 * @var int
	 */
	public $metadataProfileId;

	/**
	 * @var int
	 */
	public $metadataFieldId;

	private static $map_between_objects = array(
		'xpath',
		'metadataProfileId',
		'metadataFieldId',
	);

	private static $map_dynamic_enum = array();

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = new ESearchMetadataItem();
		return parent::toObject($object_to_fill, $props_to_skip);
	}

	protected function getItemFieldName()
	{
		return null;
	}

	protected function getDynamicEnumMap()
	{
		return self::$map_dynamic_enum;
	}

}
