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
	const TRANSFORM_XSLT = 5;
	const ADD_OR_UPDATE = 6;
	const ACTIVATE = 7;
	const REJECT = 8;
	const UPDATE_STATUS = 9;
}
