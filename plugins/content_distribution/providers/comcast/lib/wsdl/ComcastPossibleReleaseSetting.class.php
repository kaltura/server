<?php


class ComcastPossibleReleaseSetting extends SoapObject
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/value/:PossibleReleaseSetting';
	}
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'contentType':
				return 'ComcastContentType';
			case 'delivery':
				return 'ComcastDelivery';
			case 'format':
				return 'ComcastFormat';
			case 'trueFormat':
				return 'ComcastFormat';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var long
	 **/
	public $bitrate;
				
	/**
	 * @var ComcastContentType
	 **/
	public $contentType;
				
	/**
	 * @var ComcastDelivery
	 **/
	public $delivery;
				
	/**
	 * @var ComcastFormat
	 **/
	public $format;
				
	/**
	 * @var ComcastFormat
	 **/
	public $trueFormat;
				
	/**
	 * @var long
	 **/
	public $size;
				
	/**
	 * @var string
	 **/
	public $assetType;
				
	/**
	 * @var long
	 **/
	public $assetTypeID;
				
	/**
	 * @var long
	 **/
	public $encodingProfileID;
				
	/**
	 * @var string
	 **/
	public $encodingProfileTitle;
				
}


