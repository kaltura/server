<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface DropFolderFileDeletePolicy extends BaseEnum
{
	const NO_DELETE     = 0;
	const MANUAL_DELETE = 1;
	const AUTO_DELETE   = 2;	
}