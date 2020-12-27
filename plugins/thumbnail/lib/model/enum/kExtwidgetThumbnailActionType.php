<?php
/**
 * @package core
 * @subpackage thumbnail.enum
 */

interface kExtwidgetThumbnailActionType extends BaseEnum
{
	const RESIZE = 1;
	const RESIZE_WITH_PADDING = 2;
	const CROP = 3;
	const CROP_FROM_TOP = 4;
	const RESIZE_WITH_FORCE = 5;
	const CROP_AFTER_RESIZE = 6;
}