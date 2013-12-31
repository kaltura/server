<?php
/**
 * This class represents object-specific data passed to the 
 * bulk upload job.
 * @abstract
 * @package plugins.bulkUpload
 * @subpackage api.objects
 *
 */
abstract class KalturaBulkServiceData extends KalturaObject
{
	abstract public function getType ();
	abstract public function toBulkUploadJobData(KalturaBulkUploadJobData $jobData);
}