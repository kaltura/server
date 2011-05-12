<?php
/**
 * @package plugins.contentDistribution
 * @subpackage model.enum
 */
interface DistributionProfileActionStatus extends BaseEnum
{
	const DISABLED = 1;
	const AUTOMATIC = 2;
	const MANUAL = 3;
}