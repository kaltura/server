<?php
/**
 * @package plugins.dropFolder
 * @subpackage model.enum
 */ 
interface DropFolderFileHandlerType extends BaseEnum
{
	const CONTENT = 1;
	const XML     = 2;
	const CSV     = 3;
}