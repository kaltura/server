<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface CategoryStatus extends BaseEnum
{
	const UPDATING = 1;
	const ACTIVE = 2;
	const DELETED = 3;
	const PURGED = 4;
}
