<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiasiv1p2Response_lidType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiasiRender_choiceType
	 */
	protected $render_choice;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'render_choice':
				return 'WebexXmlQtiasiRender_choiceType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'render_choice',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'render_choice',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'response_lidType';
	}
	
	/**
	 * @param WebexXmlQtiasiRender_choiceType $render_choice
	 */
	public function setRender_choice(WebexXmlQtiasiRender_choiceType $render_choice)
	{
		$this->render_choice = $render_choice;
	}
	
	/**
	 * @return WebexXmlQtiasiRender_choiceType $render_choice
	 */
	public function getRender_choice()
	{
		return $this->render_choice;
	}
	
}
		
