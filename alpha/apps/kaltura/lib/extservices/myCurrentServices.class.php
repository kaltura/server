<?php
class myCurrentServices extends myBaseMediaSource implements IMediaSource
{
	protected $supported_media_types = 5; //self::SUPPORT_MEDIA_TYPE_VIDEO + (int)self::SUPPORT_MEDIA_TYPE_IMAGE;  
	protected $source_name = "Current";
//	protected $auth_method = self::AUTH_METHOD_NONE;
	protected $search_in_user = false; 
	protected $logo = "http://www.kaltura.com/images/wizard/logo_current.gif";
	protected $id = entry::ENTRY_MEDIA_SOURCE_CURRENT;
	
	private static $NEED_MEDIA_INFO = "1";
	
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
			return $this->searchVideos(  $searchText, $page, $pageSize, $authData );
		}
		elseif( $media_type == entry::ENTRY_MEDIA_TYPE_IMAGE )
		{
			return $this->searchImages(  $searchText, $page, $pageSize, $authData );
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
	
// --------------------------------------------------------------------------
	
	private function search($searchType, $searchText, $page, $pageSize)
	{
		$searchText = str_replace(' ', '+', $searchText);
		$url = "http://current.com/search/search.do?indexName=barca-search&renderer=jsp&sortBy=&q=$searchText+ASSET_TYPE:$searchType+ITEM_TYPE:items&start=".($pageSize * ($page - 1))."&len=$pageSize";

		return $this->hitUrl( $url );
/*		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);

		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_NOBODY, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

		$content = curl_exec($ch);
		curl_close($ch);

		return $content;
*/
	}

	private static function parse($searchImages, $content)
	{
		$images = array();
		$message = '';
				
		// analyze search page thumbnails
		if (strpos($content, '<ul class="contentItemList">'))
		{
			if (preg_match_all('/<li class="contentItem" id="contentItem(.*?)<dd>(.*?)<\/dd>(.*?)<\/li>/ms', $content, $entryContainers))
			{
				while($iter = each($entryContainers[1]))
				{
					$iter2 = each($entryContainers[2]);
					
					if (!preg_match('/<a href="(.*?)"/ms', $iter[1], $matches))
						continue;
						
					$objectId = $matches[1];
						
					if (!preg_match('/<img.*?src="(.*?)"/ms', $iter[1], $matches))
						continue;
						
					$thumbnail = $matches[1];
							
					if (!preg_match('/<div class="contentItemBody".*?<a.*?>(.*?)<\/a>/ms', $iter[1], $matches))
						continue;
						
					$title = $matches[1];
								
					if (!preg_match('/<li class="username".*?<a.*?>(.*?)<\/a>/ms', $iter2[1], $matches))
						continue;
						
					$credit = $matches[1];
						
					if ($searchImages)
						$images[] = array('thumb' => $thumbnail, 'title' => $title, 'id' => $objectId, 'credit' => $credit);
					else
						$images[] = array('thumb' => $thumbnail, 'title' => $title, 'id' => $objectId, 'credit' => $credit);
				}
			}
			
			$status = "ok";
		}
		else
		{
			$status = "error";
		}
		
		return array('status' => $status, 'message' => $message, 'objects' => $images);
	}
	

	
	private function searchImages($searchText, $page, $pageSize)
	{
		$result = $this->search("I", $searchText, $page, $pageSize);
		
		return self::parse(true, $result);
	}
	
	private function searchVideos($searchText, $page, $pageSize)
	{
		$result = $this->search("V", $searchText, $page, $pageSize);
		
		return self::parse(false, $result);
	}

	private static function getObjectInfo($objectId)
	{
		$source_link = "http://current.com$objectId";
		$htmlPage = kFile::downloadUrlToString($source_link, 1);
		
		$status = 'error';
		$message = '';
		$objectInfo = null;
		
		//video might be an embed from youtube
		//<a target="_blank" title="http://www.youtube.com/watch?v=0XxFjTdHYcA" href="http://www.youtube.com/watch?v=0XxFjTdHYcA">http://www.youtube.com/watch?v=0XxFjTdHYcA</a>
		
		if (preg_match('/<div id="itemSourceLink">(.*)<\/div>/ms', $htmlPage, $matches))
		{
			if (preg_match('/<a.*title="http:\/\/www\.youtube\.com\/watch\?v=(.*?)"/ms', $matches[1], $matches))
			{
				return myYouTubeServices::getObjectInfo($matches[1]);
			}
		}
		
		//so.addVariable('imgPath', 'http://i.current.com/images/studio/asset/2007/05/31/36576944_392115194_400x300.jpg');
		//so.addVariable('vidPath', 'http://v.current.com/vids/2007/05/31/20070531_36579189_36576944_3.flv');
		if (preg_match("/'vidPath', '(.*?)'/ms", $htmlPage, $matches))
		{
			$url = $matches[1];
			if (preg_match("/'imgPath', '(.*?)'/ms", $htmlPage, $matches))
			{
				$thumb = $matches[1];
			
				$objectInfo = array('id' => $objectId, 'url' => $url, //'title' => $title,
					'thumb' => $thumb, 'source_link' => $source_link);
					//'license' => '', 'credit' => '');
	
				$status = 'ok';
			}
		}
		
		return array('status' => $status, 'message' => $message, 'objectInfo' => $objectInfo , "needMediaInfo" => self::$NEED_MEDIA_INFO );
	}
	
	

		
}
?>