<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface BulkUploadObjectType extends BaseEnum
{
	const ENTRY = 1;
	
	const CATEGORY = 2;
	
	const USER = 3;
	
	const CATEGORY_USER = 4;
	
	const CATEGORY_ENTRY = 5;

	const USER_ENTRY = 6;

	const VENDOR_CATALOG_ITEM = 7;
}
