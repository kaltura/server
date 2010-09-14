<?php

/**
 * @package api
 * @subpackage objects
 */
class KalturaFileSyncImportBatchJob extends KalturaBatchJob
{
	
	public function fromData($dbData)
	{
		if(!$dbData)
			return;
			
		switch(get_class($dbData))
		{
			case 'kFileSyncImportJobData':
				$this->data = new KalturaFileSyncImportJobData();
				break;
								
			default:
				$this->data = new KalturaObject();
		}
			
		$this->data->fromObject($dbData);
	}
	    
	
	public function toData(BatchJob $dbBatchJob)
	{
		$dbData = null;
		
		switch($dbBatchJob->getJobType())
		{
			case KalturaBatchJobType::FILESYNC_IMPORT:
				$dbData = new kFileSyncImportJobData();
				if(is_null($this->data))
					$this->data = new KalturaFileSyncImportJobData();
				break;
								
			default:
				$dbData = null;
		}
		
		if(is_null($dbBatchJob->getData()))
			$dbBatchJob->setData($dbData);
	
		if($this->data instanceof KalturaObject)
		{
			$dbData = $this->data->toObject($dbBatchJob->getData());
			$dbBatchJob->setData($dbData);
		}
		
		return $dbData;
	}
	
}