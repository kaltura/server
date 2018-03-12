<?php
/**
 * @package plugins.contentDistribution
 * @subpackage model.enum
 */
interface DistributionFieldType extends BaseEnum
{
	const STRING = 0;
	const INT = 1;
	const LONG = 2;
	const TIMESTAMP = 3;

}