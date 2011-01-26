<?php


class ComcastEncodingProfile extends ComcastStatusObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfEncodingProfileField';
			case 'audioBitrateMode':
				return 'ComcastBitrateMode';
			case 'contentType':
				return 'ComcastContentType';
			case 'encodingProvider':
				return 'ComcastEncodingProvider';
			case 'format':
				return 'ComcastFormat';
			case 'hinting':
				return 'ComcastHinting';
			case 'videoBitrateMode':
				return 'ComcastBitrateMode';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastArrayOfEncodingProfileField
	 **/
	public $template;
				
	/**
	 * @var long
	 **/
	public $audioBitrate;
				
	/**
	 * @var ComcastBitrateMode
	 **/
	public $audioBitrateMode;
				
	/**
	 * @var int
	 **/
	public $audioBitsPerSample;
				
	/**
	 * @var int
	 **/
	public $audioChannels;
				
	/**
	 * @var long
	 **/
	public $audioCodecID;
				
	/**
	 * @var string
	 **/
	public $audioCodecTitle;
				
	/**
	 * @var long
	 **/
	public $audioSampleRate;
				
	/**
	 * @var boolean
	 **/
	public $availableOnSharedContent;
				
	/**
	 * @var ComcastContentType
	 **/
	public $contentType;
				
	/**
	 * @var boolean
	 **/
	public $correctForRepeatedFrames;
				
	/**
	 * @var boolean
	 **/
	public $disabled;
				
	/**
	 * @var ComcastEncodingProvider
	 **/
	public $encodingProvider;
				
	/**
	 * @var string
	 **/
	public $externalEncodingProfileID;
				
	/**
	 * @var string
	 **/
	public $fileExtension;
				
	/**
	 * @var ComcastFormat
	 **/
	public $format;
				
	/**
	 * @var ComcastHinting
	 **/
	public $hinting;
				
	/**
	 * @var long
	 **/
	public $imageHeight;
				
	/**
	 * @var float
	 **/
	public $imageQuality;
				
	/**
	 * @var long
	 **/
	public $imageWidth;
				
	/**
	 * @var boolean
	 **/
	public $includeInFeeds;
				
	/**
	 * @var long
	 **/
	public $maximumAudioBitrate;
				
	/**
	 * @var long
	 **/
	public $maximumAudioBuffering;
				
	/**
	 * @var int
	 **/
	public $maximumPacketDuration;
				
	/**
	 * @var int
	 **/
	public $maximumPacketSize;
				
	/**
	 * @var long
	 **/
	public $maximumVideoBitrate;
				
	/**
	 * @var long
	 **/
	public $maximumVideoBuffering;
				
	/**
	 * @var boolean
	 **/
	public $optimizeForEncodingSpeed;
				
	/**
	 * @var boolean
	 **/
	public $optimizeForPortableDevices;
				
	/**
	 * @var string
	 **/
	public $title;
				
	/**
	 * @var long
	 **/
	public $totalBitrate;
				
	/**
	 * @var long
	 **/
	public $videoBitrate;
				
	/**
	 * @var ComcastBitrateMode
	 **/
	public $videoBitrateMode;
				
	/**
	 * @var long
	 **/
	public $videoCodecID;
				
	/**
	 * @var string
	 **/
	public $videoCodecTitle;
				
	/**
	 * @var float
	 **/
	public $videoFrameRate;
				
	/**
	 * @var long
	 **/
	public $videoKeyFrameInterval;
				
}


