<?php


class KalturaMediaServerSplitRecordingNowRequest extends SoapObject
{				
	public function getType()
	{
		return 'http://services.api.server.media.kaltura.com/:SplitRecordingNowRequest';
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
	
}


