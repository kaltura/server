<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface AppTokenHashType extends BaseEnum
{
	const SHA1 = 'SHA1';
	const MD5 = 'MD5';
	const SHA256 = 'SHA256';
	const SHA512 = 'SHA512';
}
