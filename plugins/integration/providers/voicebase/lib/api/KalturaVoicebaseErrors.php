<?php
/**
 * @package plugins.voicebase
 * @subpackage api.errors
 */
class KalturaVoicebaseErrors
{
	const INVALID_TYPES = "INVALID_TYPES;TYPES;Invalid format types- \"@TYPES@\"";
	const LANGUAGE_NOT_SUPPORTED = "LANGUAGE_NOT_SUPPORTED;LANGUAGE;\"@LANGUAGE@\" isn't supported in external service provider";
	const NO_FLAVOR_ASSET_FOUND = "NO_FLAVOR_ASSET_FOUND;ENTRYID;no source flavor for entry id \"@ENTRYID@\"";
}
