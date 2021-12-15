<?php
class kOneDriveEventsConsumer implements kBatchJobStatusEventConsumer
{

	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		return $this->isImportMatch($dbBatchJob);
	}
	
	public function updatedJob(BatchJob $dbBatchJob)
	{
		try 
		{
			$this->onImportJobStatusFinished($dbBatchJob);
		}
		catch(Exception $e)
		{
			KalturaLog::err('Failed to process updatedJob - '.$e->getMessage());
		}
		return true;					
	}
		
	private function isImportMatch(BatchJob $dbBatchJob)
	{
		if( $dbBatchJob->getJobType() == BatchJobType::IMPORT
			&& $dbBatchJob->getData() instanceof kDropFolderImportJobData
			&& $dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED	)
		{
			$dropFolderFile = DropFolderFilePeer::retrieveByPK($dbBatchJob->getData()->getDropFolderFileId());
			if( $dropFolderFile && $dropFolderFile->getType() === OneDrivePlugin::getDropFolderTypeCoreValue(OneDriveDropFolderType::ONE_DRIVE) )
			{
				/* @var $dropFolder OneDriveDropFolder */
				$dropFolder = DropFolderPeer::retrieveByPK($dropFolderFile->getDropFolderId());
				if($dropFolder && $dropFolder->getDefaultCategoryIds())
				{
					return true;
				}
			}
		}
		return false;
	}
	
	private function onImportJobStatusFinished(BatchJob $dbBatchJob)
	{
		/** @var kDropFolderImportJobData $data */
		$data = $dbBatchJob->getData();
		$dropFolderFile = DropFolderFilePeer::retrieveByPK($data->getDropFolderFileId());
		
		/* @var $dropFolder OneDriveDropFolder */
		$dropFolder = DropFolderPeer::retrieveByPK($dropFolderFile->getDropFolderId());
		
		$categoryIds = explode(',', $dropFolder->getDefaultCategoryIds());
		foreach($categoryIds as $categoryId)
		{
			KalturaLog::info("Adding entry ID ({$dropFolderFile->getEntryId()}) to category ID ({$categoryId})");
			$dbCategoryEntry = new categoryEntry();
			$dbCategoryEntry->add($dropFolderFile->getEntryId(), $categoryId);
			
			$dbCategoryEntry->setEntryId($dropFolderFile->getEntryId());
			$dbCategoryEntry->setCategoryId($categoryId);
			$dbCategoryEntry->setPartnerId($dropFolderFile->getPartnerId());
			
			$dbCategoryEntry->save();
		}
	}
}