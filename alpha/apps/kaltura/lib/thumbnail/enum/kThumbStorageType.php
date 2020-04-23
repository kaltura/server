<?php
/**
 * @package core
 * @subpackage thumbnail.enum
 */

interface kThumbStorageType extends BaseEnum
{
	const NONE = 0;
	const S3 = 1;
	const LOCAL = 2;
}