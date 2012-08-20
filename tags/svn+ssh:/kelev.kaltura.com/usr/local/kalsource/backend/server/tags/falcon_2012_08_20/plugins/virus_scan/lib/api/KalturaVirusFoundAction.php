<?php
/**
 * @package plugins.virusScan
 * @subpackage api.objects
 */
class KalturaVirusFoundAction extends KalturaEnum
{
	/**
	 * no action is taken
	 */
	const NONE = 0; 
	
	/**
	 * infected file is deleted (physically deleted and not just marked as deleted)
	 */
	const DELETE  = 1;
	
	/**
	 * try to clean file and if can’t do nothing
	 */
	const CLEAN_NONE  = 2;
	
	/**
	 * try to clean the file and if can’t delete it (physically deleted and not just marked as deleted)
	 */
	const CLEAN_DELETE  = 3;
}