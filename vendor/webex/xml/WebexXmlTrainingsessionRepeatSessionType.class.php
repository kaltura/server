<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTrainingsessionRepeatSessionType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlTrainActionType
	 */
	protected $action;
	
	/**
	 *
	 * @var int
	 */
	protected $index;
	
	/**
	 *
	 * @var string
	 */
	protected $startDate;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'action':
				return 'WebexXmlTrainActionType';
	
			case 'index':
				return 'int';
	
			case 'startDate':
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
			'action',
			'index',
			'startDate',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'index',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'repeatSessionType';
	}
	
	/**
	 * @param WebexXmlTrainActionType $action
	 */
	public function setAction(WebexXmlTrainActionType $action)
	{
		$this->action = $action;
	}
	
	/**
	 * @return WebexXmlTrainActionType $action
	 */
	public function getAction()
	{
		return $this->action;
	}
	
	/**
	 * @param int $index
	 */
	public function setIndex($index)
	{
		$this->index = $index;
	}
	
	/**
	 * @return int $index
	 */
	public function getIndex()
	{
		return $this->index;
	}
	
	/**
	 * @param string $startDate
	 */
	public function setStartDate($startDate)
	{
		$this->startDate = $startDate;
	}
	
	/**
	 * @return string $startDate
	 */
	public function getStartDate()
	{
		return $this->startDate;
	}
	
}

