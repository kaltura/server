<?php
/**
 * @package plugins.scheduledTask
 * @subpackage model.enum
 */ 
interface ObjectTaskType extends BaseEnum
{
	const DELETE_ENTRY = 1;

	const MODIFY_CATEGORIES = 2;

	const DELETE_ENTRY_FLAVORS = 3;

	const CONVERT_ENTRY_FLAVORS = 4;

	const DELETE_LOCAL_CONTENT = 5;

	const STORAGE_EXPORT = 6;

	const MODIFY_ENTRY = 7;

	const MAIL_NOTIFICATION = 8;
}
