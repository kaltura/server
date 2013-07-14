<?php
/**
 * @package plugins.yahooDistribution
 * @subpackage model.enum
 */ 
interface YahooDistributionField extends BaseEnum
{
	//contact
	const CONTACT_TELEPHONE = 'CONTACT_TELEPHONE';
	const CONTACT_EMAIL = 'CONTACT_EMAIL';
		
	//video	
	const VIDEO_MODIFIED_DATE = 'VIDEO_MODIFIED_DATE';
	
	//feed item
	const VIDEO_FEEDITEM_ID  = 'VIDEO_FEEDITEM_ID';
	
	const VIDEO_TITLE  = 'VIDEO_TITLE';
	const VIDEO_DESCRIPTION  = 'VIDEO_DESCRIPTION';
	const VIDEO_ROUTING  = 'VIDEO_ROUTING';
	const VIDEO_KEYWORDS  = 'VIDEO_KEYWORDS';
	const VIDEO_VALID_TIME  = 'VIDEO_VALID_TIME';
	const VIDEO_EXPIRATION_TIME = 'VIDEO_EXPIRATION_TIME';	
	
	const VIDEO_LINK_TITLE  = 'VIDEO_LINK_TITLE';
	const VIDEO_LINK_URL  = 'VIDEO_LINK_URL';
	
	const VIDEO_DURATION  = 'VIDEO_DURATION';
	
}