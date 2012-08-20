<?php
/**
 * @package plugins.virusScan
 * @subpackage api.enum
 */
class KalturaVirusScanJobResult extends KalturaEnum
{
	const SCAN_ERROR        = 1;
	const FILE_IS_CLEAN     = 2;
	const FILE_WAS_CLEANED  = 3;
	const FILE_INFECTED     = 4;	
}