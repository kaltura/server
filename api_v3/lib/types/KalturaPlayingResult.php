<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPlayingResult extends KalturaObject{

	/**
	 * @var KalturaPlayingSourceArray
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


}