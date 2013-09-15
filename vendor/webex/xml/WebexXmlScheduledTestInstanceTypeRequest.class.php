<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlScheduledTestInstanceType.class.php');
require_once(__DIR__ . '/WebexXmlTrainTestDeliveryType.class.php');
require_once(__DIR__ . '/WebexXmlTrainTestStatusType.class.php');

class WebexXmlScheduledTestInstanceTypeRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var integer
	 */
	protected $testID;
	
	/**
	 *
	 * @var string
	 */
	protected $title;
	
	/**
	 *
	 * @var WebexXmlTrainTestDeliveryType
	 */
	protected $delivery;
	
	/**
	 *
	 * @var WebexXmlTrainTestStatusType
	 */
	protected $status;
	
	/**
	 *
	 * @var string
	 */
	protected $dueDate;
	
	/**
	 *
	 * @var long
	 */
	protected $sessionKey;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'testID',
			'title',
			'delivery',
			'status',
			'dueDate',
			'sessionKey',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'testID',
			'title',
			'delivery',
			'status',
			'sessionKey',
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
		return 'trainingsession:scheduledTestInstanceType';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlScheduledTestInstanceType';
	}
	
	/**
	 * @param integer $testID
	 */
	public function setTestID($testID)
	{
		$this->testID = $testID;
	}
	
	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	/**
	 * @param WebexXmlTrainTestDeliveryType $delivery
	 */
	public function setDelivery(WebexXmlTrainTestDeliveryType $delivery)
	{
		$this->delivery = $delivery;
	}
	
	/**
	 * @param WebexXmlTrainTestStatusType $status
	 */
	public function setStatus(WebexXmlTrainTestStatusType $status)
	{
		$this->status = $status;
	}
	
	/**
	 * @param string $dueDate
	 */
	public function setDueDate($dueDate)
	{
		$this->dueDate = $dueDate;
	}
	
	/**
	 * @param long $sessionKey
	 */
	public function setSessionKey($sessionKey)
	{
		$this->sessionKey = $sessionKey;
	}
	
}

