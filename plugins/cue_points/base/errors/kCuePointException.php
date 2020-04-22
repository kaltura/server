<?php
/**
 * @package plugins.cuePoint
 * @subpackage api.errors
 */
class kCuePointException extends kCoreException implements kCuePointErrors
{
	const COPY_CUE_POINT_TO_ENTRY_NOT_PERMITTED = "COPY_CUE_POINT_TO_ENTRY_NOT_PERMITTED";
}