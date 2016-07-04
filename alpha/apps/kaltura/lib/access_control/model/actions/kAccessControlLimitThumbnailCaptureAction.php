<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kAccessControlLimitThumbnailCaptureAction extends kRuleAction 
{
	public function __construct() 
	{
		parent::__construct(RuleActionType::LIMIT_THUMBNAIL_CAPTURE);
	}
}
