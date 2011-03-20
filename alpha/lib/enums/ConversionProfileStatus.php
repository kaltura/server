<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface ConversionProfileStatus extends BaseEnum
{
	const DISABLED  = 1;
	const ENABLED = 2;
	const DELETED = 3;
}