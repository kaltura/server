<?php
/**
 * @package plugins.dropFolder
 * @subpackage model.enum
 */ 
interface DropFolderFileDeletePolicy extends BaseEnum
{
	const MANUAL_DELETE = 1;
	const AUTO_DELETE   = 2;	
}