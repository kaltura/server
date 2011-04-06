<?php
/**
 * @package plugins.dropFolder
 * @subpackage model.enum
 */ 
interface DropFolderStatus extends BaseEnum
{
	const DISABLED  = 0;
	const AUTOMATIC = 1;
	const MANUAL    = 2;
	const DELETED   = 3;
}