<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaResponseProfileCacheRecalculateResults extends KalturaObject
{
	/**
	 * Last recalculated id
	 * 
	 * @var string
	 */
	public $lastKeyId;
	
	/**
	 * Number of recalculated keys
	 * 
	 * @var int
	 */
	public $recalculated;
}