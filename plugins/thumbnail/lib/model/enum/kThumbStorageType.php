<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.enum
 */
interface kThumbStorageType extends BaseEnum
{
	const NONE = 0;
	const S3 = 1;
	const LOCAL = 2;
	const GCP = 3;
}