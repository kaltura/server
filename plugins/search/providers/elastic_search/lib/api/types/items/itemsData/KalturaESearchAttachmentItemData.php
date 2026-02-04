<?php

class KalturaESearchAttachmentItemData extends KalturaESearchItemData
{
	/**
	 * @var int
	 */
	public $pageNumber;

	/**
	 * @var string
	 */
	public $content;

	/**
	 * @var string
	 */
	public $fileName;

	/**
	 * @var string
	 */
	public $assetId;

	/**
	 * @var string
	 */
	public $assetType;

	/**
	 * @var string
	 */
	public $assetSubType;

	/**
	 * @var string
	 */
	public $tags;

	/**
	 * @var int
	 */
	public $accuracy;


	private static $map_between_objects = array(
		'pageNumber',
		'content',
		'fileName',
		'assetId',
		'assetType',
		'assetSubType',
		'tags',
		'accuracy'
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new ESearchAttachmentItemData();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
