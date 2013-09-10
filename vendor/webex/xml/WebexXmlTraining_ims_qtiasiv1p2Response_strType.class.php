<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiasiv1p2Response_strType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiasiRender_fibType
	 */
	protected $render_fib;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'render_fib':
				return 'WebexXmlQtiasiRender_fibType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'render_fib',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'render_fib',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'response_strType';
	}
	
	/**
	 * @param WebexXmlQtiasiRender_fibType $render_fib
	 */
	public function setRender_fib(WebexXmlQtiasiRender_fibType $render_fib)
	{
		$this->render_fib = $render_fib;
	}
	
	/**
	 * @return WebexXmlQtiasiRender_fibType $render_fib
	 */
	public function getRender_fib()
	{
		return $this->render_fib;
	}
	
}
		
