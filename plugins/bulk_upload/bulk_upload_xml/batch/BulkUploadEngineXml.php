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
	 * @var string
	 */
	private $xsdFilePath = "{dirname(__FILE__)}../lib/schema/ingestion.xsd";
	 
	/**
	 * @return string
	 */
	public function getName()
	{
		return get_class($this);
	}
	
	/* (non-PHPdoc)
	 * @see KBulkUploadEngine::HandleBulkUpload()
	 */
	public function handleBulkUpload() 
	{
		$this->validate();
	    $this->parse();
	}
	
	/**
	 * 
	 * Validates that the xml is valid using hte XSD
	 */
	protected function validate() 
	{
		$xdoc = new DomDocument;
		$xdoc->Load($this->data->filePath);
		//Validate the XML file against the schema
		if(!$xdoc->schemaValidate($this->xsdFilePath)) 
		{
			throw new KalturaException("Validate files failed on job [$this->job->id]", KalturaBatchJobAppErrors::BULK_VALIDATION_FAILED);
		}
		
		return true;
	}

	/**
	 * 
	 * Parses the Xml file lines and creates the right actions in the system
	 */
	protected function parse() 
	{
		//TODO: Roni - Add Xml parsing logic here
		
	}
}
