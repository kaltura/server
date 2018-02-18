<?php
/**
 * @package plugins.reach
 * @subpackage model.enum
 */ 
interface VendorProfileStatus extends BaseEnum
{
	const DISABLED	= 1;
	const ACTIVE	= 2;
	const DELETED	= 3;
}