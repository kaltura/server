<?php
/**
 * @package plugins.widevine
 * @subpackage api.errors
 */
class KalturaWidevineErrorCodes
{
	/*
	 * The following error codes are reserved by the mediaplayer in Android 3.x and higher:
		513 - 518
		543
		600
		601
		800 - 821
	 */
	const FLAVOR_ASSET_ID_CANNOT_BE_NULL = 520;
	
	const WIDEVINE_ASSET_ID_CANNOT_BE_NULL = 521;
	
	const FLAVOR_ASSET_ID_NOT_FOUND = 522;
	
	const FLAVOR_ASSET_ID_DONT_MATCH_WIDEVINE_ASSET_ID = 523;
	
	const ACCESS_CONTROL_RESTRICTED = 524;
	
	const ENTRY_NOT_SCHEDULED_NOW = 525;
	
	const ENTRY_MODERATION_ERROR = 526;
	
	const WRONG_ASSET_TYPE = 527;
	
	const MISSING_MANDATORY_SIGN_PARAMETER = 528;
	
	const LICENSE_SERVER_URL_NOT_SET = 529;
	
	const LICENSE_KEY_NOT_SET = 530;
	
	const GENERAL_ERROR = 768;
}