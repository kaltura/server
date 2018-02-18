<?php
/**
 * @package plugins.doubleClickDistribution
 * @subpackage model.enum
 */ 
interface DoubleClickDistributionField extends BaseEnum
{
	const GUID						= 'GUID';
	const PUB_DATE					= 'PUB_DATE';
	const TITLE						= 'TITLE';
	const DESCRIPTION				= 'DESCRIPTION';
	const LINK						= 'LINK';
	const AUTHOR					= 'AUTHOR';
	const KEYWORDS					= 'KEYWORDS';
	const CATEGORIES				= 'CATEGORIES';
	const MONETIZABLE				= 'MONETIZABLE';
	const TOTAL_VIEW_COUNT 			= 'TOTAL_VIEW_COUNT';
	const PREVIOUS_DAY_VIEW_COUNT 	= 'PREVIOUS_DAY_VIEW_COUNT';
	const PREVIOUS_WEEK_VIEW_COUNT 	= 'PREVIOUS_WEEK_VIEW_COUNT';
	const FAVORITE_COUNT 			= 'FAVORITE_COUNT';
	const LIKE_COUNT				= 'LIKE_COUNT';
	const DISLIKE_COUNT				= 'DISLIKE_COUNT';
	const DFP_METADATA 				= 'DFP_METADATA';
	const LAST_MODIFIED_DATE		= 'LAST_MODIFIED_DATE';
	const LAST_MEDIA_MODIFIED_DATE	= 'LAST_MEDIA_MODIFIED_DATE';
	const STATUS					= 'STATUS';
	const FW_CAID					= 'FW_CAID';
}