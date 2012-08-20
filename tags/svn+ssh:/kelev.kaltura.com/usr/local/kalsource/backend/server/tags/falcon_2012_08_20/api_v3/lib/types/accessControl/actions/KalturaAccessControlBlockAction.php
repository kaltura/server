<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAccessControlBlockAction extends KalturaAccessControlAction
{
	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = accessControlActionType::BLOCK;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kAccessControlAction(accessControlActionType::BLOCK);
			
		return parent::toObject($dbObject, $skip);
	}
}