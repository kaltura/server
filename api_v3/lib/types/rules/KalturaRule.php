<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaRule extends KalturaObject
{
	/**
	 * Short Rule Description
	 *
	 * @var string
	 */
	public $description;
	
	/**
	 * Rule Custom Data to allow saving rule specific information 
	 *
	 * @var string
	 */
	public $ruleData;
	
	/**
	 * Message to be thrown to the player in case the rule is fulfilled
	 * 
	 * @var string
	 */
	public $message;
	
	/**
	 * Actions to be performed by the player in case the rule is fulfilled
	 * 
	 * @var KalturaRuleActionArray
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
	 * @var KalturaContextTypeHolderArray
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
		'description',
		'ruleData',
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
