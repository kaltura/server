<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventEnrollmentFieldType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $incl;
	
	/**
	 *
	 * @var boolean
	 */
	protected $req;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'incl':
				return 'boolean';
	
			case 'req':
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
			'incl',
			'req',
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
		return 'enrollmentFieldType';
	}
	
	/**
	 * @param boolean $incl
	 */
	public function setIncl($incl)
	{
		$this->incl = $incl;
	}
	
	/**
	 * @return boolean $incl
	 */
	public function getIncl()
	{
		return $this->incl;
	}
	
	/**
	 * @param boolean $req
	 */
	public function setReq($req)
	{
		$this->req = $req;
	}
	
	/**
	 * @return boolean $req
	 */
	public function getReq()
	{
		return $this->req;
	}
	
}
		
