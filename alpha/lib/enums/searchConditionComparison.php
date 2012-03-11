<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface searchConditionComparison extends BaseEnum
{
	const EQUEL = 1;
	const GREATER_THAN = 2;
	const GREATER_THAN_OR_EQUEL = 3;
	const LESS_THAN = 4;
	const LESS_THAN_OR_EQUEL = 5;
}