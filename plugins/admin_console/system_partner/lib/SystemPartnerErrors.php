<?php
/**
 * @package plugins.systemPartner
 * @subpackage errors
 */
class SystemPartnerErrors extends KalturaErrors
{
	/**
	 * System Partner Service
	 */

	const UNABLE_TO_FORM_GROUP_ASSOCIATION = "UNABLE_TO_FORM_GROUP_ASSOCIATION;PID,GROUP_TYPE;Partner @PID@ groupType @GROUP_TYPE@ does not allow group associations.";

	const PARTNER_AUDIO_THUMB_ENTRY_ID_ERROR = "PARTNER_AUDIO_THUMB_ENTRY_ID_ERROR;ID;Wrong entry id - @ID@ -  for audio thumbnails";

	const PARTNER_LIVE_THUMB_ENTRY_ID_ERROR = "PARTNER_LIVE_THUMB_ENTRY_ID_ERROR;ID;Wrong entry id - @ID@ -  for live thumbnails";

	const DOMAINS_NOT_ALLOWED = "DOMAINS_NOT_ALLOWED;DOMAINS;Some domains are not allowing Kaltura: @DOMAINS@ ";

    const PARTNER_RECORDING_CONVERSION_PROFILE_ID_ERROR = "PARTNER_RECORDING_CONVERSION_PROFILE_ID_ERROR;ID;Wrong conversion profile id - @ID@ -  for recording entries";

	/**
	 * codes
	 */

	const DOMAINS_NOT_ALLOWED_CODE = 'DOMAINS_NOT_ALLOWED';
}