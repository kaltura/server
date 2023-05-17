<?php

/**
 * @package plugins.scheduledTask
 * @subpackage model.enum
 */
interface ObjectFilterEngineType extends BaseEnum
{
	const ENTRY = 1;
	const ENTRY_VENDOR_TASK = 2;
	const RECYCLE_BIN_CLEANUP = 3;
}
