<?php


class KalturaMediaServerCreateTimeCuePointsResponse extends SoapObject
{				
	public function getType()
	{
		return 'http://services.api.server.media.kaltura.com/:CreateTimeCuePointsResponse';
	}
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}

	
	/**
	 *
	 * @var boolean
	 **/
	public $return;
	
}
































