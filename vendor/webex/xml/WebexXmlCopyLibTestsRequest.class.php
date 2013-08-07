<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlCopyLibTests.class.php');
require_once(__DIR__ . '/long.class.php');
require_once(__DIR__ . '/WebexXmlTrainShareType.class.php');

class WebexXmlCopyLibTestsRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlArray<long>
	 */
	protected $testID;
	
	/**
	 *
	 * @var WebexXmlTrainShareType
	 */
	protected $copyToType;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'testID',
			'copyToType',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'testID',
			'copyToType',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'trainingsession';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'trainingsession:copyLibTests';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlCopyLibTests';
	}
	
	/**
	 * @param WebexXmlArray<long> $testID
	 */
	public function setTestID($testID)
	{
		if($testID->getType() != 'long')
			throw new WebexXmlException(get_class($this) . "::testID must be of type long");
		
		$this->testID = $testID;
	}
	
	/**
	 * @param WebexXmlTrainShareType $copyToType
	 */
	public function setCopyToType(WebexXmlTrainShareType $copyToType)
	{
		$this->copyToType = $copyToType;
	}
	
}
		
