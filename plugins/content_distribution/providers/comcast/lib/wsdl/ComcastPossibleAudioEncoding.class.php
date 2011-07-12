<?php


class ComcastPossibleAudioEncoding extends SoapObject
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/value/:PossibleAudioEncoding';
	}
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'bitrateMode':
				return 'ComcastBitrateMode';
			case 'encodingProvider':
				return 'ComcastEncodingProvider';
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
	 * @var ComcastBitrateMode
	 **/
	public $bitrateMode;
				
	/**
	 * @var int
	 **/
	public $bitsPerSample;
				
	/**
	 * @var int
	 **/
	public $channels;
				
	/**
	 * @var long
	 **/
	public $codecID;
				
	/**
	 * @var ComcastEncodingProvider
	 **/
	public $encodingProvider;
				
	/**
	 * @var long
	 **/
	public $sampleRate;
				
}


