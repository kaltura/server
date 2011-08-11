<?php
/**
 * @package plugins.dropFolder
 * @subpackage model.enum
 */ 
interface DropFolderStatus extends BaseEnum
{
	const DISABLED = 0;
	const ENABLED  = 1;
	const DELETED  = 2;
}