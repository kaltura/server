<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaRule extends KalturaObject
{
	/**
	 * Message to be thrown to the player in case the rule fulfilled
	 * 
	 * @var string
	 */
	public $message;
	
	/**
	 * Actions to be performed by the player in case the rule fulfilled
	 * 
	 * @var KalturaAccessControlActionArray
	 */
	public $actions;
	
	/**
	 * Conditions to validate the rule
	 * 
	 * @var KalturaConditionArray
	 */
	public $conditions;
	
	/**
	 * Indicates what contexts should be tested by this rule 
	 * 
	 * @var KalturaAccessControlContextTypeHolderArray
	 */
	public $contexts;
	
	/**
	 * Indicates that this rule is enough and no need to continue checking the rest of the rules 
	 * 
	 * @var bool
	 */
	public $stopProcessing;

	private static $mapBetweenObjects = array
	(
		'message',
		'actions',
		'conditions',
		'contexts',
		'stopProcessing',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kRule();
			
		return parent::toObject($dbObject, $skip);
	}
}
