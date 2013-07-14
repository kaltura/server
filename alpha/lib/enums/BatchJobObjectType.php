<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface BatchJobObjectType extends BaseEnum
{
	const ENTRY 		= 1;
	const CATEGORY		= 2;
	const FILE_SYNC		= 3;
	const ASSET 		= 4;
}
