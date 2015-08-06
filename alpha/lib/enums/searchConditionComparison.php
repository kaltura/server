<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface searchConditionComparison extends BaseEnum
{
	const EQUAL = 1;
	const GREATER_THAN = 2;
	const GREATER_THAN_OR_EQUAL = 3;
	const LESS_THAN = 4;
	const LESS_THAN_OR_EQUAL = 5;
	const NOT_EQUAL = 6;
}