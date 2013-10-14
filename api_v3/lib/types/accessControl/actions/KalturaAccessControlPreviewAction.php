<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAccessControlPreviewAction extends KalturaRuleAction
{
	/**
	 * @var int
	 */
	public $limit;
	
	private static $mapBetweenObjects = array
	(
		'limit',
	);

	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = RuleActionType::PREVIEW;
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
			$dbObject = new kAccessControlPreviewAction();
			
		return parent::toObject($dbObject, $skip);
	}
}