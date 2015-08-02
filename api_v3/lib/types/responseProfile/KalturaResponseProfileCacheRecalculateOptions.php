<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaResponseProfileCacheRecalculateOptions extends KalturaObject
{
	/**
	 * Maximum number of keys to recalculate
	 * 
	 * @var int
	 */
	public $limit;
	
	/**
	 * Class name
	 * @var string
	 */
	public $cachedObjectType;
	
	/**
	 * @var string
	 */
	public $objectId;
	
	/**
	 * @var string
	 */
	public $startObjectKey;
	
	/**
	 * @var string
	 */
	public $endObjectKey;
	
	/**
	 * @var time
	 */
	public $jobCreatedAt;
	
	/**
	 * @var bool
	 */
	public $isFirstLoop;
}