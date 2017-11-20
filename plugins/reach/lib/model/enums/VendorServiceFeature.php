<?php
/**
 * @package plugins.reach
 * @subpackage model.enum
 */ 
interface VendorServiceFeature extends BaseEnum
{
	const CAPTIONS 		= 1;
	const TRANSLATION 	= 2;
	const OCR 			= 3;
	const ASR			= 4;
}