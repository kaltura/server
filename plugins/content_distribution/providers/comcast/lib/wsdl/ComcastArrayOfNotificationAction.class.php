<?php


class ComcastArrayOfNotificationAction extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastNotificationAction");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


