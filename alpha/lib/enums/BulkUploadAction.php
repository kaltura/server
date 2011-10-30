<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface BulkUploadAction extends BaseEnum
{
	const ADD = 1;
	const UPDATE = 2;
	const DELETE = 3;
	const REPLACE = 4;
}
