<?php
/**
 * @package plugins.bulkUpload
 * @subpackage lib
 */
class KalturaBulkUploadCsvVersion extends KalturaEnum
{
	const V1 = 1; // 5 values in a row
	const V2 = 2; // 12 values in a row
	const V3 = 3; // values defined in header
}