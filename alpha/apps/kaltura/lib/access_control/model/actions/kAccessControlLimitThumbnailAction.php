<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kAccessControlLimitThumbnailAction extends kRuleAction 
{
	public function __construct() 
	{
		parent::__construct(RuleActionType::LIMIT_THUMBNAIL);
	}
}
