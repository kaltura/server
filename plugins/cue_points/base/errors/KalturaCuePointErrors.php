<?php
/**
 * @package plugins.cuePoint
 * @subpackage api.errors
 */
class KalturaCuePointErrors extends KalturaErrors implements kCuePointErrors
{
	const INVALID_CUE_POINT_ID = "INVALID_CUE_POINT_ID;ID;Invalid cue point id [@ID@]";
	
	const CUE_POINT_NOT_FOUND = "CUE_POINT_NOT_FOUND;ID;Cue point with id provided not found [@ID@]";
	
	const CUE_POINT_PROVIDED_NOT_OF_TYPE_THUMB_CUE_POINT = "CUE_POINT_PROVIDED_NOT_OF_TYPE_THUMB_CUE_POINT;ID;Cue point provided not of type thumb cue point [@ID@]";
	
	const CUE_POINT_ALREADY_ASSOCIATED_WITH_ASSET = "CUE_POINT_PROVIDED_ALREADY_ASSOCIATED_WITH_ASSET;ID;Cue point provided already associated with other asset [@ID@]";
	
	const CUE_POINT_SYSTEM_NAME_EXISTS = "CUE_POINT_SYSTEM_NAME_EXISTS;NAME,ID;Cue point system name already exists [@NAME@] with id [@ID@]";

	const PARENT_CUE_POINT_NOT_FOUND = "PARENT_CUE_POINT_NOT_FOUND;ID;Parent cue point id [@ID@] not found";

	const PARENT_CUE_POINT_DO_NOT_BELONG_TO_THE_SAME_ENTRY = "PARENT_CUE_POINT_DO_NOT_BELONG_TO_THE_SAME_ENTRY;;Parent cue point does not belong to the same entry";
	
	const USER_KS_CANNOT_LIST_RELATED_CUE_POINTS = "USER_KS_CANNOT_LIST_RELATED_CUE_POINTS;FILTER;Cue-points should not be listed [@FILTER@] as related-objects with unprivileged user ks";

	const NO_PERMISSION_ON_ENTRY = "NO_PERMISSION_ON_ENTRY;ID;No permissions to insert or update cue points for entry [@ID@]";
}
