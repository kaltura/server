<?php
/**
 * @package plugins.storageProfile
 * @subpackage api.enum
 */
class KalturaStorageServePriority extends KalturaEnum
{	  				
	const KALTURA_ONLY = 1;
	const KALTURA_FIRST = 2;
	const EXTERNAL_FIRST = 3;
	const EXTERNAL_ONLY = 4;
}
