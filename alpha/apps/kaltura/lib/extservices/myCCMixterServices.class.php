<?php
class myCCMixterServices extends myBaseMediaSource implements IMediaSource
{
	private static $NEED_MEDIA_INFO = "0";
	
	protected $supported_media_types = self::SUPPORT_MEDIA_TYPE_AUDIO; // bitwise 
	protected $source_name = "CCMixter";
//	protected $auth_method = array { self::AUTH_METHOD_NONE;
	protected $search_in_user = false; 
	protected $logo = "http://ccmixter.org/mixter-files/ccdj.gif";
	protected $id = entry::ENTRY_MEDIA_SOURCE_CCMIXTER;

	public function getMediaInfo( $media_type ,$objectId) 
	{
		if( $media_type == entry::ENTRY_MEDIA_TYPE_AUDIO )
		{
			return "";
		}
		else
		{
			// this provider does not supply media type $media_type
		}		
		return null;		
	}
	
	
	public function searchMedia( $media_type , $searchText, $page, $pageSize, $authData = null, $extraData = null)
	{
		if( $media_type == entry::ENTRY_MEDIA_TYPE_AUDIO )
		{
			return self::searchSounds($searchText, $page, $pageSize, $authData ); 		
		}
		else
		{
			// this provider does not supply media type $media_type
		}
	}
	
	public function getAuthData( $kuserId, $userName, $password, $token)
	{
		return array('status' => 'ok', 'message' => '', 'authData' => $userName);
//		return ""; // empty value
	}	
		
	
	private static function searchSounds($searchText, $page, $pageSize)
	{
		$url = "http://ccmixter.org/media/api/search?query=".urlencode($searchText);
		
		return self::sendRequest($url);
	}
		
	private static function getAttributeValue($node, $attrName)
	{
		if ($node->attributes)
		{
			foreach($node->attributes as $i)
			{
				if	($i->name == $attrName)
					return $i->value; 
			}
		}
		
		return "";
	}
	
	private static function getXmlNodeValue($element, $nodeName, $attrName = null)
	{
		$titleNodes = $element->getElementsByTagName($nodeName);
		
		if ($titleNodes->length)
		{
			$node = $titleNodes->item(0);
			return $attrName ? self::getAttributeValue($node, $attrName) : $node->nodeValue;
		}
		return "";
	}

	public static function sendRequest($url)
	{
		$images = array();
		$message = '';
		
		$xmlStr = kFile::downloadUrlToString($url);

		$doc = new DOMDocument();
		$doc->loadXML($xmlStr);

		$xpath = new DOMXPath($doc);

		$sounds = $xpath->query("//item");
		$objects = array();

		//get only "by"
		//<cc:license>http://creativecommons.org/licenses/by/2.5/</cc:license>
		foreach ($sounds as $sound) {
			$objects[] = array(
				'url' => self::getXmlNodeValue($sound, "enclosure", "url"),
				'title' => self::getXmlNodeValue($sound, "title")." by ".self::getXmlNodeValue($sound, "creator"),
				'id' => self::getXmlNodeValue($sound, "guid") ,
				'thumb' => self::AUDIO_THUMB_URL );

		}

		$status = 'ok';
		/*
		{
			$status = 'error';
			$message = $rsp_obj['code'].' : '.$rsp_obj['message'];
		}
		*/
		
		return array('status' => $status, 'message' => $message, 'objects' => $objects , "needMediaInfo" => self::$NEED_MEDIA_INFO);
	}


}
?>