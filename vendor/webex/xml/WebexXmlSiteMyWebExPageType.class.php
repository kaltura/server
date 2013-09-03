<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteMyWebExPageType extends WebexXmlRequestType
{
	const _MY_MEETINGS = 'My Meetings';
					
	const _MY_COMPUTERS = 'My Computers';
					
	const _MY_FILES:FOLDERS = 'My Files:Folders';
					
	const _MY_FILES:TRAINING_RECORDINGS = 'My Files:Training Recordings';
					
	const _MY_FILES:RECORDED_EVENTS = 'My Files:Recorded Events';
					
	const _MY_REPORTS = 'My Reports';
					
	const _MY_PROFILE = 'My Profile';
					
	const _MY_CONTACTS = 'My Contacts';
					
	const _MY_WORKSPACES = 'My Workspaces';
					
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
		return 'myWebExPageType';
	}
	
}
		
