<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlHistoryViewTimeScopeType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $viewTimeStart;
	
	/**
	 *
	 * @var string
	 */
	protected $viewTimeEnd;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'viewTimeStart':
				return 'string';
	
			case 'viewTimeEnd':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'viewTimeStart',
			'viewTimeEnd',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'viewTimeStart',
			'viewTimeEnd',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'viewTimeScopeType';
	}
	
	/**
	 * @param string $viewTimeStart
	 */
	public function setViewTimeStart($viewTimeStart)
	{
		$this->viewTimeStart = $viewTimeStart;
	}
	
	/**
	 * @return string $viewTimeStart
	 */
	public function getViewTimeStart()
	{
		return $this->viewTimeStart;
	}
	
	/**
	 * @param string $viewTimeEnd
	 */
	public function setViewTimeEnd($viewTimeEnd)
	{
		$this->viewTimeEnd = $viewTimeEnd;
	}
	
	/**
	 * @return string $viewTimeEnd
	 */
	public function getViewTimeEnd()
	{
		return $this->viewTimeEnd;
	}
	
}
		
