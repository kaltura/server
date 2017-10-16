<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlDelRecordingFromRecycleBin.class.php');

class WebexXmlDelRecordingFromRecycleBinRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlArray<int>
	 */
	protected $recordingID;
	
	/**
	 *
	 * @var boolean
	 */
	protected $deleteAll;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'recordingID',
			'deleteAll',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'recordingID',
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
		return 'ep:delRecordingFromRecycleBin';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlDelRecordingFromRecycleBin';
	}
	
	/**
	 * @param WebexXmlArray<int> $recordingID
	 */
	public function setRecordingID($recordingID)
	{
		$this->recordingID = $recordingID;
	}
	
	/**
	 * @param boolean $deleteAll
	 */
	public function setDeleteAll($deleteAll)
	{
		$this->deleteAll = $deleteAll;
	}
	
}
		
