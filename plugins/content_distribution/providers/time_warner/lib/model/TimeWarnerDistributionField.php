<?php
/**
 * @package plugins.timeWarnerDistribution
 * @subpackage model.enum
 */ 
interface TimeWarnerDistributionField extends BaseEnum
{
	const GUID							= 'GUID';
	const TITLE							= 'TITLE';
	const DESCRIPTION					= 'DESCRIPTION';
	const AUTHOR						= 'AUTHOR';
	const PUB_DATE						= 'PUB_DATE';
	const START_TIME					= 'START_TIME';
	const END_TIME						= 'END_TIME';
	const MEDIA_COPYRIGHT				= 'MEDIA_COPYRIGHT';
	const MEDIA_KEYWORDS				= 'MEDIA_KEYWORDS';
	const MEDIA_RATING					= 'MEDIA_RATING';
	const MEDIA_CATEGORY_CT				= 'MEDIA_CATEGORY_CT';
	const MEDIA_CATEGORY_TX				= 'MEDIA_CATEGORY_TX';
	const MEDIA_CATEGORY_GE				= 'MEDIA_CATEGORY_GE';
	const MEDIA_CATEGORY_GR				= 'MEDIA_CATEGORY_GR';
	const PLMEDIA_APPROVED				= 'PLMEDIA_APPROVED';
	const CABLE_EPISODE_NUMBER			= 'CABLE_EPISODE_NUMBER';
	const CABLE_EXTERNAL_ID				= 'CABLE_EXTERNAL_ID';
	const CABLE_PRODUCTION_DATE			= 'CABLE_PRODUCTION_DATE';
	const CABLE_NETWORK					= 'CABLE_NETWORK';
	const CABLE_PROVIDER				= 'CABLE_PROVIDER';
	const CABLE_SHORT_DESCRIPTION		= 'CABLE_SHORT_DESCRIPTION';
	const CABLE_SHORT_TITLE				= 'CABLE_SHORT_TITLE';
	const CABLE_SHOW_NAME				= 'CABLE_SHOW_NAME';
}