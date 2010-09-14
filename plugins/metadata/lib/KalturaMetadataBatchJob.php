<?php

/**
 * @package api
 * @subpackage objects
 */
class KalturaMetadataBatchJob extends KalturaBatchJob
{
	public function fromData($dbData)
	{
		if(!$dbData)
			return;
			
		switch(get_class($dbData))
		{
			case 'kImportMetadataJobData':
				$this->data = new KalturaImportMetadataJobData();
				break;
				
			case 'kTransformMetadataJobData':
				$this->data = new KalturaTransformMetadataJobData();
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
			case KalturaBatchJobType::METADATA_IMPORT:
				$dbData = new kImportMetadataJobData();
				if(is_null($this->data))
					$this->data = new KalturaImportMetadataJobData();
				break;
				
			case KalturaBatchJobType::METADATA_TRANSFORM:
				$dbData = new kTransformMetadataJobData();
				if(is_null($this->data))
					$this->data = new KalturaTransformMetadataJobData();
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