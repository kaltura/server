<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlAttendeeFeedbackDefaultFieldsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $setup;
	
	/**
	 *
	 * @var string
	 */
	protected $easeOfUse;
	
	/**
	 *
	 * @var string
	 */
	protected $performance;
	
	/**
	 *
	 * @var string
	 */
	protected $comment;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'setup':
				return 'string';
	
			case 'easeOfUse':
				return 'string';
	
			case 'performance':
				return 'string';
	
			case 'comment':
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
			'setup',
			'easeOfUse',
			'performance',
			'comment',
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
		return 'feedbackDefaultFieldsType';
	}
	
	/**
	 * @param string $setup
	 */
	public function setSetup($setup)
	{
		$this->setup = $setup;
	}
	
	/**
	 * @return string $setup
	 */
	public function getSetup()
	{
		return $this->setup;
	}
	
	/**
	 * @param string $easeOfUse
	 */
	public function setEaseOfUse($easeOfUse)
	{
		$this->easeOfUse = $easeOfUse;
	}
	
	/**
	 * @return string $easeOfUse
	 */
	public function getEaseOfUse()
	{
		return $this->easeOfUse;
	}
	
	/**
	 * @param string $performance
	 */
	public function setPerformance($performance)
	{
		$this->performance = $performance;
	}
	
	/**
	 * @return string $performance
	 */
	public function getPerformance()
	{
		return $this->performance;
	}
	
	/**
	 * @param string $comment
	 */
	public function setComment($comment)
	{
		$this->comment = $comment;
	}
	
	/**
	 * @return string $comment
	 */
	public function getComment()
	{
		return $this->comment;
	}
	
}
		
