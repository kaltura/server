<?php
require_once ("extservices/IMediaSource.class.php");
class myMediaSourceFactory
{
	/*
	 const ENTRY_MEDIA_SOURCE_FILE = 1;
	 const ENTRY_MEDIA_SOURCE_WEBCAM = 2;
	 const ENTRY_MEDIA_SOURCE_FLICKR = 3;
	 const ENTRY_MEDIA_SOURCE_YOUTUBE = 4;
	 const ENTRY_MEDIA_SOURCE_URL = 5;
	 const ENTRY_MEDIA_SOURCE_TEXT = 6;
	 const ENTRY_MEDIA_SOURCE_MYSPACE = 7;
	 const ENTRY_MEDIA_SOURCE_PHOTOBUCKET = 8;
	 const ENTRY_MEDIA_SOURCE_JAMENDO = 9;
	 const ENTRY_MEDIA_SOURCE_CCMIXTER = 10;
	 const ENTRY_MEDIA_SOURCE_NYPL = 11;
	 const ENTRY_MEDIA_SOURCE_CURRENT = 12;

	 */

	/**
	 * @param int $media_source
	 * @return myBaseMediaSource
	 */
	public static function getMediaSource ( $media_source )
	{
		switch ( $media_source )
		{
			case entry::ENTRY_MEDIA_SOURCE_FILE:
				$result =  new myFileUploadService();
				break;
			case entry::ENTRY_MEDIA_SOURCE_WEBCAM:
				$result = new myWebcamRecordService();
				break;
			case entry::ENTRY_MEDIA_SOURCE_FLICKR:
				$result =  new myFlickrServices();
				break;
			case entry::ENTRY_MEDIA_SOURCE_YOUTUBE:
				$result =  new myYouTubeServices();
				break;
			case entry::ENTRY_MEDIA_SOURCE_URL:
				$result =  new myUrlImportServices();
				break;
			case entry::ENTRY_MEDIA_SOURCE_MYSPACE:
				$result =  new myMySpaceServices();
				break;
			case entry::ENTRY_MEDIA_SOURCE_PHOTOBUCKET:
				$result =  new myPhotobucketServices();
				break;
			case entry::ENTRY_MEDIA_SOURCE_JAMENDO:
				$result =  new myJamendoServices();
				break;
			case entry::ENTRY_MEDIA_SOURCE_CCMIXTER:
				$result =  new myCCMixterServices();
				break;
			case entry::ENTRY_MEDIA_SOURCE_NYPL:
				$result =  new myNYPLServices();
				break;
				/*			case entry::ENTRY_MEDIA_SOURCE_CURRENT:
				 $result =  new myCurrentServices();
				 break; */
			case entry::ENTRY_MEDIA_SOURCE_KALTURA:
				$result =  new myKalturaServices();
				break;
			case entry::ENTRY_MEDIA_SOURCE_KALTURA_QA:
				$result =  new myKalturaQaServices();
				break;
			case entry::ENTRY_MEDIA_SOURCE_KALTURA_USER_CLIPS:
				$result =  new myKalturaUserClipsServices();
				break;
			case entry::ENTRY_MEDIA_SOURCE_MEDIA_COMMONS:
				$result =  new myMediaCommonsServices();
				break;
			case entry::ENTRY_MEDIA_SOURCE_KALTURA_PARTNER:
				$result =  new myKalturaPartnerServices();
				break;
			case entry::ENTRY_MEDIA_SOURCE_KALTURA_KSHOW:
				$result =  new myKalturaKshowServices();
				break;
			case entry::ENTRY_MEDIA_SOURCE_KALTURA_PARTNER_KSHOW:
				$result =  new myKalturaPartnerKshowServices();
				break;
			case entry::ENTRY_MEDIA_SOURCE_ARCHIVE_ORG:
				$result =  new myArchiveOrgServices();
				break;
			case entry::ENTRY_MEDIA_SOURCE_METACAFE:
				$result =  new myMetacafeServices();
				break;
			case entry::ENTRY_MEDIA_SOURCE_SEARCH_PROXY:
				$result = new mySearchProxyServices();
				break;
			case entry::ENTRY_MEDIA_SOURCE_PARTNER_SPECIFIC:
				$pid = kCurrentContext::$ks_partner_id;
				$partner = PartnerPeer::retrieveByPK($pid);
				$specServices = $partner->getPartnerSpecificServices();
				if ($specServices) {
					if (class_exists($specServices)) {
						$result = new $specServices();
					}
					else {
						// class not found
						throw new Exception("Cannot find partner specific services class of name [$specServices] defined for partner id [$pid]");
					}
				}
				else {
					// no partner specific services defined for current partner id
					throw new Exception("Partner id [$pid] does not have a any defined partner specific services");
				}
				break;
			default:
				/* OLD CODE FROM LIRON - left here for the comments
				// TODO - once we have am extension system - hook in here !
				// $result = kExtensionMgr::extend ( "mediaSourceFactory:getMediaSource" ,  $media_source );
				if ( $media_source == 100 ) // hard coded for Stroome
				{
					//class_exists('ext'.$pid.'Services')
					// depending on the ks partner - choose the service. This map will have to be registered somewhere in the config / DB
					// for now - it will be redirected to stroome and stroome will validate the ks's partner
					$result = new extStroomeServices(); //STROOME
					return $result;
				}
				*/
				throw new Exception ("Cannot create media source of type [$media_source]");
				$result = new myKalturaServices();
		}

		return $result;
	}

