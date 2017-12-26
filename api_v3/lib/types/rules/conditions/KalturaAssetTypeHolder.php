<?php
/**
 * @package api
 * @subpackage objects
 * @abstract
 */
class KalturaAssetTypeHolder extends KalturaObject
{
	/**
	 * The type of the action
	 *
	 * @var KalturaAssetType
	 */
	public $type;

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		return $this->type;
	}

	private static $mapBetweenObjects = array
	(
		'type',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}