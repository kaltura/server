<?php
/**
 * @package plugins.playReady
 * @subpackage api.errors
 */
class KalturaPlayReadyErrors
{
	const ENTRY_NOT_FOUND_BY_KEY_ID = "ENTRY_NOT_FOUND_BY_KEY_ID;KEY_ID;Entry not found by PlayReady key id [@KEY_ID@].";
	const PLAYREADY_POLICY_NOT_FOUND = "PLAYREADY_POLICY_NOT_FOUND;ENTRY_ID;PlayReady policy not found for entry [@ENTRY_ID@].";
	const PLAYREADY_PROFILE_NOT_FOUND = "PLAYREADY_PROFILE_NOT_FOUND;;PlayReady profile configuration not found";
	const PLAYREADY_POLICY_OBJECT_NOT_FOUND = "PLAYREADY_POLICY_OBJECT_NOT_FOUND;POLICY_ID;PlayReady policy object with id [@POLICY_ID@] not found.";
	const KEY_ID_DONT_MATCH = "KEY_ID_DONT_MATCH;KEY_ID,ENTRY_KEY_ID;Input key ID [@KEY_ID@] doesn't match entry key ID [@ENTRY_KEY_ID@]";
	const ANALOG_OUTPUT_PROTECTION_ID_NOT_ALLOWED = "ANALOG_OUTPUT_PROTECTION_ID_NOT_ALLOWED;ID_1,ID_2 Analog video output protection ID [@ID_1@] maybe specified only if ID [@ID_2@] is not specified.";
	const COPY_ENABLER_TYPE_MISSING = "COPY_ENABLER_TYPE_MISSING;;At least one copy enabler type should be specified";
	const FAILED_TO_GET_ENTRY_KEY_ID = "FAILED_TO_GET_ENTRY_KEY_ID;ENTRY_ID;Failed to get PlayReady key id for entry [@ENTRY_ID@].";
}
