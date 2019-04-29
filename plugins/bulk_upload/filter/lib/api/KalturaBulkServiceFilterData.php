<?php

/**
 * Represents the Bulk service input for filter bulk upload
 * @package plugins.bulkUploadFilter
 * @subpackage api.objects
 */
class KalturaBulkServiceFilterData extends KalturaBulkServiceFilterDataBase
{
	/**
	 * Template object for new object creation
	 * @var KalturaObject
	 */
	public $templateObject;

	
	public function toBulkUploadJobData(KalturaBulkUploadJobData $jobData)
	{
		$jobData->filter = $this->filter;
		$jobData->templateObject = $this->templateObject;
	}
}