<?php
/**
 * @package plugins.playReady
 * @subpackage api.errors
 */
class KalturaPlayReadyErrors
{
	const ENTRY_NOT_FOUND_BY_KEY_ID = "ENTRY_NOT_FOUND_BY_KEY_ID,Entry not found by PlayReady key id \"%s\"";
	const PLAYREADY_POLICY_NOT_FOUND = "PLAYREADY_POLICY_NOT_FOUND, PlayReady policy not found for entry \"%s\"";
	const PLAYREADY_PROFILE_NOT_FOUND = "PLAYREADY_PROFILE_NOT_FOUND, PlayReady profile configuration not found";
	const PLAYREADY_POLICY_OBJECT_NOT_FOUND = "PLAYREADY_POLICY_OBJECT_NOT_FOUND, PlayReady policy object with id \"%s\" not found";
	const KEY_ID_DONT_MATCH = "KEY_ID_DONT_MATCH, Input key ID \"%s\" doesn't match entry key ID \"%s\"";
	const ANALOG_OUTPUT_PROTECTION_ID_NOT_ALLOWED = "ANALOG_OUTPUT_PROTECTION_ID_NOT_ALLOWED, Analog video output protection ID \"%s\" maybe specified only if ID \"%s\" is not specified";
	const COPY_ENABLER_TYPE_MISSING = "COPY_ENABLER_TYPE_MISSING, At least one copy enabler type should be specified";
	const FAILED_TO_GET_ENTRY_KEY_ID = "FAILED_TO_GET_ENTRY_KEY_ID,Failed to get PlayReady key id for entry \"%s\"";
}
