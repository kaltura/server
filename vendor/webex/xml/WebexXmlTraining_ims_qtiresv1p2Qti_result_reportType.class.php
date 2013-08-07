<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiresv1p2Qti_result_reportType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiResultType
	 */
	protected $result;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'result':
				return 'WebexXmlQtiResultType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'result',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'result',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'qti_result_reportType';
	}
	
	/**
	 * @param WebexXmlQtiResultType $result
	 */
	public function setResult(WebexXmlQtiResultType $result)
	{
		$this->result = $result;
	}
	
	/**
	 * @return WebexXmlQtiResultType $result
	 */
	public function getResult()
	{
		return $this->result;
	}
	
}
		
