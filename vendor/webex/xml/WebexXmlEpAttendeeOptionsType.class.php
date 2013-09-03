<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEpAttendeeOptionsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $joinApproval;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'joinApproval':
				return 'boolean';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'joinApproval',
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
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'attendeeOptionsType';
	}
	
	/**
	 * @param boolean $joinApproval
	 */
	public function setJoinApproval($joinApproval)
	{
		$this->joinApproval = $joinApproval;
	}
	
	/**
	 * @return boolean $joinApproval
	 */
	public function getJoinApproval()
	{
		return $this->joinApproval;
	}
	
}
		
