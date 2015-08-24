<?php
/**
 * @package plugins.facebookDistribution
 * @subpackage model.enum
 */ 
interface FacebookDistributionField extends BaseEnum
{
    const TITLE  = 'title';
	const DESCRIPTION  = 'description';
	const SCHEDULE_PUBLISHING_TIME  = 'scheduled_publishing_time';
    const CALL_TO_ACTION_TYPE  = 'call_to_action_type';
    const CALL_TO_ACTION_VALUE  = 'call_to_action_value';  
    const PLACE  = 'place';
	const TAGS  = 'tags';	  
	const TARGETING  = 'targeting';
	const FEED_TARGETING  = 'feed_targeting';
}