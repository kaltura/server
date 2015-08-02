<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAccessControlServeRemoteEdgeServerAction extends KalturaRuleAction
{
	/**
	 * Comma separated list of edge servers playBack should be done from
	 * 
	 * @var string
	 */
	public $edgeServerIds;
	
	private static $mapBetweenObjects = array
	(
		'edgeServerIds',
	);
	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = RuleActionType::SERVE_FROM_REMOTE_SERVER;
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
			$dbObject = new kAccessControlServeRemoteEdgeServerAction();
			
		return parent::toObject($dbObject, $skip);
	}
}