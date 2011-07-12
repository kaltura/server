<?php


class ComcastArrayOfNotificationAction extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/enum/:ArrayOfNotificationAction';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastNotificationAction");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


