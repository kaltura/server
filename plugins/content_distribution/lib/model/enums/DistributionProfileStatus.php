<?php
/**
 * @package plugins.contentDistribution
 * @subpackage model.enum
 */
interface DistributionProfileStatus extends BaseEnum
{
	const DISABLED = 1;
	const ENABLED = 2;
	const DELETED = 3;
}