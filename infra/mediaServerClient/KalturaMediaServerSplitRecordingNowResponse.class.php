<?php


class KalturaMediaServerSplitRecordingNowResponse extends SoapObject
{				
	public function getType()
	{
		return 'http://services.api.server.media.kaltura.com/:SplitRecordingNowResponse';
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
































