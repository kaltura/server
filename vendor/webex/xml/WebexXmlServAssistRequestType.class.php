<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlServAssistRequestType extends WebexXmlRequestType
{
	const _NONE = 'None';
					
	const _DRY_RUN = 'Dry Run';
					
	const _CONSULT = 'Consult';
					
	const _LIVE_EVENT_SUPPORT = 'Live Event Support';
					
	const _AUDIO_STREAMING = 'Audio Streaming';
					
	const _VIDEO = 'Video';
					
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
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
		return 'assistRequestType';
	}
	
}
		
