<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaResponseProfileCacheRecalculateOptions extends KalturaObject
{
	/**
	 * Unique identifier of object type and id
	 * 
	 * @var string
	 */
	public $objectKey;
	
	/**
	 * First id to recalculate
	 * 
	 * @var string
	 */
	public $startKeyId;
	
	/**
	 * Last id to recalculate
	 * 
	 * @var string
	 */
	public $endKeyId;
	
	/**
	 * Maximum number of keys to recalculate
	 * 
	 * @var int
	 */
	public $limit;
}