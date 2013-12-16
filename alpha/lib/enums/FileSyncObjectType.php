<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface FileSyncObjectType extends BaseEnum
{
	const ENTRY = 1;
	const UICONF = 2;
	const BATCHJOB = 3;
	const ASSET = 4;
	const METADATA = 5;
	const METADATA_PROFILE = 6;
	const SYNDICATION_FEED = 7;
	const CONVERSION_PROFILE = 8;
	const FILE_ASSET = 9;
	
	/**
	 * @deprecated use ASSET instead
	 */
	const FLAVOR_ASSET = 4;
}
