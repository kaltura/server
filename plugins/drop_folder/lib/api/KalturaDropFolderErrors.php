<?php

/**
 * @package plugins.dropFolder
 * @subpackage api.errors
 */
class KalturaDropFolderErrors extends KalturaErrors
{
	
	const DROP_FOLDER_FILE_ALREADY_EXISTS = "DROP_FOLDER_FILE_ALREADY_EXISTS;FOLDER_ID,FILE_NAME;Drop folder id [@FOLDER_ID@] already has a file with name [@FILE_NAME@]";
	
	const DROP_FOLDER_NOT_FOUND = "DROP_FOLDER_NOT_FOUND;FOLDER_ID;Drop folder id [@FOLDER_ID@] not found";
	
	const DROP_FOLDER_ALREADY_EXISTS = "DROP_FOLDER_ALREADY_EXISTS;PATH;Drop folder with path [@PATH@] already exists";
}