<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */ 
interface ESearchItemType extends BaseEnum
{
	const EXACT_MATCH = 1;
	const PARTIAL = 2;
	const STARTS_WITH = 3;
	const DOESNT_CONTAIN = 4;
	const RANGE = 5;
}
