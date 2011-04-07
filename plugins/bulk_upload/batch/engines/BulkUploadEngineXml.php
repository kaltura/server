<?php
/**
 * Class for the handling Bulk upload using XML in the system 
 * 
 * @package Scheduler
 * @subpackage Provision
 */
class BulkUploadEngineXml extends KBulkUploadEngine
{
	/**
	 * @return string
	 */
	public function getName()
	{
		return get_class($this);
	}
	
	/**
	 * @param KSchedularTaskConfig $taskConfig
	 */
	protected function __construct( KSchedularTaskConfig $taskConfig )
	{
		parent::__construct($taskConfig);
				
		KalturaLog::debug("Connecting to Akamai(username: $username, password: $password)");
	}
	
	/* (non-PHPdoc)
	 * @see KBulkUploadEngine::HandleBulkUpload()
	 */
	public function HandleBulkUpload(KalturaBatchJob $job, KalturaBulkUploadJobData $data) 
	{
		//Add XML logic here
	}
	
	/* (non-PHPdoc)
	 * @see KBulkUploadEngine::validateFile()
	 */
	public function validateFile(KalturaBatchJob $job, KalturaBulkUploadJobData $data) {
		// TODO Auto-generated method stub
		
	}

/* (non-PHPdoc)
	 * @see KBulkUploadEngine::parseRows()
	 */
	public function parseRows(KalturaBatchJob $job, KalturaBulkUploadJobData $data) {
		// TODO Auto-generated method stub
		
	}

/* (non-PHPdoc)
	 * @see KBulkUploadEngine::init()
	 */
	public function init(KalturaBatchJob $job, KalturaBulkUploadJobData $data) {
		// TODO Auto-generated method stub
		
	}

/* (non-PHPdoc)
	 * @see KBulkUploadEngine::close()
	 */
	public function close(KalturaBatchJob $job, KalturaBulkUploadJobData $data) {
		// TODO Auto-generated method stub
		
	}

}


