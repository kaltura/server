<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchCaptionItemData extends KalturaESearchItemData {

	/**
	 * @var string
	 */
	public $line;

	/**
	 * @var int
	 */
	public $startsAt;

	/**
	 * @var int
	 */
	public $endsAt;

	/**
	 * @var string
	 */
	public $language;

	/**
	 * @var string
	 */
	public $captionAssetId;

	/**
	 * @var string
	 */
	public $label;

	private static $map_between_objects = array(
		'line',
		'startsAt',
		'endsAt',
		'language',
		'captionAssetId',
		'label',
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = new ESearchCaptionItemData();
		return parent::toObject($object_to_fill, $props_to_skip);
	}


}
