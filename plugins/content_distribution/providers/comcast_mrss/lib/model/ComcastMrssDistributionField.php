<?php
/**
 * @package plugins.comcastMrssDistribution
 * @subpackage model.enum
 */ 
interface ComcastMrssDistributionField extends BaseEnum
{
	const START_TIME					= 'START_TIME';
	const END_TIME						= 'END_TIME';
	const GUID_ID						= 'GUID_ID';
	const TITLE							= 'TITLE';
	const DESCRIPTION					= 'DESCRIPTION';
	const LINK							= 'LINK';
	const PUB_DATE						= 'PUB_DATE';
	const LAST_BUILD_DATE				= 'LAST_BUILD_DATE';
	const MEDIA_RATING					= 'MEDIA_RATING';
	const MEDIA_KEYWORDS				= 'MEDIA_KEYWORDS';
	const MEDIA_CATEGORY				= 'MEDIA_CATEGORY';
	const COMCAST_TV_SERIES				= 'COMCAST_TV_SERIES';
	const COMCAST_LINK					= 'COMCAST_LINK';
	const COMCAST_BRAND					= 'COMCAST_BRAND';
	const COMCAST_VIDEO_CONTENT_TYPE	= 'COMCAST_VIDEO_CONTENT_TYPE';
}