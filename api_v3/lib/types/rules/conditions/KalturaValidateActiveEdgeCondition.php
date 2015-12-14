<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaValidateActiveEdgeCondition extends KalturaCondition
{	
	/**
	 * Comma separated list of edge servers to validate are active
	 * 
	 * @var string
	 */
	public $edgeServerIds;
	
	/**
	 * Time limit since last heartbeat in seconds
	 *
	 * @var int
	 */
	public $ttl;
	
	private static $mapBetweenObjects = array
	(
		'edgeServerIds',
		'ttl',
	);
	
	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = ConditionType::ACTIVE_EDGE_VALIDATE;
	}
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kValidateActiveEdgeCondition();
			
		return parent::toObject($dbObject, $skip);
	}
}
