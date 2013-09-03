<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaContextDataResult extends KalturaObject
{	
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

	private static $mapBetweenObjects = array
	(
		'messages',
		'actions',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}