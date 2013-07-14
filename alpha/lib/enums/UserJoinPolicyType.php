<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface UserJoinPolicyType extends BaseEnum
{
	const AUTO_JOIN = 1;
	const REQUEST_TO_JOIN = 2;
	const NOT_ALLOWED = 3;
}
