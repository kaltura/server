<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface CategoryKuserStatus extends BaseEnum
{
	const ACTIVE = 1;
	const PENDING = 2;
	const NOT_ACTIVE = 3; //for archive CategoryUsers but without regecting them.
	const DELETED = 4;
}
