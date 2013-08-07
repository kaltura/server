<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiasiv1p2OrderType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiasiOrder_extensionType
	 */
	protected $order_extension;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'order_extension':
				return 'WebexXmlQtiasiOrder_extensionType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'order_extension',
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
		return 'orderType';
	}
	
	/**
	 * @param WebexXmlQtiasiOrder_extensionType $order_extension
	 */
	public function setOrder_extension(WebexXmlQtiasiOrder_extensionType $order_extension)
	{
		$this->order_extension = $order_extension;
	}
	
	/**
	 * @return WebexXmlQtiasiOrder_extensionType $order_extension
	 */
	public function getOrder_extension()
	{
		return $this->order_extension;
	}
	
}
		
