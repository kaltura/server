<?php
/**
 * @package core
 * @subpackage thumbnail.enum
 */

interface kFilterType extends BaseEnum
{
	const BLUE_SHIFT = 'blueshift';
	const CHARCOAL = 'charcoal';
	const CONTRAST = 'contrast';
	const EDGE = 'edge';
	const OIL = 'oil';
	const POLAROID  = "polaroid";
	const RAISE = 'raise';
	const SEPIA = 'sepia';
	const SHADE = 'shade';
	const SOLARIZE = 'solarize';
	const VIGNETTE = 'vignette';
	const WAVE = 'wave';
}