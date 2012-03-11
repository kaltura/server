<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface accessControlActionType extends BaseEnum
{
	const BLOCK = 1;
	const PREVIEW = 2;
	
// TODO not supported yet
//	const ALTERNATE_CONTENT = 3;
//	const LIMIT_BANDWIDTH = 4;
//	const LIMIT_FLAVORS = 5;
}
