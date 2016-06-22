<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAccessControlLimitThumbnailCaptureAction extends KalturaRuleAction
{
	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = RuleActionType::LIMIT_THUMBNAIL_CAPTURE;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kAccessControlLimitThumbnailCaptureAction();
			
		return parent::toObject($dbObject, $skip);
	}
}