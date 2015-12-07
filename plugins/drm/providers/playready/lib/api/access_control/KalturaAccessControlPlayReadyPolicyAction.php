<?php
/**
 * @package plugins.playReady
 * @subpackage api.objects
 */
class KalturaAccessControlPlayReadyPolicyAction extends KalturaRuleAction
{
	/**
	 * Play ready policy id 
	 * 
	 * @var int
	 */
	public $policyId;

	private static $mapBetweenObjects = array
	(
		'policyId',
	);

	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = PlayReadyAccessControlActionType::DRM_POLICY;
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
			$dbObject = new kAccessControlPlayReadyPolicyAction();
			
		return parent::toObject($dbObject, $skip);
	}
}
