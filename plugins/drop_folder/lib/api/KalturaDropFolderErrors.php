<?php

/**
 * @package plugins.dropFolder
 * @subpackage api.errors
 */
class KalturaDropFolderErrors extends KalturaErrors
{
	
	const DROP_FOLDER_FILE_ALREADY_EXISTS = "DROP_FOLDER_FILE_ALREADY_EXISTS,Drop folder id [%s] already has a file with name [%s]";
	
	const DROP_FOLDER_NOT_FOUND = "DROP_FOLDER_NOT_FOUND,Drop folder id [%s] not found";
	
	const DROP_FOLDER_PARTNER_ID_NO_MATCH = "DROP_FOLDER_PARTNER_ID_NO_MATCH,Drop folder [%s] partner id does not match drop folder file partner id [%s]";
	
	const DROP_FOLDER_ALREADY_EXISTS = "DROP_FOLDER_ALREADY_EXISTS,Drop folder with path [%s] already exists on data center [%s]";
}