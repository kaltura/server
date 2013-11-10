<?php
/**
 * @package plugins.cuePoint
 * @subpackage api.errors
 */
class KalturaCuePointErrors extends KalturaErrors implements kQuePointErrors
{
	const INVALID_CUE_POINT_ID = "INVALID_ID;ID;Invalid cue point id [@ID@]";
	
	const CUE_POINT_SYSTEM_NAME_EXISTS = "CUE_POINT_SYSTEM_NAME_EXISTS;NAME,ID;Cue point system name already exists [@NAME@] with id [@ID@]";
}