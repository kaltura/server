<?php
/**
 * @package plugins.drm
 * @subpackage model.enum
 */ 
interface DrmDeviceStatus extends BaseEnum
{
	const PENDING = 1;
	const ACTIVE = 2;
	const INACTIVE = 3;
	const DELETED = 4;
}