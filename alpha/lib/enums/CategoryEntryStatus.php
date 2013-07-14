<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface CategoryEntryStatus extends BaseEnum
{
	const PENDING = 1;
	const ACTIVE = 2;
	const DELETED = 3;
	const REJECTED = 4;
}
