<?php
/**
 * @package plugins.reach
 * @subpackage model.enum
 */ 
interface VendorProfileContentDeletionPolicy extends BaseEnum
{
	const DO_NOTHING						= 1;
	const DELETE_ONCE_PROCESSED				= 2;
	const DELETE_ONCE_DELETED_FROM_KALTURA 	= 3;
}