<?php

/**
 * @package plugins.dropFolder
 * @subpackage api.errors
 */
class KalturaDropFolderErrors extends KalturaErrors
{
	
	const DROP_FOLDER_FILE_ALREADY_EXISTS = "DROP_FOLDER_FILE_ALREADY_EXISTS,Drop folder id [%s] already has a file with name [%s]";
	
	const DROP_FOLDER_NOT_FOUND = "DROP_FOLDER_NOT_FOUND,Drop folder id [%s] not found";
	
	const DROP_FOLDER_ALREADY_EXISTS = "DROP_FOLDER_ALREADY_EXISTS,Drop folder with path [%s] already exists";
}