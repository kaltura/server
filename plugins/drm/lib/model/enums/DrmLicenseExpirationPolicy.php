<?php
/**
 * @package plugins.drm
 * @subpackage model.enum
 */ 
interface DrmLicenseExpirationPolicy extends BaseEnum
{
	const FIXED_DURATION = 1;
	const ENTRY_SCHEDULING_END = 2;
	const UNLIMITED = 3;
}