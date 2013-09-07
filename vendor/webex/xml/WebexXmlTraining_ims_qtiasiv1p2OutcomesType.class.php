<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiasiv1p2OutcomesType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiasiDecvarType
	 */
	protected $decvar;
	
	/**
	 *
	 * @var WebexXmlQtiasiInterpretvarType
	 */
	protected $interpretvar;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'decvar':
				return 'WebexXmlQtiasiDecvarType';
	
			case 'interpretvar':
				return 'WebexXmlQtiasiInterpretvarType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'decvar',
			'interpretvar',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'decvar',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'outcomesType';
	}
	
	/**
	 * @param WebexXmlQtiasiDecvarType $decvar
	 */
	public function setDecvar(WebexXmlQtiasiDecvarType $decvar)
	{
		$this->decvar = $decvar;
	}
	
	/**
	 * @return WebexXmlQtiasiDecvarType $decvar
	 */
	public function getDecvar()
	{
		return $this->decvar;
	}
	
	/**
	 * @param WebexXmlQtiasiInterpretvarType $interpretvar
	 */
	public function setInterpretvar(WebexXmlQtiasiInterpretvarType $interpretvar)
	{
		$this->interpretvar = $interpretvar;
	}
	
	/**
	 * @return WebexXmlQtiasiInterpretvarType $interpretvar
	 */
	public function getInterpretvar()
	{
		return $this->interpretvar;
	}
	
}
		
