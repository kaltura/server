<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */

interface ESearchScoreFunctionMode extends BaseEnum
{
	const MULTIPLY = 'multiply';
	const SUM = 'sum';
}