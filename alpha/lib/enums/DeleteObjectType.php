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
	const ENTRY = 6;
	const CATEGORY_USER_SUBSCRIBER = 7;
	const USER_GROUP_SUBSCRIPTION = 8;
}
