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
	const ASSET_PROPERTIES_COMPARE = 8;
	const USER_ROLE = 9;
	const GEO_DISTANCE = 10;
	const OR_OPERATOR = 11;
	const HASH = 12;
	const DELIVERY_PROFILE = 13;
	const ACTIVE_EDGE_VALIDATE = 14;
	const ANONYMOUS_IP = 15;
	const FLAVOR_TYPE = 16;
}
