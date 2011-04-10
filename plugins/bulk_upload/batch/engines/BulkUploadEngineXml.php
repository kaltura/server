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
	public function __construct( KSchedularTaskConfig $taskConfig, $kClient )
	{
		parent::__construct($taskConfig, $kClient);
	}
	
	/* (non-PHPdoc)
	 * @see KBulkUploadEngine::HandleBulkUpload()
	 */
	public function handleBulkUpload(KalturaBatchJob $job, KalturaBulkUploadJobData $data) 
	{
		//Add XML logic here
		parent::handleBulkUpload($job, $data);
	}
	
	/* (non-PHPdoc)
	 * @see KBulkUploadEngine::validateFile()
	 */
	protected function validateFile(KalturaBatchJob $job, KalturaBulkUploadJobData $data) {
		// TODO Auto-generated method stub
		
		$xmlFilePath =$job->$xmlFilePath;
		//TOOD: Roni - add the xsd file path here (or get it from a configuration)
		$xsdFilePath = "";
		$xdoc = new DomDocument;
		//Load the xml document in the DOMDocument object
		$xdoc->Load($xmlFilePath);
		//Validate the XML file against the schema
		if ($xdoc->schemaValidate($xsdFilePath)) {
		print "$xmlFilePath is valid.\n";
		} else {
		print "$xmlFilePath is invalid.\n";
		}
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


