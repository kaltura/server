<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.enum
 */
interface kThumbFactoryFieldName extends BaseEnum
{
	const ENTRY = 'entry';
	const SOURCE_ENTRY = 'sourceEntry';
	const VERSION = 'version';
	const WIDTH = 'width';
	const HEIGHT = 'height';
	const TYPE = 'type';
	const BG_COLOR = 'bgColor';
	const CROP_WIDTH = 'cropWidth';
	const CROP_HEIGHT = 'cropHeight';
	const CROP_X = 'cropX';
	const CROP_Y = 'cropY';
	const VID_SEC = 'vidSec';
	const VID_SLICE = 'vidSlice';
	const VID_SLICES = 'vidSlices';
	const DENSITY = 'density';
	const START_SEC = 'startSec';
	const END_SEC = 'endSec';
	const STRIP_PROFILES = 'stripProfiles';
	const QUALITY = 'quality';
	const IMAGE_FORMAT = 'imageFormat';
	const SRC_WIDTH = 'srcWidth';
	const SRC_HEIGHT = 'srcHeight';
	const ORIG_IMAGE_PATH = 'origImagePath';
	const FILE_SYNC = 'fileSync';
}