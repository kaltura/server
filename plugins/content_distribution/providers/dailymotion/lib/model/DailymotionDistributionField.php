<?php
/**
 * @package plugins.dailymotionDistribution
 * @subpackage model.enum
 */ 
interface DailymotionDistributionField extends BaseEnum
{
    const VIDEO_TITLE = 'VIDEO_TITLE';
    const VIDEO_DESCRIPTION = 'VIDEO_DESCRIPTION';
    const VIDEO_TAGS = 'VIDEO_TAGS';
    const VIDEO_CHANNEL = 'VIDEO_CHANNEL';    
    const VIDEO_LANGUAGE = 'VIDEO_LANGUAGE';
    const VIDEO_TYPE = 'VIDEO_TYPE';
	const VIDEO_GEO_BLOCKING_OPERATION = 'VIDEO_GEO_BLOCKING_OPERATION';
	const VIDEO_GEO_BLOCKING_COUNTRY_LIST = 'VIDEO_GEO_BLOCKING_COUNTRY_LIST';
}