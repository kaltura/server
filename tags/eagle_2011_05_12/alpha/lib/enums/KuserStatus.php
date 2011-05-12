<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface KuserStatus extends BaseEnum
{
	const BLOCKED = 0;
	const ACTIVE = 1;
	const DELETED = 2;	
}