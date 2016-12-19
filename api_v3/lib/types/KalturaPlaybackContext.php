<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPlaybackContext extends KalturaObject{

	/**
	 * @var KalturaPlaybackSourceArray
	 */
	public $sources;
    
	/**
	 * @var KalturaFlavorAssetArray
	 */
	public $flavorAssets;

	/**
	 * Array of actions as received from the rules that invalidated
	 * @var KalturaRuleActionArray
	 */
	public $actions;

	/**
	 * Array of actions as received from the rules that invalidated
	 * @var KalturaAccessControlMessageArray
	 */
	public $messages;

	private static $mapBetweenObjects = array
	(
		'flavorAssets',
		'sources',
		'messages',
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}