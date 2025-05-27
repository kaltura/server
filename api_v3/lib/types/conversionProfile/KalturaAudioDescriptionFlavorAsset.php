<?php
/**
 * @package api
 * @subpackage objects
 * @relatedService FlavorAssetService
 */
class KalturaAudioDescriptionFlavorAsset extends KalturaFlavorAsset
{
	/**
	 * The desired order of the flavor
	 *
	 * @var int
	 */
	public $order;

	private static $map_between_objects = array
	(
		"order"
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toInsertableObject($sourceObject = null, $propsToSkip = array())
	{
		$sourceObject = new AudioDescriptionAsset();
		return parent::toInsertableObject ($sourceObject, $propsToSkip);
	}
}
