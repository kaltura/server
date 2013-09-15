<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiasiv1p2SectionType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlQtiasiItemType>
	 */
	protected $item;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'item':
				return 'WebexXmlArray<WebexXmlQtiasiItemType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'item',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'item',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'sectionType';
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlQtiasiItemType> $item
	 */
	public function setItem(WebexXmlArray $item)
	{
		if($item->getType() != 'WebexXmlQtiasiItemType')
			throw new WebexXmlException(get_class($this) . "::item must be of type WebexXmlQtiasiItemType");
		
		$this->item = $item;
	}
	
	/**
	 * @return WebexXmlArray $item
	 */
	public function getItem()
	{
		return $this->item;
	}
	
}
		
