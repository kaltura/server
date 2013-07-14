<?php
/**
 * @package plugins.dropFolder
 * @subpackage model.enum
 */ 
interface DropFolderErrorCode extends BaseEnum
{
	const ERROR_CONNECT = 1;
	const ERROR_AUTENTICATE = 2;
	const ERROR_GET_PHISICAL_FILE_LIST = 3;
	const ERROR_GET_DB_FILE_LIST = 4;
	const DROP_FOLDER_APP_ERROR = 5;
	const CONTENT_MATCH_POLICY_UNDEFINED = 6;
}