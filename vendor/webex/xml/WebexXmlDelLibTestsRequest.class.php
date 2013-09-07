<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlDelLibTests.class.php');
require_once(__DIR__ . '/long.class.php');

class WebexXmlDelLibTestsRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlArray<long>
	 */
	protected $testID;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'testID',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'testID',
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
		return 'trainingsession:delLibTests';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlDelLibTests';
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
	
}
		
