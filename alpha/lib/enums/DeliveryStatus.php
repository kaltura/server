<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface DeliveryStatus extends BaseEnum
{
	// Active delivery profile
	const ACTIVE = 0;
	// Deleted delivery profile
	const DELETED = 1;
	// Indicated delivery profiles we consider to add
	const STAGING_IN = 2;
	// Indicated delivery profiles we consider to remove
	const STAGING_OUT = 3; 
}
