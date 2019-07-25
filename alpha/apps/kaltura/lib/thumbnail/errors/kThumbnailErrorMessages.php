<?php
/**
 * @package core
 * @subpackage thumbnail.errors
 */

class kThumbnailErrorMessages
{
	const ERROR_STRING = 'errorString';
	const ACTION_STRING = 'actionString';
	const SOURCE_STRING = 'sourceString';
	const OPACITY = 'Opacity must be between 1-100';
	const MISSING_COMPOSITE = 'Missing composite object';
	const COMPOSE_FAILED = 'Failed to compose image';
	const MISSING_CORP_X_Y = 'You cant define only crop x or crop y';
	const CROP_WIDTH = 'Crop width must be smaller or equal to the current width';
	const CROP_HEIGHT = 'Crop height must be smaller or equal to the current height';
	const WIDTH_POSITIVE = 'Width must be positive';
	const HEIGHT_POSITIVE = 'Height must be positive';
	const ILLEGAL_GRAVITY = 'Illegal gravity point value';
	const BEST_FIT_WIDTH = 'If bestFit is supplied parameter width must be positive';
	const BEST_FIT_HEIGHT = 'If bestFit is supplied parameter height must be positive';
	const WIDTH_DIMENSIONS = 'Width must be between 0 and 10000';
	const HEIGHT_DIMENSIONS = 'Height must be between 0 and 10000';
	const DEGREES = 'Degrees must be between 0 and 360, exclusive';
	const Y_ROUNDING = 'y rounding parameter must be positive';
	const X_ROUNDING = 'x rounding parameter must be positive';
	const MISSING_TEXT = 'You must supply a text for this action';
	const ENTRY_SOURCE_ONLY = ' can only work on entry source';
	const FAILED = ' failed';
	const SECOND =  'Second cant be negative';
	const VID_SLICE_FAILED = 'Vid slice failed';
	const SLICE_NUMBER = 'Slice number must be positive and can not be greater then number of slices';
	const NUMBER_OF_SLICE = 'Number of slices must be positive';
	const START_SEC = 'Start sec must be positive';
	const END_SEC = 'End sec cant be greater then the video length';
	const END_SEC_START_SEC = 'End sec must be greater then start sec';
	const VID_STRIP_FAILED = 'Vid strip failed';
	const ENTRY_TYPE = 'entryType';
	const TEXT_DOES_NOT_FIT_ERR = 'text does not fit the bounding box';
	const ILLEGAL_ENUM_VALUE = 'Illegal enum value';
	const NOT_VALID_IMAGE_FORMAT = 'Not valid image format';
	const DENSITY_POSITIVE = 'Density must be positive';
	const QUALITY_NOT_IN_RANGE = 'Quality must be between 20 and 100';
}