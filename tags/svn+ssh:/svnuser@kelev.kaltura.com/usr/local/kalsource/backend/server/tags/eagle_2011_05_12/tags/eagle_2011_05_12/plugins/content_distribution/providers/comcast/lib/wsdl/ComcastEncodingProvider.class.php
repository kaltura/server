<?php


class ComcastEncodingProvider extends SoapObject
{				
	const _COMMANDLINE = 'CommandLine';
					
	const _DIGITALRAPIDS = 'DigitalRapids';
					
	const _FLASHACCESS = 'FlashAccess';
					
	const _FLASHDYNAMIC = 'FlashDynamic';
					
	const _FLIPFACTORY = 'FlipFactory';
					
	const _FLIPFACTORY5 = 'FlipFactory5';
					
	const _FLIPFACTORY6 = 'FlipFactory6';
					
	const _FLIXENGINE8 = 'FlixEngine8';
					
	const _IISTRANSFORMMANAGER = 'IISTransformManager';
					
	const _MOVENETWORKS = 'MoveNetworks';
					
	const _NONE = 'None';
					
	const _RADIANTGRID = 'RadiantGrid';
					
	const _RHOZET = 'Rhozet';
					
	const _SCENECAST = 'Scenecast';
					
	const _THEPLATFORM = 'thePlatform';
					
	const _WIDEVINE = 'Widevine';
					
	const _WM9 = 'WM9';
					
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


