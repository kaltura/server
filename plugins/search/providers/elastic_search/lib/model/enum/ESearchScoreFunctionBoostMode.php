<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */

interface ESearchScoreFunctionBoostMode extends BaseEnum
{
	const MULTIPLY = 'multiply';
	const SUM = 'sum';
}
