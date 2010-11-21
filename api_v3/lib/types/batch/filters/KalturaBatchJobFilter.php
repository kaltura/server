<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaBatchJobFilter extends KalturaBatchJobBaseFilter
{
	protected function toDynamicJobSubTypeValues($jobType, $jobSubTypeIn)
	{
		$data = new KalturaJobData();
		switch($jobType)
		{
			case KalturaBatchJobType::BULKUPLOAD:
				$data = new KalturaBulkUploadJobData();
				break;
				
			case KalturaBatchJobType::CONVERT:
				$data = new KalturaConvertJobData();
				break;
				
			case KalturaBatchJobType::CONVERT_PROFILE:
				$data = new KalturaConvertProfileJobData();
				break;
				
			case KalturaBatchJobType::EXTRACT_MEDIA:
				$data = new KalturaExtractMediaJobData();
				break;
				
			case KalturaBatchJobType::IMPORT:
				$data = new KalturaImportJobData();
				break;
				
			case KalturaBatchJobType::POSTCONVERT:
				$data = new KalturaPostConvertJobData();
				break;
				
			case KalturaBatchJobType::PULL:
				$data = new KalturaPullJobData();
				break;
				
			case KalturaBatchJobType::REMOTE_CONVERT:
				$data = new KalturaRemoteConvertJobData();
				break;
				
			case KalturaBatchJobType::MAIL:
				$data = new KalturaMailJobData();
				break;
				
			case KalturaBatchJobType::NOTIFICATION:
				$data = new KalturaNotificationJobData();
				break;
				
			case KalturaBatchJobType::BULKDOWNLOAD:
				$data = new KalturaBulkDownloadJobData();
				break;
				
			case KalturaBatchJobType::FLATTEN:
				$data = new KalturaFlattenJobData();
				break;
				
			case KalturaBatchJobType::PROVISION_PROVIDE:
			case KalturaBatchJobType::PROVISION_DELETE:
				$data = new KalturaProvisionJobData();
				break;
				
			case KalturaBatchJobType::CONVERT_COLLECTION:
				$data = new KalturaConvertCollectionJobData();
				break;
				
			case KalturaBatchJobType::STORAGE_EXPORT:
				$data = new KalturaStorageExportJobData();
				break;
				
			case KalturaBatchJobType::STORAGE_DELETE:
				$data = new KalturaStorageDeleteJobData();
				break;
				
			default:
				$data = KalturaPluginManager::loadObject('KalturaJobData', $jobType);
		}
		
		$jobSubTypeArray = explode(baseObjectFilter::IN_SEPARATOR, $jobSubTypeIn);
		$dbJobSubTypeArray = array();
		foreach($jobSubTypeArray as $jobSubType)
			$dbJobSubTypeArray[] = $data->toSubType($jobSubType);
			
		return implode(baseObjectFilter::IN_SEPARATOR, $dbJobSubTypeArray);
	}
	
	/**
	 * @param int $jobType
	 * @return BatchJobFilter
	 */
	public function toFilter($jobType = null)
	{
		$batchJobFilter = $this->toObject(new BatchJobFilter());
		
		if(!is_null($jobType) && !is_null($this->jobSubTypeIn))
		{
			$jobSubTypeIn = $this->toDynamicJobSubTypeValues($jobType, $this->jobSubTypeIn);
			$batchJobFilter->set('_in_job_sub_type', $jobSubTypeIn);
		}
		
		return $batchJobFilter;
	}
}