	// returns a tupple with media source and object data
	public static function getMediaSourceAndObjectDataByUrl ( $media_type , $url )
	{
		$result = array ();
		$media_source = null;
		if (preg_match("/youtube\.[a-zA-Z0-9\.]+\/watch\?v=(.*)/", $url, $objectId))
		{
			$media_source = self::getMediaSource ( entry::ENTRY_MEDIA_SOURCE_YOUTUBE );
			$obj_id = $objectId[1];
		}
		//http://www.flickr.com/photos/k_soggie/338990574/
		elseif (preg_match("/http:\/\/www.flickr.com\/photos\/.*?\/(\d+)/", $url, $objectId))
		{
			$media_source = self::getMediaSource ( entry::ENTRY_MEDIA_SOURCE_FLICKR );
			$obj_id = $objectId[1];
		}
		else
		{
			// do not this URL provider
				
		}

		if ( $media_source == null )
		{
			return null;
		}

		$result[] = $media_source;
		$result[] = $obj_id;

		return $result; // $media_source , $obj_id
	}

	public static function getAllMediaSourceProvidersIds ()
	{
		return array (
		entry::ENTRY_MEDIA_SOURCE_FILE ,
		entry::ENTRY_MEDIA_SOURCE_WEBCAM ,
		entry::ENTRY_MEDIA_SOURCE_KALTURA ,
		entry::ENTRY_MEDIA_SOURCE_KALTURA_PARTNER ,
		entry::ENTRY_MEDIA_SOURCE_KALTURA_KSHOW,
		entry::ENTRY_MEDIA_SOURCE_KALTURA_PARTNER_KSHOW ,
		//				entry::ENTRY_MEDIA_SOURCE_KALTURA_USER_CLIPS ,
		entry::ENTRY_MEDIA_SOURCE_FLICKR ,
		entry::ENTRY_MEDIA_SOURCE_PHOTOBUCKET ,
		entry::ENTRY_MEDIA_SOURCE_JAMENDO ,
		entry::ENTRY_MEDIA_SOURCE_CCMIXTER ,
		entry::ENTRY_MEDIA_SOURCE_NYPL ,
		entry::ENTRY_MEDIA_SOURCE_YOUTUBE ,
		entry::ENTRY_MEDIA_SOURCE_MYSPACE ,
		entry::ENTRY_MEDIA_SOURCE_MEDIA_COMMONS ,
		entry::ENTRY_MEDIA_SOURCE_URL ,
		entry::ENTRY_MEDIA_SOURCE_ARCHIVE_ORG ,
		entry::ENTRY_MEDIA_SOURCE_METACAFE,
		entry::ENTRY_MEDIA_SOURCE_SEARCH_PROXY,
		//				entry::ENTRY_MEDIA_SOURCE_CURRENT ,
		);

	}
}
?>