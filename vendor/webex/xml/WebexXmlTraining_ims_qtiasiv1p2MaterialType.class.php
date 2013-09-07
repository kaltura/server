<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiasiv1p2MaterialType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiasiMattextType
	 */
	protected $mattext;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'mattext':
				return 'WebexXmlQtiasiMattextType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'mattext',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'mattext',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'materialType';
	}
	
	/**
	 * @param WebexXmlQtiasiMattextType $mattext
	 */
	public function setMattext(WebexXmlQtiasiMattextType $mattext)
	{
		$this->mattext = $mattext;
	}
	
	/**
	 * @return WebexXmlQtiasiMattextType $mattext
	 */
	public function getMattext()
	{
		return $this->mattext;
	}
	
}
		
