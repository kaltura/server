<?php
/**
 * @package plugins.enhancedSearch
 * @subpackage model.enum
 */ 
interface EnhancedSearchItemType extends BaseEnum
{
	const EXACT_MATCH = 1;
	const PARTIAL = 2;
	const STARTS_WITH = 3;
	const DOESNT_CONTAIN = 4;
}