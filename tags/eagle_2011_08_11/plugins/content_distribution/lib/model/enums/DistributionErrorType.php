<?php
/**
 * @package plugins.contentDistribution
 * @subpackage model.enum
 */
interface DistributionErrorType extends BaseEnum
{
	const MISSING_FLAVOR = 1;
	const MISSING_THUMBNAIL = 2;
	const MISSING_METADATA = 3;
	const INVALID_DATA = 4;
}