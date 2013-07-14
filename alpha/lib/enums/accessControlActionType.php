<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface accessControlActionType extends BaseEnum
{
	const BLOCK = 1;
	const PREVIEW = 2;
	const LIMIT_FLAVORS = 3;
	
// TODO not supported yet
//	const ALTERNATE_CONTENT = 3;
//	const LIMIT_BANDWIDTH = 4;
//	
}
