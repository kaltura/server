<?php
class myUrlImportServices extends myBaseMediaSource implements IMediaSource
{
	// TODO - once the fetch video is fixed - return $supported_media_types to be 5
	protected $supported_media_types = 5; //self::SUPPORT_MEDIA_TYPE_VIDEO + (int)self::SUPPORT_MEDIA_TYPE_IMAGE;  
	protected $source_name = "URL";
//	protected $auth_method = self::AUTH_METHOD_NONE;
	protected $search_in_user = false; 
	protected $logo = "http://www.kaltura.com/images/wizard/logo_url.gif";
	protected $id = entry::ENTRY_MEDIA_SOURCE_URL;
	
	private static $NEED_MEDIA_INFO = "0";
	
	public function getMediaInfo( $media_type ,$objectId) 
	{
		if( $media_type == entry::ENTRY_MEDIA_TYPE_VIDEO || $media_type == entry::ENTRY_MEDIA_TYPE_IMAGE )
		{
			return self::getObjectInfo( $objectId );
		}
		else
		{
			// this provider does not supply media type $media_type
		}		
		return null;		
	}	
	
	
	public function searchMedia( $media_type , $searchText, $page, $pageSize, $authData = null, $extraData = null)
	{
		if( $media_type == entry::ENTRY_MEDIA_TYPE_VIDEO )
		{
			$result = null;
			if (preg_match("/http:\/\/current.com\/items\/(.*)/", $searchText, $objectId))
			{
				//http://current.com/items/87617121_ecospot_runner_up_perry_the_cockroach
				$media_source = myMediaSourceFactory::getMediaSource ( entry::ENTRY_MEDIA_SOURCE_CURRENT );
				$result = $media_source->getMediaInfo( $media_type ,"/items/".$objectId[1]);
			}
			if (preg_match("/youtube\.[a-zA-Z0-9\.]+\/watch\?v=(.*)/", $searchText, $objectId))
			{
				$media_source = myMediaSourceFactory::getMediaSource ( entry::ENTRY_MEDIA_SOURCE_YOUTUBE );
				$result = $media_source->getMediaInfo( $media_type, $objectId[1]);
			}
			else
			{
				$headers = kFile::downloadUrlToString($searchText, 2);

				if (!preg_match("/Content-Type:\s*video\/(.*)\r/i", $headers))
				{
					$content = kFile::downloadUrlToString($searchText, 1);
					$result = self::parseVideos($searchText, $page, $pageSize);
				
					if ($result)
						return $result;
				}
			}
			
			if ($result)
			{
				$objects = array();
				if ($result['objectInfo'])
					$objects[] = $result['objectInfo'];
				return array('status' => $result['status'], 'message' => $result['message'], 'objects' => $objects);
			}
			else
			{
				$message = 'No videos found';
				$status = 'ok';
		
				return array('status' => $status, 'message' => $message, 'objects' => array() );
			}
		}
		elseif( $media_type == entry::ENTRY_MEDIA_TYPE_IMAGE )
		{
			return self::searchImages(  $searchText, $page, $pageSize, $authData );
		}		
		else
		{
			// this provider does not supply media type $media_type
		}		
		return null;		
		
	}
		
	public function getAuthData( $kuserId, $userName, $password, $token)
	{
		return ""; // empty value
	}	

// ----------------------------------------------------------------------------
	
	private static function getFilenameFromUrl($url)
	{
		$path =  parse_url($url, PHP_URL_PATH);
		$title = pathinfo($path, PATHINFO_FILENAME);
		
		return $title;
	}
	
	private static function search($searchType, $searchText, $page, $pageSize)
	{
	}

