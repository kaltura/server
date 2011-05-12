<?php
/**
 * @package plugins.dropFolder
 * @subpackage model.enum
 */ 
interface DropFolderFileErrorCode extends BaseEnum
{
	const ERROR_UPDATE_ENTRY = 1;
	const ERROR_ADD_ENTRY = 2;
	const FLAVOR_NOT_FOUND = 3;
	const FLAVOR_MISSING_IN_FILE_NAME = 4;
	const SLUG_REGEX_NO_MATCH = 5;
	const ERROR_READING_FILE = 6;
}

