<?php
/**
 * @package plugins.contentDistribution
 * @subpackage model.enum
 */
interface DistributionFieldRequiredStatus extends BaseEnum
{
	const NOT_REQUIRED = 0;
	const REQUIRED_BY_PROVIDER = 1;
	const REQUIRED_BY_PARTNER = 2;
	const REQUIRED_FOR_AUTOMATIC_DISTRIBUTION = 3;
}