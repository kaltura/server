<?php
/**
 * @package plugins.contentDistribution
 * @subpackage model.enum
 */
interface GenericDistributionProviderParser extends BaseEnum
{
	const XSL = 1;
	const XPATH = 2;
	const REGEX = 3;
}