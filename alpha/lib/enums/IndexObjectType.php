<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface IndexObjectType extends BaseEnum
{
	const LOCK_CATEGORY = 1;
	const CATEGORY = 2;
	const CATEGORY_ENTRY = 3;
	const ENTRY = 4;
	const CATEGORY_USER = 5;
	const USER = 6;
}
