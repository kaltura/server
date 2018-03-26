<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface ServerNodeStatus extends BaseEnum
{
	const ACTIVE = 1;
	const DISABLED = 2;
	const DELETED = 3;
	const NOT_REGISTERED = 4;
}
