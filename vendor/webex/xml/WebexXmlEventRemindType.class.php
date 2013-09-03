<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventRemindType extends WebexXmlRequestType
{
	/**
	 *
	 * @var integer
	 */
	protected $minutesAhead;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'minutesAhead':
				return 'integer';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'minutesAhead',
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
		return 'remindType';
	}
	
	/**
	 * @param integer $minutesAhead
	 */
	public function setMinutesAhead($minutesAhead)
	{
		$this->minutesAhead = $minutesAhead;
	}
	
	/**
	 * @return integer $minutesAhead
	 */
	public function getMinutesAhead()
	{
		return $this->minutesAhead;
	}
	
}
		
