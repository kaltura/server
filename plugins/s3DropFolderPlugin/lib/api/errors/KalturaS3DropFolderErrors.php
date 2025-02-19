<?php

/**
 * @package plugins.vendor
 * @subpackage api.errors
 */
class KalturaS3DropFolderErrors extends KalturaErrors
{
	const MISSING_S3ARN_CONFIG = "MISSING_S3_ARN;;'useS3Arn' ('Bucket Policy Allow Access') was set but 's3Arn' config is missing";
}
