<?php
/**
 * @package plugins
 * @subpackage lib
 */
class kBulkUploadXmlJobData extends kBulkUploadJobData
{
	/**
	 * Created by the API
	 * 
	 * @var string
	 */
	private $xmlFilePath;
			
	/**
	 * @return the $xmlFilePath
	 */
	public function getXmlFilePath() {
		return $this->xmlFilePath;
	}

	
	/**
	 * @param string $xmlFilePath
	 */
	public function setXmlFilePath($xmlFilePath) {
		$this->xmlFilePath = $xmlFilePath;
	}
}