<?php


class ComcastRequest extends ComcastBusinessObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfRequestField';
			case 'contentClass':
				return 'ComcastContentClass';
			case 'contentType':
				return 'ComcastContentType';
			case 'country':
				return 'ComcastCountry';
			case 'delivery':
				return 'ComcastDelivery';
			case 'format':
				return 'ComcastFormat';
			case 'language':
				return 'ComcastLanguage';
			case 'requestDayOfWeek':
				return 'ComcastDayOfWeek';
			case 'requestMonthOnly':
				return 'Comcastint';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastArrayOfRequestField
	 **/
	public $template;
				
	/**
	 * @var string
	 **/
	public $affiliate;
				
	/**
	 * @var string
	 **/
	public $assetType;
				
	/**
	 * @var string
	 **/
	public $author;
				
	/**
	 * @var long
	 **/
	public $bitrate;
				
	/**
	 * @var long
	 **/
	public $bitrateInKbps;
				
	/**
	 * @var string
	 **/
	public $browser;
				
	/**
	 * @var float
	 **/
	public $buffering;
				
	/**
	 * @var string
	 **/
	public $categories;
				
	/**
	 * @var ComcastContentClass
	 **/
	public $contentClass;
				
	/**
	 * @var long
	 **/
	public $contentID;
				
	/**
	 * @var long
	 **/
	public $contentIDForGroup;
				
	/**
	 * @var string
	 **/
	public $contentOwner;
				
	/**
	 * @var long
	 **/
	public $contentOwnerAccountID;
				
	/**
	 * @var ComcastContentType
	 **/
	public $contentType;
				
	/**
	 * @var ComcastCountry
	 **/
	public $country;
				
	/**
	 * @var ComcastDelivery
	 **/
	public $delivery;
				
	/**
	 * @var string
	 **/
	public $encodingProfile;
				
	/**
	 * @var ComcastFormat
	 **/
	public $format;
				
	/**
	 * @var string
	 **/
	public $inPlaylist;
				
	/**
	 * @var long
	 **/
	public $inPlaylistID;
				
	/**
	 * @var long
	 **/
	public $inPlaylistIDForGroup;
				
	/**
	 * @var ComcastLanguage
	 **/
	public $language;
				
	/**
	 * @var long
	 **/
	public $length;
				
	/**
	 * @var long
	 **/
	public $lengthPlayed;
				
	/**
	 * @var long
	 **/
	public $loadTime;
				
	/**
	 * @var string
	 **/
	public $network;
				
	/**
	 * @var long
	 **/
	public $networkServerID;
				
	/**
	 * @var string
	 **/
	public $operatingSystem;
				
	/**
	 * @var string
	 **/
	public $outlet;
				
	/**
	 * @var long
	 **/
	public $outletAccountID;
				
	/**
	 * @var float
	 **/
	public $played;
				
	/**
	 * @var string
	 **/
	public $player;
				
	/**
	 * @var string
	 **/
	public $portal;
				
	/**
	 * @var float
	 **/
	public $quality;
				
	/**
	 * @var string
	 **/
	public $rating;
				
	/**
	 * @var string
	 **/
	public $region;
				
	/**
	 * @var long
	 **/
	public $requestCount;
				
	/**
	 * @var dateTime
	 **/
	public $requestDate;
				
	/**
	 * @var dateTime
	 **/
	public $requestDateOnly;
				
	/**
	 * @var ComcastDayOfWeek
	 **/
	public $requestDayOfWeek;
				
	/**
	 * @var float
	 **/
	public $requestHour;
				
	/**
	 * @var dateTime
	 **/
	public $requestMonth;
				
	/**
	 * @var Comcastint
	 **/
	public $requestMonthOnly;
				
	/**
	 * @var long
	 **/
	public $size;
				
	/**
	 * @var string
	 **/
	public $title;
				
	/**
	 * @var long
	 **/
	public $trackingCount;
				
	/**
	 * @var string
	 **/
	public $userName;
				
}


