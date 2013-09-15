<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiresv1p2Section_resultType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlQtiItem_resultType>
	 */
	protected $item_result;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'item_result':
				return 'WebexXmlArray<WebexXmlQtiItem_resultType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'item_result',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'item_result',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'section_resultType';
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlQtiItem_resultType> $item_result
	 */
	public function setItem_result(WebexXmlArray $item_result)
	{
		if($item_result->getType() != 'WebexXmlQtiItem_resultType')
			throw new WebexXmlException(get_class($this) . "::item_result must be of type WebexXmlQtiItem_resultType");
		
		$this->item_result = $item_result;
	}
	
	/**
	 * @return WebexXmlArray $item_result
	 */
	public function getItem_result()
	{
		return $this->item_result;
	}
	
}
		
