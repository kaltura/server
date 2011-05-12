<?php

class myYouTubeServices extends myBaseMediaSource implements IMediaSource
{
	protected $supported_media_types =  3;// self::SUPPORT_MEDIA_TYPE_VIDEO +self::SUPPORT_MEDIA_TYPE_AUDIO;  
	protected $source_name = "YouTube";
	protected $auth_method = array ( self::AUTH_METHOD_PUBLIC , self::AUTH_METHOD_USER );
	protected $search_in_user = false; 
	protected $logo = "http://www.kaltura.com/images/wizard/logo_youtube.png";
	protected $id = entry::ENTRY_MEDIA_SOURCE_YOUTUBE;
	
	private static $NEED_MEDIA_INFO = "1";
		
	const DEV_ID = 'aQ79OgbnZjE';

	public  function getMediaInfo( $media_type ,$objectId)
	{
		if( $media_type == entry::ENTRY_MEDIA_TYPE_VIDEO )
		{
			return self::getObjectInfo( $objectId );
		}
		else
		{
			// this provider does not supply media type $media_type
		}		
		return self::getObjectInfo( $objectId );
	}

	public  function searchMedia( $media_type , $searchText, $page, $pageSize, $authData = null, $extraData = null)
	{
		if( $media_type == entry::ENTRY_MEDIA_TYPE_VIDEO )
		{
			return self::searchVideos ($searchText, $page, $pageSize, $authData ); 		
		}
		elseif( $media_type == entry::ENTRY_MEDIA_TYPE_AUDIO )
		{
			// this provider does not supply media type $media_type
			return self::searchVideos ($searchText, $page, $pageSize, $authData ); 
		}
	}
		
	public  function getAuthData($kuserId, $userName, $password, $token)
	{
		return array('status' => 'ok', 'message' => '', 'authData' => $userName);
	}
	
	
	private static function getXmlNodeValue($element, $nodeName)
	{
		$titleNodes = $element->getElementsByTagName($nodeName);
		return $titleNodes->length ? $titleNodes->item(0)->nodeValue : "";
	}

	private static function getXPathResponse($url)
	{
		$xmlStr = kFile::downloadUrlToString($url);

		$doc = new DOMDocument();
		$doc->loadXML($xmlStr);
		
		$xpath = new DOMXPath($doc);
		
		return $xpath;
	}
	
