<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface DeleteObjectType extends BaseEnum
{
	const CATEGORY_ENTRY = 1;
	const CATEGORY_USER = 2;
	const GROUP_USER = 3;
	const CATEGORY_ENTRY_AGGREGATION = 4;
	const USER_ENTRY = 5;
}