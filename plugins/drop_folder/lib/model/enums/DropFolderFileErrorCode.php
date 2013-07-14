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
	const ERROR_DOWNLOADING_FILE = 7;
	const ERROR_UPDATE_FILE = 8;
	const ERROR_ADDING_CONTENT_PROCESSOR = 10;
	const ERROR_IN_CONTENT_PROCESSOR = 11;
	const ERROR_DELETING_FILE = 12;
	const FILE_NO_MATCH = 13;
}

