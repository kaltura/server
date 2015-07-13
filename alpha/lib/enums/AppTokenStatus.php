<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface AppTokenStatus extends BaseEnum
{
	const DISABLED = 1;
	const ACTIVE = 2;
	const DELETED = 3;
}
