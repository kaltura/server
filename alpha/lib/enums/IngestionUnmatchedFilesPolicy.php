<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface IngestionUnmatchedFilesPolicy extends BaseEnum
{
	const ADD_AS_ENTRY  = 1;
	const KEEP_IN_FOLDER = 2;	
}