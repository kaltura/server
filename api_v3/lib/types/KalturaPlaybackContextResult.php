<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPlaybackContextResult extends KalturaObject{

	/**
	 * @var KalturaPlaybackSourceArray
	 */
	public $sources;
    
	/**
	 * @var KalturaFlavorAssetArray
	 */
	public $flavorAssets;

	/**
	 * Array of messages as received from the rules that invalidated
	 * @var KalturaStringArray
	 */
	public $messages;

	/**
	 * Array of actions as received from the rules that invalidated
	 * @var KalturaRuleActionArray
	 */
	public $actions;

	/**
	 * Array of actions as received from the rules that invalidated
	 * @var KalturaPlaybackRestrictionsArray
	 */
	public $restrictions;

	private static $mapBetweenObjects = array
	(
		'flavorAssets',
		'sources',
		'restrictions',
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}