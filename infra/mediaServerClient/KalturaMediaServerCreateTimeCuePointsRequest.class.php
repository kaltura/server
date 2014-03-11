<?php


class KalturaMediaServerCreateTimeCuePointsRequest extends SoapObject
{				
	public function getType()
	{
		return 'http://services.api.server.media.kaltura.com/:CreateTimeCuePointsRequest';
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
	 * @var string
	 **/
	public $liveEntryId;
	
	/**
	 *
	 * @var int
	 **/
	public $interval;
	
	/**
	 *
	 * @var int
	 **/
	public $duration;
	
}


