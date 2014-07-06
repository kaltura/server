<?php
/**
 * @package plugins.dropFolder
 * @subpackage model.enum
 */ 
interface DropFolderFileDeletePolicy extends BaseEnum
{
	const MANUAL_DELETE = 1;
	const AUTO_DELETE   = 2; // Auto delete when import is done
	const AUTO_DELETE_WHEN_ENTRY_IS_READY = 3; // Auto delete when the entry is ready (i.e. conversion is done)
}