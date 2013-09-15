<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlComPsoFieldsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $psoField1;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'psoField1':
				return 'WebexXml';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'psoField1',
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
		return 'psoFieldsType';
	}
	
	/**
	 * @param WebexXml $psoField1
	 */
	public function setPsoField1(WebexXml $psoField1)
	{
		$this->psoField1 = $psoField1;
	}
	
	/**
	 * @return WebexXml $psoField1
	 */
	public function getPsoField1()
	{
		return $this->psoField1;
	}
	
}
		
