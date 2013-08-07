<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiasiv1p2InterpretvarType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiasiMaterialType
	 */
	protected $material;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'material':
				return 'WebexXmlQtiasiMaterialType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'material',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'material',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'interpretvarType';
	}
	
	/**
	 * @param WebexXmlQtiasiMaterialType $material
	 */
	public function setMaterial(WebexXmlQtiasiMaterialType $material)
	{
		$this->material = $material;
	}
	
	/**
	 * @return WebexXmlQtiasiMaterialType $material
	 */
	public function getMaterial()
	{
		return $this->material;
	}
	
}
		
