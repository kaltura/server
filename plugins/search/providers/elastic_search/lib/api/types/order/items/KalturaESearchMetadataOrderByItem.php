<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchMetadataOrderByItem extends KalturaESearchOrderByItem
{
	/**
	 *  @var string
	 */
	public $xpath;

	/**
	 *  @var int
	 */
	public $metadataProfileId;

	private static $map_between_objects = array(
		'xpath',
		'metadataProfileId',
	);

	private static $map_field_enum = array();

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = new ESearchMetadataOrderByItem();
		return parent::toObject($object_to_fill, $props_to_skip);
	}

	public function getFieldEnumMap()
	{
		return self::$map_field_enum;
	}

	public function getItemFieldName()
	{
		return null;
	}
}
