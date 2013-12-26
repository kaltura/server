<?php
/**
 * @package plugins.playReady
 * @subpackage model.enum
 */ 
interface PlayReadyLicenseRemovalPolicy extends BaseEnum
{
	const FIXED_FROM_EXPIRATION = 1;
	const ENTRY_SCHEDULING_END = 2;
	const NONE = 3;
}