<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlGetAPIVersion.class.php');

class WebexXmlGetAPIVersionRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var boolean
	 */
	protected $returnTrainReleaseVersion;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'returnTrainReleaseVersion',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'ep';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'ep:getAPIVersion';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlGetAPIVersion';
	}
	
	/**
	 * @param boolean $returnTrainReleaseVersion
	 */
	public function setReturnTrainReleaseVersion($returnTrainReleaseVersion)
	{
		$this->returnTrainReleaseVersion = $returnTrainReleaseVersion;
	}
	
}
		
