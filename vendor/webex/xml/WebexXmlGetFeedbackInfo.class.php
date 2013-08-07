<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlAttFeedbackSessionType.class.php');

class WebexXmlGetFeedbackInfo extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlAttFeedbackSessionType>
	 */
	protected $session;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'session':
				return 'WebexXmlArray<WebexXmlAttFeedbackSessionType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $session
	 */
	public function getSession()
	{
		return $this->session;
	}
	
}
		
