<?php
/**
 * @package plugins.reach
 * @subpackage model.enum
 */ 
interface VendorProfileContentDeletionPolicy extends BaseEnum
{
	const DO_NOTHING						= 1;
	const DELETE_ONCE_PROCESSED				= 2;
	const DELETE_AFTER_WEEK					= 3;
	const DELETE_AFTER_MONTH				= 4;
	const DELETE_AFTER_THREE_MONTHS			= 5;
}