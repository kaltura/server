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
	 * Code to be thrown to the player in case the rule is fulfilled
	 *
	 * @var string
	 */
	public $code;
	
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
	
	/**
	 * Indicates if we should force ks validation for admin ks users as well
	 *
	 * @var KalturaNullableBoolean
	 */
	public $forceAdminValidation;
	
	/**
	 * Rule creation date as Unix timestamp (In seconds)
	 * @var time
	 */
	public $ruleCreationTime;
	
	/**
	 * The ID of the user who created the rule
	 * @var string
	 */
	public $ruleCreator;

	private static $mapBetweenObjects = array
	(
		'description',
		'ruleData',
		'message',
		'code',
		'actions',
		'conditions',
		'contexts',
		'stopProcessing',
		'forceAdminValidation',
		'ruleCreationTime',
		'ruleCreator',
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
