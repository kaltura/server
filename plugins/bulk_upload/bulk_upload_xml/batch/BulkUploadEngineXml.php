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
	 * 
	 * The engine xsd file path
	 * @var unknown_type
	 */
	private $xsdFilePath = "{dirname(__FILE__)}../lib/schema/ingestion.xsd";
	 
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
	public function __construct( KSchedularTaskConfig $taskConfig, KalturaClient $kClient)
	{
		parent::__construct($taskConfig, $kClient);
		KalturaLog::debug("Created new KBulkUploadEngineXml");
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
	 * @see KBulkUploadEngine::validate()
	 */
	protected function validate(KalturaBatchJob $job, KalturaBulkUploadJobData $data) 
	{
		$xmlFilePath =$job->$xmlFilePath;
		
//		//TOOD: Roni - add the xsd file path here (or get it from a configuration)
//		$xsdFilePath = 
		
		$xdoc = new DomDocument;
		$xdoc->Load($xmlFilePath);
		//Validate the XML file against the schema
		if ($xdoc->schemaValidate($this->xsdFilePath)) 
		{
			return true;
		} 
		
		return false;
	}

	/* (non-PHPdoc)
	 * @see KBulkUploadEngine::parse()
	 */
	public function parse(KalturaBatchJob $job, KalturaBulkUploadJobData $data) {
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