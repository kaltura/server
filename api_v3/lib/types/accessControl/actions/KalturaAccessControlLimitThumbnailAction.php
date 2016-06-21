<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAccessControlLimitThumbnailAction extends KalturaRuleAction
{
	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = RuleActionType::LIMIT_THUMBNAIL;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kAccessControlLimitThumbnailAction();
			
		return parent::toObject($dbObject, $skip);
	}
}