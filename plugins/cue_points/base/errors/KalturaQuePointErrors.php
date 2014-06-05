<?php
/**
 * @package plugins.cuePoint
 * @subpackage api.errors
 */
class KalturaCuePointErrors extends KalturaErrors implements kQuePointErrors
{
	const INVALID_CUE_POINT_ID = "INVALID_ID;ID;Invalid cue point id [@ID@]";
	
	const CUE_POINT_ID_NOT_FOUND = "CUE_POINT_ID_NOT_FOUND;ID;cue point with id provided not found [@ID@]";
	
	const CUE_POINT_PROVIDED_NOT_OF_TYPE_THUMB_CUE_POINT = "CUE_POINT_PROVIDED_NOT_OF_TYPE_THUMB_CUE_POINT;ID;cue point provided not of type thumb cue point [@ID@]";
	
	const CUE_POINT_ALREADY_ASSOCIATED_WITH_ASSET = "CUE_POINT_PROVIDED_ALREADY_ASSOCIATED_WITH_ASSET;ID;cue point provided already associated with other asset [@ID@]";
	
	const CUE_POINT_SYSTEM_NAME_EXISTS = "CUE_POINT_SYSTEM_NAME_EXISTS;NAME,ID;Cue point system name already exists [@NAME@] with id [@ID@]";
}