	private static function parseImages($content, $baseUrl, $page, $pageSize)
	{
		$baseHost = parse_url($baseUrl, PHP_URL_SCHEME) . "://". parse_url($baseUrl, PHP_URL_HOST) ."/";
		$basePath = parse_url($baseUrl, PHP_URL_PATH);
		if ($basePath)
			$basePath .= "/";
		
		
		$images = array();
		$message = '';
		$cache = array();
		
		$offset = ($page - 1) * $pageSize;

		if (preg_match_all('/<img.*?src=[\s\'"]*([^\s\'>"]*)[\s\'">]*?/msi', $content, $imgUrls))
		{
			foreach($imgUrls[1] as $imgUrl)
			{
				if (in_array($imgUrl, $cache))
					continue;
					
				$cache[] = $imgUrl;
				
				if ($offset)
				{
					$offset--;
					continue;
				}
				
				$url = "";
				if (parse_url($imgUrl, PHP_URL_SCHEME))
					$url = $imgUrl;
				else
				{
					$url = $baseHost;
					
					if ($imgUrl[0] != "/")
						$url .= $basePath;
						
					$url .= $imgUrl;
				}
				
				$title = self::getFilenameFromUrl($url);
				
				$images[] = array('thumb' => $url, 'title' => $title, 'url' => $url, 'id' => $url);
				
				if (count($images) == $pageSize)
					break;
			}
		}
		
		$status = "ok";
		
		return array('status' => $status, 'message' => $message, 'objects' => $images);
	}
	
	private static function parseVideos($url, $page, $pageSize)
	{
		$videos = array();
		$status = 'ok';
		$message = '';
		$cache = array();
		
		$media_source = myMediaSourceFactory::getMediaSource ( entry::ENTRY_MEDIA_SOURCE_YOUTUBE );
		
		$offset = ($page - 1) * $pageSize;
		$objects = array();

		$content = kFile::downloadUrlToString($url, 1);
		if (preg_match_all("/http:\/\/www.youtube.com\/v\/([a-zA-Z0-9_\-]*)/msi", $content, $vidUrls))
		{
			foreach($vidUrls[1] as $vidUrl)
			{
				if (in_array($vidUrl, $cache))
					continue;
					
				$cache[] = $vidUrl;
				
				$result = $media_source->getMediaInfo( entry::ENTRY_MEDIA_TYPE_VIDEO, $vidUrl);
				if (!$result)
					continue;
					
				if ($offset)
				{
					$offset--;
					continue;
				}
				
				$objects[] = $result['objectInfo'];
				
				if (count($objects) == $pageSize)
					break;
			}
		}
		/*
		else if (preg_match_all("/http:\/\/v.wordpress.com\/([a-zA-Z0-9_\-]*)/msi", $content, $vidUrls))
		{
			foreach($vidUrls[1] as $vidUrl)
			{
				if (in_array($vidUrl, $cache))
					continue;
					
				$cache[] = $vidUrl;

				$url = "http://v.wordpress.com/videofile/".$vidUrl."/flv";

				$objectInfo = array('id' => $vidUrl, 'url' => $url, 'title' => '',
					'thumb' => '',
					'tags' => '',
					'license' => '', 'credit' => '');
				
				$result = $media_source->getMediaInfo( entry::ENTRY_MEDIA_TYPE_VIDEO, $vidUrl);
				if (!$result)
					continue;
					
				if ($offset)
				{
					$offset--;
					continue;
				}
				
				$objects[] = $objectInfo;
				
				if (count($objects) == $pageSize)
					break;
			}
		}
		*/
		
		return array('status' => $status, 'message' => $message, 'objects' => $objects);
	}
	

	
	private static function searchImages($searchText, $page, $pageSize)
	{
		if (!parse_url($searchText, PHP_URL_SCHEME))
			$searchText = "http://$searchText";
		
		$headers = kFile::downloadUrlToString($searchText, 2);
		
		$images = array();
		$message = "";
		if (preg_match("/Content-Type:\s*image\/(.*)\r/i", $headers, $matches))
		{
			if ($matches[1] == "gif" || $matches[1] == "jpeg" || $matches[1] == "png" || $matches[1] == "bmp")
			{
				$title = self::getFilenameFromUrl($searchText);
				
				$images[] = array('thumb' => $searchText, 'title' => $title, 'url' => $searchText, 'id' => $searchText);
			}
		}
		else if (preg_match("/Content-Type:\s*text\/html;?.*\r/i", $headers))
		{
			$content = kFile::downloadUrlToString($searchText, 1);
			return self::parseImages($content, $searchText, $page, $pageSize);
		}

		if(!count($images))
			$message = 'No Images found';

		$status = 'ok';
		
		return array('status' => $status, 'message' => $message, 'objects' => $images , "needMediaInfo" => self::$NEED_MEDIA_INFO);
	}
	
	private static function searchVideos($searchText, $page, $pageSize)
	{
	}

	private static function getObjectInfo($objectId)
	{
	}
	
	

		
}
?>