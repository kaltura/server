<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTrainingsessionScheduledTestType extends WebexXmlRequestType
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
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'testID':
				return 'integer';
	
			case 'title':
				return 'string';
	
			case 'delivery':
				return 'WebexXmlTrainTestDeliveryType';
	
			case 'status':
				return 'WebexXmlTrainTestStatusType';
	
			case 'dueDate':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
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
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'scheduledTestType';
	}
	
	/**
	 * @param integer $testID
	 */
	public function setTestID($testID)
	{
		$this->testID = $testID;
	}
	
	/**
	 * @return integer $testID
	 */
	public function getTestID()
	{
		return $this->testID;
	}
	
	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	/**
	 * @return string $title
	 */
	public function getTitle()
	{
		return $this->title;
	}
	
	/**
	 * @param WebexXmlTrainTestDeliveryType $delivery
	 */
	public function setDelivery(WebexXmlTrainTestDeliveryType $delivery)
	{
		$this->delivery = $delivery;
	}
	
	/**
	 * @return WebexXmlTrainTestDeliveryType $delivery
	 */
	public function getDelivery()
	{
		return $this->delivery;
	}
	
	/**
	 * @param WebexXmlTrainTestStatusType $status
	 */
	public function setStatus(WebexXmlTrainTestStatusType $status)
	{
		$this->status = $status;
	}
	
	/**
	 * @return WebexXmlTrainTestStatusType $status
	 */
	public function getStatus()
	{
		return $this->status;
	}
	
	/**
	 * @param string $dueDate
	 */
	public function setDueDate($dueDate)
	{
		$this->dueDate = $dueDate;
	}
	
	/**
	 * @return string $dueDate
	 */
	public function getDueDate()
	{
		return $this->dueDate;
	}
	
}
		
