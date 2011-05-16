<?php


class ComcastCodec extends SoapObject
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/value/:Codec';
	}
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'contentType':
				return 'ComcastContentType';
			case 'encodingProvider':
				return 'ComcastEncodingProvider';
			case 'bitrateModes':
				return 'ComcastArrayOfBitrateMode';
			case 'possibleTargetForats':
				return 'ComcastArrayOfFormat';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastContentType
	 **/
	public $contentType;
				
	/**
	 * @var ComcastEncodingProvider
	 **/
	public $encodingProvider;
				
	/**
	 * @var long
	 **/
	public $id;
				
	/**
	 * @var ComcastArrayOfBitrateMode
	 **/
	public $bitrateModes;
				
	/**
	 * @var string
	 **/
	public $title;
				
	/**
	 * @var ComcastArrayOfFormat
	 **/
	public $possibleTargetForats;
				
}


