<?php
/**
 * @package plugins.contentDistribution
 * @subpackage model.enum
 */
interface EntryDistributionDirtyStatus extends BaseEnum
{
	const NONE = 0;
	const SUBMIT_REQUIRED = 1;
	const DELETE_REQUIRED = 2;
	const UPDATE_REQUIRED = 3;
	const ENABLE_REQUIRED = 4;
	const DISABLE_REQUIRED = 5;
}