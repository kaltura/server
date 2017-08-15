<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchMetadataItemData extends KalturaESearchItemData
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
	 * @var string
	 */
	public $valueText;

	/**
	 * @var int
	 */
	public $valueInt;

	private static $map_between_objects = array(
		'xpath',
		'metadataProfileId',
		'valueText',
		'valueInt',
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = new ESearchMetadataItemData();
		return parent::toObject($object_to_fill, $props_to_skip);
	}


}
