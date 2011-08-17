<?php
/**
 * @package plugins.cuePoint
 * @subpackage api.errors
 */
class KalturaCuePointErrors extends KalturaErrors implements kQuePointErrors
{
	const INVALID_CUE_POINT_ID = "INVALID_CUE_POINT_ID,Invalid cue point id [%s]";
	
	const CUE_POINT_SYSTEM_NAME_EXISTS = "CUE_POINT_SYSTEM_NAME_EXISTS,Cue point system name already exists [%s] with id [%s]";
}