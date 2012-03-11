<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface ConditionType extends BaseEnum
{
	const AUTHENTICATED = 1;
	const COUNTRY = 2;
	const IP_ADDRESS = 3;
	const SITE = 4;
	const USER_AGENT = 5;
	const FIELD_MATCH = 6;
	const FIELD_COMPARE = 7;
}
