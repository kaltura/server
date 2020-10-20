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
	 * @var KalturaCaptionPlaybackPluginDataArray
	 */
	public $playbackCaptions;

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

	/**
	* @var KalturaKeyValueArray
	*/
	public $bumperData;

	private static $mapBetweenObjects = array
	(
		'playbackCaptions',
		'flavorAssets',
		'sources',
		'messages',
		'bumperData',
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}
