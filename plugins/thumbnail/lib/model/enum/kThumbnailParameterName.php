<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.enum
 */
interface kThumbnailParameterName extends BaseEnum
{
	const WIDTH = "width";
	const HEIGHT = "height";
	const BACKGROUND_COLOR = "background_color";
	const GRAVITY_POINT = "gravity_point";
	const BEST_FIT = "best_fit";
	const FILTER_TYPE = "filter_type";
	const BLUR = "blur";
	const X = "x";
	const Y = "y";
	const COMPOSITE_TYPE = "compositeType";
	const CHANNEL = "channel";
	const COMPOSITE_OBJECT = "compositeObject";
	const OPACITY = "opacity";
}
