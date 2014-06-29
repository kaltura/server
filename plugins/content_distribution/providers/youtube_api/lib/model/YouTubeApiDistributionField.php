<?php
/**
 * @package plugins.youTubeDistribution
 * @subpackage model.enum
 */ 
interface YouTubeApiDistributionField extends BaseEnum
{
    const MEDIA_TITLE  = 'MEDIA_TITLE';
	const MEDIA_DESCRIPTION  = 'MEDIA_DESCRIPTION';
	const MEDIA_KEYWORDS  = 'MEDIA_KEYWORDS';
    const MEDIA_CATEGORY  = 'MEDIA_CATEGORY';
    const MEDIA_PLAYLIST_IDS  = 'MEDIA_PLAYLIST_IDS';
    
    const START_DATE  = 'START_DATE';
	const END_DATE  = 'END_DATE';	
   
	const ALLOW_COMMENTS  = 'ALLOW_COMMENTS';
	const ALLOW_RESPONSES  = 'ALLOW_RESPONSES';
	const ALLOW_RATINGS  = 'ALLOW_RATINGS';
	const ALLOW_EMBEDDING  = 'ALLOW_EMBEDDING';

    const ALT_RAW_FILENAME  = 'ALT_RAW_FILENAME'; // Alternative raw filename
}