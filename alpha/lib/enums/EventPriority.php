<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface EventPriority extends BaseEnum
{
	const HIGH = 1;
	const ABOVE_NORMAL = 2;
	const NORMAL = 3;
	const BELOW_NORMAL = 4;
	const LOW = 5;
}
