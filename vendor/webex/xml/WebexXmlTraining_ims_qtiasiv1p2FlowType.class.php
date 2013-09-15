<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiasiv1p2FlowType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlQtiasiMaterialType>
	 */
	protected $material;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlQtiasiResponse_lidType>
	 */
	protected $response_lid;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlQtiasiResponse_strType>
	 */
	protected $response_str;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'material':
				return 'WebexXmlArray<WebexXmlQtiasiMaterialType>';
	
			case 'response_lid':
				return 'WebexXmlArray<WebexXmlQtiasiResponse_lidType>';
	
			case 'response_str':
				return 'WebexXmlArray<WebexXmlQtiasiResponse_strType>';
	
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
			'response_lid',
			'response_str',
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
		return 'flowType';
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlQtiasiMaterialType> $material
	 */
	public function setMaterial(WebexXmlArray $material)
	{
		if($material->getType() != 'WebexXmlQtiasiMaterialType')
			throw new WebexXmlException(get_class($this) . "::material must be of type WebexXmlQtiasiMaterialType");
		
		$this->material = $material;
	}
	
	/**
	 * @return WebexXmlArray $material
	 */
	public function getMaterial()
	{
		return $this->material;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlQtiasiResponse_lidType> $response_lid
	 */
	public function setResponse_lid(WebexXmlArray $response_lid)
	{
		if($response_lid->getType() != 'WebexXmlQtiasiResponse_lidType')
			throw new WebexXmlException(get_class($this) . "::response_lid must be of type WebexXmlQtiasiResponse_lidType");
		
		$this->response_lid = $response_lid;
	}
	
	/**
	 * @return WebexXmlArray $response_lid
	 */
	public function getResponse_lid()
	{
		return $this->response_lid;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlQtiasiResponse_strType> $response_str
	 */
	public function setResponse_str(WebexXmlArray $response_str)
	{
		if($response_str->getType() != 'WebexXmlQtiasiResponse_strType')
			throw new WebexXmlException(get_class($this) . "::response_str must be of type WebexXmlQtiasiResponse_strType");
		
		$this->response_str = $response_str;
	}
	
	/**
	 * @return WebexXmlArray $response_str
	 */
	public function getResponse_str()
	{
		return $this->response_str;
	}
	
}
		
