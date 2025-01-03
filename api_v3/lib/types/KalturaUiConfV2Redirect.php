<?php
/**
 * @package api
 * @subpackage objects
 */

class KalturaUiConfV2Redirect extends KalturaObject
{
	/**
	 * @var int
	 */
	public $v7id;

	/**
	 * @var bool
	 */
	public $isApproved;

	/**
	 * @var bool
	 */
	public $translatePlugins;

	private static $map_between_objects = array
	(
		"v7id" ,
		"isApproved" ,
		"translatePlugins"
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		$this->validatePropertyNumeric('v7id');
		$this->validatePropertyNotNull('isApproved');
		$this->validatePropertyNotNull('translatePlugins');
		$uiConfV2Redirect = new uiConfV2Redirect();
		return parent::toObject($uiConfV2Redirect, $props_to_skip);
	}

	public function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$this->v7id = $srcObj->getV7Id();
		$this->isApproved = $srcObj->getIsApproved();
		$this->translatePlugins = $srcObj->getTranslatePlugins();
		parent::doFromObject($srcObj, $responseProfile);
	}
}
