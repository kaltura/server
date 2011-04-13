<?php
/**
 * @package plugin.dropFolder
 * @subpackage model.enum
 */ 
interface DropFolderUnmatchedContentFilePolicy extends BaseEnum
{
	const ADD_AS_ENTRY  = 1;
	const KEEP_IN_FOLDER = 2;	
}