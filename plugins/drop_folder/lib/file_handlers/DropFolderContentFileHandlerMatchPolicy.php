<?php
/**
 * @package plugin.dropFolder
 * @subpackage model.enum
 */ 
interface DropFolderContentFileHandlerMatchPolicy extends BaseEnum
{
	const ADD_AS_NEW = 1;
	const MATCH_EXISTING_OR_ADD_AS_NEW = 2;
	const MATCH_EXISTING_OR_KEEP_IN_FOLDER = 3;
}