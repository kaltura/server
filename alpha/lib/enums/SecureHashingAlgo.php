<?php
/**
 * @package Core
 * @subpackage model.enum
 */

interface SecureHashingAlgo extends BaseEnum
{
	const SHA_1 = 1;
	const SHA_256 = 2;
	const SHA_512 = 3;
}
