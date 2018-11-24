<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */
interface ESearchFilterItemType extends BaseEnum
{
	const EXACT_MATCH = 1;
	const PARTIAL = 2;
	const STARTS_WITH = 3;
	const EXISTS = 4;
	const RANGE = 5;
	const EXACT_MATCH_MULTI = 6;
	const EXACT_MATCH_NOT = 7;
	const PARTIAL_MULTI_OR = 8;
	const PARTIAL_MULTI_AND = 9;
	const RANGE_GTE = 10;
	const RANGE_LTE = 11;
}
