<?php
/**
 * @package plugins.captureSpace
 * @subpackage errors
 */
class CaptureSpaceErrors extends KalturaErrors
{
	const ALREADY_LATEST_VERSION = "ALREADY_LATEST_VERSION;VERSION,OS;Version [@VERSION@] is already the latest version for [@OS@]";
	
	const NO_UPDATE_IS_AVAILABLE = "NO_UPDATE_IS_AVAILABLE;VERSION,OS;No update is available for version [@VERSION@] for [@OS@]";
	
	const NO_INSTALL_IS_AVAILABLE = "NO_INSTALL_IS_AVAILABLE;OS;No installation file is available for operating system [@OS@]";
}