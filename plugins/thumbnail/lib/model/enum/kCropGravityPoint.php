<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.enum
 */
interface kCropGravityPoint extends BaseEnum
{
	const NORTHWEST = Imagick::GRAVITY_NORTHWEST;
	const NORTH = Imagick::GRAVITY_NORTH;
	const NORTHEAST = Imagick::GRAVITY_NORTHEAST;
	const WEST = Imagick::GRAVITY_WEST;
	const CENTER = Imagick::GRAVITY_CENTER;
	const EAST = Imagick::GRAVITY_EAST;
	const SOUTHWEST = Imagick::GRAVITY_SOUTHWEST;
	const SOUTH = Imagick::GRAVITY_SOUTH;
	const SOUTHEAST = Imagick::GRAVITY_SOUTHEAST;
}
