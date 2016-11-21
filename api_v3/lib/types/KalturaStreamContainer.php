<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaStreamContainer extends KalturaObject
{
	/**
	 * @var string
	 */
	public $type;
	/**
	 * @var int
	 */
	public $trackIndex;

	/**
	 * @var string
	 */
	public $language;

	/**
	 * @var int
	 */
	public $channelIndex;

	/**
	 * @var string
	 */
	public $label;

	/**
	 * @var string
	 */
	public $channelLayout;
	
	private static $mapBetweenObjects = array
	(
		"type",
		"trackIndex",
		"language",
		"channelIndex",
		"label",
		"channelLayout",
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new kStreamContainer();
		}

		return parent::toObject($dbObject, $propsToSkip);
	}
}