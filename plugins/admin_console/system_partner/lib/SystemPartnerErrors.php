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

	const PARTNER_AUDIO_THUMB_ENTRY_ID_ERROR = "PARTNER_AUDIO_THUMB_ENTRY_ID_ERROR;;partner_audio_thumb_entry_id_error";

	const PARTNER_LIVE_THUMB_ENTRY_ID_ERROR = "PARTNER_LIVE_THUMB_ENTRY_ID_ERROR;;partner_live_thumb_entry_id_error";

}