<?php
/**
 * @package plugins.cuePoint
 * @subpackage model.enum
 */
class CuePointPermissionName implements PermissionName
{
	const REMOVE_CUE_POINTS_WHEN_REPLACING_MEDIA = 'REMOVE_CUE_POINTS_WHEN_REPLACING_MEDIA';
	const DO_NOT_COPY_CUE_POINTS_TO_CLIP = 'DO_NOT_COPY_CUE_POINTS_TO_CLIP';
	const DO_NOT_COPY_CUE_POINTS_TO_TRIMMED_ENTRY = 'DO_NOT_COPY_CUE_POINTS_TO_TRIMMED_ENTRY';
}
