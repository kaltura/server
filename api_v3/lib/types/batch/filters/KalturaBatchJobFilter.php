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
				
			case KalturaBatchJobType::INDEX:
				$data = new KalturaIndexJobData();
				break;
				
			case KalturaBatchJobType::COPY:
				$data = new KalturaCopyJobData();
				break;
				
			case KalturaBatchJobType::DELETE:
				$data = new KalturaDeleteJobData();
				break;

			case KalturaBatchJobType::DELETE_FILE:
				$data = new KalturaDeleteFileJobData();
				break;
				
			case KalturaBatchJobType::MOVE_CATEGORY_ENTRIES:
				$data = new KalturaMoveCategoryEntriesJobData();
				break;
				
			default:
				$data = KalturaPluginManager::loadObject('KalturaJobData', $jobType);
				KalturaLog::debug("Loaded data type [" . get_class($data) . "] for job type [$jobType]");
		}
		
		if(!$data)
		{
			KalturaLog::err("Data type not found for job type [$jobType]");
			return null;
		}
			
		$jobSubTypeArray = explode(baseObjectFilter::IN_SEPARATOR, $jobSubTypeIn);
		$dbJobSubTypeArray = array();
		foreach($jobSubTypeArray as $jobSubType)
			$dbJobSubTypeArray[] = $data->toSubType($jobSubType);
			
		$dbJobSubType = implode(baseObjectFilter::IN_SEPARATOR, $dbJobSubTypeArray);
		KalturaLog::debug("Filter sub types translated from [$jobSubTypeIn] to [$dbJobSubType]");
		return $dbJobSubType;
	}

	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new BatchJobFilter();
	}
	
	/**
	 * @param int $jobType
	 * @return BatchJobFilter
	 */
	public function toFilter($jobType = null)
	{
		$batchJobFilter = $this->toObject(new BatchJobFilter(false));
		
		if(!is_null($jobType) && !is_null($this->jobSubTypeIn))
		{
			$jobSubTypeIn = $this->toDynamicJobSubTypeValues($jobType, $this->jobSubTypeIn);
			$batchJobFilter->set('_in_job_sub_type', $jobSubTypeIn);
		}
	
		if(!is_null($jobType) && !is_null($this->jobSubTypeNotIn))
		{
			$jobSubTypeNotIn = $this->toDynamicJobSubTypeValues($jobType, $this->jobSubTypeNotIn);
			$batchJobFilter->set('_notin_job_sub_type', $jobSubTypeNotIn);
		}
		
		return $batchJobFilter;
	}

	/* (non-PHPdoc)
	 * @see KalturaFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null)
	{
		$batchJobFilter = new BatchJobFilter(true);
		$this->toObject($batchJobFilter);
		
		$c = new Criteria();
		
		$batchJobFilter->attachToCriteria($c);
		
		if(!$pager)
		   $pager = new KalturaFilterPager();
		
		$pager->attachToCriteria($c);
		
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL2;
		
		$list = BatchJobPeer::doSelect($c);
		
		$c->setLimit(false);
		$count = BatchJobPeer::doCount($c);

		$newList = KalturaBatchJobArray::fromStatisticsBatchJobArray($list);
		
		$response = new KalturaBatchJobListResponse();
		$response->objects = $newList;
		$response->totalCount = $count;
		
		myDbHelper::$use_alternative_con = null;
		
		return $response;
	}
		
	}

}
