<?php


class ComcastDelivery extends SoapObject
{				
	const _ALL = 'All';
					
	const _DOWNLOADANDSTREAMING = 'DownloadAndStreaming';
					
	const _DOWNLOADANDPUSH = 'DownloadAndPush';
					
	const _STREAMINGANDPUSH = 'StreamingAndPush';
					
	const _DOWNLOAD = 'Download';
					
	const _STREAMING = 'Streaming';
					
	const _PUSH = 'Push';
					
	const _NONE = 'None';
					
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
				
}


