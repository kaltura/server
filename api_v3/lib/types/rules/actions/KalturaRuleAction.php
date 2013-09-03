<?php
/**
 * @package api
 * @subpackage objects
 * @abstract
 */
abstract class KalturaRuleAction extends KalturaObject
{
	/**
	 * The type of the action
	 * 
	 * @readonly
	 * @var KalturaRuleActionType
	 */
	public $type;
}