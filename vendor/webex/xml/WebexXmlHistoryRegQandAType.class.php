<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlHistoryRegQandAType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $regQ;
	
	/**
	 *
	 * @var string
	 */
	protected $regA;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'regQ':
				return 'string';
	
			case 'regA':
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
			'regQ',
			'regA',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'regQ',
			'regA',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'regQandAType';
	}
	
	/**
	 * @param string $regQ
	 */
	public function setRegQ($regQ)
	{
		$this->regQ = $regQ;
	}
	
	/**
	 * @return string $regQ
	 */
	public function getRegQ()
	{
		return $this->regQ;
	}
	
	/**
	 * @param string $regA
	 */
	public function setRegA($regA)
	{
		$this->regA = $regA;
	}
	
	/**
	 * @return string $regA
	 */
	public function getRegA()
	{
		return $this->regA;
	}
	
}

