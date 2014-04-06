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
	const MISSING_ASSET = 5;
	const CONDITION_NOT_MET = 6; // Not an error, rather a notice that a distribution condition is not met
}