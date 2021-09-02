<?php
/**
 * @package plugins.reach
 * @subpackage model.enum
 */ 
interface VendorServiceFeature extends BaseEnum
{
	const CAPTIONS              = 1;
	const TRANSLATION           = 2;
	const ALIGNMENT             = 3;
	const AUDIO_DESCRIPTION     = 4;
	const CHAPTERING            = 5;
	const INTELLIGENT_TAGGING   = 6;
}