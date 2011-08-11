<?php
/**
 * @package plugins.youTubeDistribution
 * @subpackage model.enum
 */ 
interface YouTubeDistributionField extends BaseEnum
{
	// item
	const NOTIFICATION_EMAIL = 'NOTIFICATION_EMAIL';
	const ACCOUNT_USERNAME  = 'ACCOUNT_USERNAME';
	const OWNER_NAME  = 'OWNER_NAME';
	const TARGET  = 'TARGET';
	const DATE_RECORDED  = 'DATE_RECORDED';
	const LANGUAGE  = 'LANGUAGE';
	const START_TIME  = 'START_TIME';
	const END_TIME  = 'END_TIME';
	
	// item/media:content
	const MEDIA_TITLE  = 'MEDIA_TITLE';
	//const MEDIA_CONTENT_URL  = 'MEDIA_CONTENT_URL';
	const MEDIA_DESCRIPTION  = 'MEDIA_DESCRIPTION';
	const MEDIA_KEYWORDS  = 'MEDIA_KEYWORDS';
	const MEDIA_CATEGORY  = 'MEDIA_CATEGORY';
	const MEDIA_RATING  = 'MEDIA_RATING';
	
	// item/yt:community
	const ALLOW_COMMENTS  = 'ALLOW_COMMENTS';
	const ALLOW_RESPONSES  = 'ALLOW_RESPONSES';
	const ALLOW_RATINGS  = 'ALLOW_RATINGS';
	const ALLOW_EMBEDDING  = 'ALLOW_EMBEDDING';
	
	// item/yt:policy
	const POLICY_COMMERCIAL  = 'POLICY_COMMERCIAL';
	const POLICY_UGC = 'POLICY_UGC';

	// item/yt:web_metadata
	const WEB_METADATA_CUSTOM_ID  = 'WEB_METADATA_CUSTOM_ID';
	
	// item/yt:tv_metadata
	const TV_METADATA_CUSTOM_ID  = 'TV_METADATA_CUSTOM_ID';
	const TV_METADATA_EPISODE  = 'TV_METADATA_EPISODE';
	const TV_METADATA_EPISODE_TITLE  = 'TV_METADATA_EPISODE_TITLE';
	const TV_METADATA_SHOW_TITLE  = 'TV_METADATA_SHOW_TITLE';
	const TV_METADATA_SEASON  = 'TV_METADATA_SEASON';
	
	// item/yt:playlists
	const PLAYLISTS = 'PLAYLISTS';
	
	// item/yt:advertising/yt:third_party_ads
	const THIRD_PARTY_AD_SERVER_AD_TYPE = 'THIRD_PARTY_AD_SERVER_AD_TYPE';
	const THIRD_PARTY_AD_SERVER_PARTNER_ID = 'THIRD_PARTY_AD_SERVER_PARTNER_ID';
	const THIRD_PARTY_AD_SERVER_VIDEO_ID = 'THIRD_PARTY_AD_SERVER_VIDEO_ID';
	
}