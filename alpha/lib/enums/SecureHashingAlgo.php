<?php
/**
 * @package Core
 * @subpackage model.enum
 */

interface SecureHashingAlgo extends BaseEnum
{
	const SHA_1 = 'sha1';
	const SHA_256 = 'sha256';
	const SHA_512 = 'sha512';
}
