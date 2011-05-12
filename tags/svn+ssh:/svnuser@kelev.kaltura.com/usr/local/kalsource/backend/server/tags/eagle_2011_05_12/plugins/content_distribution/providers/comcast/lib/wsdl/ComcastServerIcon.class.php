<?php


class ComcastServerIcon extends SoapObject
{				
	const _ABACAST = 'Abacast';
					
	const _ACTIVATE = 'Activate';
					
	const _AKAMAI = 'Akamai';
					
	const _AMAZON_S3 = 'Amazon S3';
					
	const _ARCOSTREAM = 'ArcoStream';
					
	const _ASPERA = 'Aspera';
					
	const _BITTORRENT = 'BitTorrent';
					
	const _CDNETWORKS = 'CDNetworks';
					
	const _CISCO = 'Cisco';
					
	const _COMCAST = 'Comcast';
					
	const _CUSTOM = 'Custom';
					
	const _DIGITAL_FOUNTAIN = 'Digital Fountain';
					
	const _EDGECAST = 'EdgeCast';
					
	const _EXODUS = 'Exodus';
					
	const _EXTERNAL = 'External';
					
	const _FLASH_VIDEO = 'Flash Video';
					
	const _HIGHWINDS = 'Highwinds';
					
	const _LEVEL_3 = 'Level 3';
					
	const _LIMELIGHT_NETWORKS = 'Limelight Networks';
					
	const _MEDIA_ON_DEMAND = 'Media on Demand';
					
	const _MIRROR_IMAGE = 'Mirror Image';
					
	const _MOVE_NETWORKS = 'Move Networks';
					
	const _QUICKTIME = 'QuickTime';
					
	const _QWEST = 'Qwest';
					
	const _RBN = 'RBN';
					
	const _REALMEDIA = 'RealMedia';
					
	const _SAVVIS = 'Savvis';
					
	const _SPEEDERA = 'Speedera';
					
	const _THEPLATFORM = 'thePlatform';
					
	const _THUMBNAILS = 'Thumbnails';
					
	const _VELOCIX = 'Velocix';
					
	const _VERIVUE = 'Verivue';
					
	const _VITALSTREAM = 'VitalStream';
					
	const _WINDOWS_MEDIA = 'Windows Media';
					
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


