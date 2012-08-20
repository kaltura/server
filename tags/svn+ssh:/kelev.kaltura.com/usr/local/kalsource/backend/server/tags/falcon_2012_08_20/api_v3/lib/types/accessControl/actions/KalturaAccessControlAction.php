<?php
/**
 * @package api
 * @subpackage objects
 * @abstract
 */
abstract class KalturaAccessControlAction extends KalturaObject
{
	/**
	 * The type of the access control action
	 * 
	 * @readonly
	 * @var KalturaAccessControlActionType
	 */
	public $type;
}