	private static function sendRequest($url)
	{
		$images = array();
		$message = '';
		
		$xpath = self::getXPathResponse($url);

		$videos = $xpath->query("//video");
		
		$filter_adult_content = true;
		
		if ($filter_adult_content)
		{
			$ch = curl_init();
						
			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_USERAGENT, "curl/7.11.1");
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_NOBODY, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER , TRUE);
		}

		foreach ($videos as $video) {
			$objectId = self::getXmlNodeValue($video, "id");
			
			if ($filter_adult_content)
			{
				curl_setopt($ch, CURLOPT_URL, "http://www.youtube.com/watch?v=$objectId");
				$headers = curl_exec($ch);			
				if (preg_match('/Location: (.*?)\/verify_age/', $headers))
					continue;
			}
			
			$title = self::getXmlNodeValue($video, "title");
			$duration = self::getXmlNodeValue($video, "length_seconds");
			
			$images[] = array(
				'thumb' => self::getXmlNodeValue($video, "thumbnail_url"),
				'title' => self::getXmlNodeValue($video, "title"),
				'description' => "($duration secs) $title",
				'id' => $objectId);
		}
		
		if ($filter_adult_content)
		{
			curl_close($ch);
		}

		$status = 'ok';
		/*
		{
			$status = 'error';
			$message = $rsp_obj['code'].' : '.$rsp_obj['message'];
		}
		*/
		
		return array('status' => $status, 'message' => $message, 'objects' => $images, "needMediaInfo" => self::$NEED_MEDIA_INFO);
	}


	
	private static function searchVideos($searchText, $page, $pageSize, $authData = null)
	{
		if ($authData)
			$url = "http://www.youtube.com/api2_rest?method=youtube.videos.list_by_user&dev_id=".self::DEV_ID."&page=$page&per_page=$pageSize&user=".urlencode($authData);
		else
			$url = "http://www.youtube.com/api2_rest?method=youtube.videos.list_by_tag&dev_id=".self::DEV_ID."&page=$page&per_page=$pageSize&tag=".urlencode($searchText);
		
		return self::sendRequest($url);
	}
	
	private static function getObjectInfo($objectId)
	{
		$url = "http://www.youtube.com/api2_rest?method=youtube.videos.get_details&dev_id=".self::DEV_ID."&video_id=".urlencode($objectId);
		$xpath = self::getXPathResponse($url);
		
		$title = '';
		$tags = '';
		$credit = '';
		
		if ($xpath)
		{
			$videos = $xpath->query("//video_details");
			
			foreach($videos as $video)
			{
				$title = self::getXmlNodeValue($video, "title");
				$tags = implode(',', explode(' ', self::getXmlNodeValue($video, "tags")));
				$credit = self::getXmlNodeValue($video, "author");
			}
		}
		
		$source_link = "http://www.youtube.com/watch?v=".$objectId;
		$htmlPage = kFile::downloadUrlToString($source_link, 3);

		$status = 'error';
		$message = '';
		$objectInfo = null;
		
		if (preg_match('/Location: (.*?)\/verify_age/', $htmlPage))
		{
			$message = "Adult content, age verification required, Please choose another movie";
		}
			else if (preg_match('/swfArgs.*?\{.*?, "t":\s*"(.*?)"/s', $htmlPage, $timestampMatch) || 
			preg_match('/SWF_ARGS.*?\{.*?, "t":\s*"(.*?)"/s', $htmlPage, $timestampMatch) )
		{
			//var swfArgs = {"sourceid": "y", "video_id": "CoiFGva_JoY", "l": 64, "sk": "uKKGdPxW5MWYni0u_OXxkgU", "t": "OEgsToPDskKqwUniXcKcELZlXY7Smhdx", "hl": "en", "plid": "AAREdD-JLalob4scAAAAIIAYQAA", "sdetail": "p%3A"};
			$fmt_url = "";
			//"fmt_map": "35/640000/9/0/115,18/512000/9/0/115,34/0/9/0/115,5/0/7/0/0"
			if (preg_match('/swfArgs.*?\{.*?, "fmt_map":\s*"(.*?)"/s', $htmlPage, $fmt_map))
			{
				$fmt_map_array = explode(",", $fmt_map[1]);
				$fmt_details = explode("/", $fmt_map_array[0]);
				//print_r($fmt_map_array);
				//echo "fmt: ".$fmt_details[0]."\n";
				
				if ($fmt_details[0])
					$fmt_url = "&fmt=".$fmt_details[0];
			}
			elseif (preg_match('/SWF_ARGS.*?\{.*?, "fmt_map":\s*"(.*?)"/s', $htmlPage, $fmt_map))
			{
				//"fmt_map": "34%2F0%2F9%2F0%2F115%2C5%2F0%2F7%2F0%2F0"
				$encoded_fmt_map = urldecode( $fmt_map[1] );
				$fmt_map_array = explode(",", $encoded_fmt_map);
				$fmt_details = explode("/", $fmt_map_array[0]);
				//print_r($fmt_map_array);
				//echo "fmt: ".$fmt_details[0]."\n";
				
				if ($fmt_details[0])
					$fmt_url = "&fmt=".$fmt_details[0]	;			
			}
		
			//var swfArgs = {hl:'en',video_id:'F924-D-g5t8',l:'24',t:'OEgsToPDskL9BIntclUB-PPzMEpVQKo8',sk:'xXvbHpmFGQKgv-b9__DkgwC'};
			$tId = $timestampMatch[1];
			//$url = "http://youtube.com/get_video?video_id=".$objectId."&t=$tId$fmt_url";
			$url = requestUtils::getRequestHost().'/extservices/youtubeRedirect?itemId='.$objectId;
			
			$objectInfo = array('id' => $objectId, 'url' => $url, 'title' => $title,
				'thumb' => "http://img.youtube.com/vi/$objectId/2.jpg",
				'tags' => $tags,
				'ext' => "flv" ,
				'source_link' => $source_link,
				'license' => '', 'credit' => $credit ,
				'flash_playback_type' => 'video' );
			$status = 'ok';
		}
		
		return array('status' => $status, 'message' => $message, 'objectInfo' => $objectInfo);
	}
	

	
}
?>