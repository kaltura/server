<?php
/**
 * @package plugins
 * @subpackage bulk_upload_csv
 */
class KalturaBulkUploadCsvVersion extends KalturaStringEnum
{
	const V1 = 1; // 5 values in a row
	const V2 = 2; // 12 values in a row
	const V3 = 3; // values defined in header
}