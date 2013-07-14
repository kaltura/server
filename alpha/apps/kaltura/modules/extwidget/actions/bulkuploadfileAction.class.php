<?php
/**
 * @package Core
 * @subpackage externalWidgets
 */
class bulkuploadfileAction extends sfAction
{
	public function execute()
	{
		$jobId = $this->getRequestParameter("id");
		$partnerId = $this->getRequestParameter("pid");
		$type = $this->getRequestParameter("type");
		
		$c = new Criteria();
		$c->addAnd(BatchJobPeer::ID, $jobId);
		$c->addAnd(BatchJobPeer::PARTNER_ID, $partnerId);
		$c->addAnd(BatchJobPeer::JOB_TYPE, BatchJobType::BULKUPLOAD);
		$batchJob = BatchJobPeer::doSelectOne($c);
		if (!$batchJob)
			die("File not found");
			
		header("Content-Type: text/plain; charset=UTF-8");
			
		if ($type == "log")
		{
			$criteria = new Criteria();
			$criteria->add(BulkUploadResultPeer::BULK_UPLOAD_JOB_ID, $jobId);
			$criteria->addAscendingOrderByColumn(BulkUploadResultPeer::LINE_INDEX);
			$criteria->setLimit(100);
			
			$bulkUploadResults = BulkUploadResultPeer::doSelect($criteria);
			if(!count($bulkUploadResults))
				die("Log file is not ready");
				
			$STDOUT = fopen('php://output', 'w');
			$data = $batchJob->getData();
			
			$handledResults = 0;
			while(count($bulkUploadResults))
			{
				$handledResults += count($bulkUploadResults);
				foreach($bulkUploadResults as $bulkUploadResult)
				{
					$values = array(
						$bulkUploadResult->getTitle(),
						$bulkUploadResult->getDescription(),
						$bulkUploadResult->getTags(),
						$bulkUploadResult->getUrl(),
						$bulkUploadResult->getContentType(),
					);
					
					if($data instanceof kBulkUploadCsvJobData && $data->getCsvVersion() > 1)
					{
						$values[] = $bulkUploadResult->getConversionProfileId();
						$values[] = $bulkUploadResult->getAccessControlProfileId();
						$values[] = $bulkUploadResult->getCategory();
						$values[] = $bulkUploadResult->getScheduleStartDate('Y-m-d\TH:i:s');
						$values[] = $bulkUploadResult->getScheduleEndDate('Y-m-d\TH:i:s');
						$values[] = $bulkUploadResult->getThumbnailUrl();
						$values[] = $bulkUploadResult->getPartnerData();
					}
					$values[] = $bulkUploadResult->getEntryId();
					$values[] = $bulkUploadResult->getEntryStatus();
					$values[] = $bulkUploadResult->getErrorDescription();
					
					fputcsv($STDOUT, $values);
				}
	    		
	    		if(count($bulkUploadResults) < $criteria->getLimit())
	    			break;
	    			
	    		kMemoryManager::clearMemory();
	    		$criteria->setOffset($handledResults);
				$bulkUploadResults = BulkUploadResultPeer::doSelect($criteria);
			}
			fclose($STDOUT);
		}
		else
		{
			$syncKey = $batchJob->getSyncKey(BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOAD);

			if (kFileSyncUtils::file_exists($syncKey, true))
			{
				$content = kFileSyncUtils::file_get_contents($syncKey, true);
				echo $content;
				die;
			}
			else
			{
				die("File not found");
			}
		}
		die; //  no template needed
	}
}
