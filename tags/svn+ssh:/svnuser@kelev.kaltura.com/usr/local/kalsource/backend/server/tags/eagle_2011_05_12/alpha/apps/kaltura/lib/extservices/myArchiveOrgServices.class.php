<?php
class myArchiveOrgServices extends myBaseMediaSource implements IMediaSource
{
	protected $supported_media_types =  self::SUPPORT_MEDIA_TYPE_VIDEO ;  
	protected $source_name = "Archive.org";
	protected $auth_method = array ( self::AUTH_METHOD_PUBLIC );
	protected $search_in_user = false; 
	protected $logo = "http://www.kaltura.com/images/wizard/logo_archiveorg.png";
	protected $id = entry::ENTRY_MEDIA_SOURCE_ARCHIVE_ORG;
	
	private static $domain = "http://www.archive.org";
		
	private static $NEED_MEDIA_INFO = "1";
		
	const USER_AGENT = "User-Agent: Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)";

	
	public function getMediaInfo( $media_type ,$objectId) 
	{
		if( $media_type == entry::ENTRY_MEDIA_TYPE_VIDEO )
		{
			return $this->getObjectInfo( $objectId );
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
			$result = $this->search($searchText, $page);
			return self::parse($result);
		}
		else
		{
			// this provider does not supply media type $media_type
		}		
		return null;		
		
	}

	public function getAuthData( $kuserId, $userName, $password, $token)
	{
		return;
	}
		
	
	private function search($searchText, $page)
	{
		$url = self::$domain."/search.php?query=$searchText%20AND%20mediatype%3Amovies&page=$page";

		return $this->hitUrl( $url );
/*				
		$ch = curl_init();
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

	private static function parse($content)
	{
		$items = array();
		$message = '';
		if (preg_match('/<tr class="hitRow">/', $content, $containerTag))
		{
			if (preg_match_all('/<tr class="hitRow">(.*?)<\/tr>/ms', $content, $entryContainers))
			{
				foreach($entryContainers[1] as $entryContainer)
				{
					if (preg_match('/<td class="hitCell"><a class="titleLink" href="(.*?)">(.*?)<\/a>(.*?)<\/td>/', $entryContainer, $titleResult))
					{
						$url = $titleResult[1];
						$title = strip_tags($titleResult[2]);
					}	
					else 
					{
						$url = "";
						$title = "";
					}
					
					if (preg_match('/<td class="thumbCell"><img width="[0-9]*" height="[0-9]*" src="(.*)" alt="(.*)"\/><\/td>/ms', $entryContainer, $thumbnailResult))
					
						$thumbnail = self::$domain . $thumbnailResult[1];
					else
						$thumbnail = "";
					
					$items[] = array(
						'thumb' => $thumbnail,
						'title' => $title, 
						'id' => $url ,
						'flash_playback_type' => 'none',
					);
				}
			}
								
			$status = "ok";
		}
		else
		{
			$status = "error";
		}

		return array('status' => $status, 'message' => $message, 'objects' => $items , "needMediaInfo" => self::$NEED_MEDIA_INFO);
	}


	private function getObjectInfo($objectId)
	{
		$url = self::$domain . $objectId;
		
		$htmlPage = kFile::downloadUrlToString($url);

		$status = 'error';
		$message = '';
		$objectInfo = null;

		$tags = '';
		$flv = '';
		$thumbnail = '';

		$foundFlv = false;
		if (preg_match('/IAD.flvs = \["(.*)"\];/', $htmlPage, $pregResult))
		{
			$flv = "/download/".$pregResult[1];
			$foundFlv = true;
		}
		else if (self::findAlternativeVideo('MPEG4', $htmlPage))
		{
			$flv = self::findAlternativeVideo('MPEG4', $htmlPage);
			$foundFlv = true;
		}
		else if (self::findAlternativeVideo('256Kb MPEG4', $htmlPage))
		{
			$flv = self::findAlternativeVideo('256Kb MPEG4', $htmlPage);
			$foundFlv = true;
		}
		else if (self::findAlternativeVideo('64Kb MPEG4', $htmlPage))
		{
			$flv = self::findAlternativeVideo('64Kb MPEG4', $htmlPage);
			$foundFlv = true;
		}
		else if (self::findAlternativeVideo('Windows Media', $htmlPage))
		{
			$flv = self::findAlternativeVideo('Windows Media', $htmlPage);
			$foundFlv = true;
		}
		else if (self::findAlternativeVideo('QuickTime', $htmlPage))
		{
			$flv = self::findAlternativeVideo('QuickTime', $htmlPage);
			$foundFlv = true;
		}
		else if (self::findAlternativeVideo('MPEG1', $htmlPage))
		{
			$flv = self::findAlternativeVideo('MPEG1', $htmlPage);
			$foundFlv = true;
		}
		else if (self::findAlternativeVideo('MPEG2', $htmlPage))
		{
			$flv = self::findAlternativeVideo('MPEG2', $htmlPage);
			$foundFlv = true;
		}
		else if (self::findAlternativeVideo('Other', $htmlPage))
		{
			$flv = self::findAlternativeVideo('Other', $htmlPage);
			$foundFlv = true;
		}
		
		if ($foundFlv)
		{
			$flv = self::$domain.$flv;
			
			// find alternative thumbnail
			if (!$thumbnail)
			{
				$regex = '/<img title="\[item image\]"\s*alt="\[item image\]"\s*(style="([^"]*)"\s){0,2}\s*src="([^"]*)"\/>/i';
				
				if (preg_match($regex, $htmlPage, $pregResult))
				{
					$thumbnail = $pregResult[3];
				}
			}
			
			if (preg_match('/Keywords:<\/span>(.*?)<\/span>/', $htmlPage, $tagsResult))
			{
				$tags = strip_tags(@$tagsResult[1]);
				
				// remove ; and  :
				$tags = trim($tags);
				$tags = str_replace(array(";", ":"), ",", $tags); 
				$tagsArray = explode(",", $tags);
				
				// make unique
				$tagsArray = array_unique($tagsArray);
				$tagsArray = array_values($tagsArray);

				// remove white spaces
				for($i = 0, $len = count($tagsArray); $i < $len; $i++)
					$tagsArray[$i] = trim($tagsArray[$i]);
					
				// back to string
				$tags = implode(", ", $tagsArray);
			}

			$objectInfo = array (
				'id' => $objectId, 
				'url' => $flv, 
				'tags' => $tags,
				'thumbnail' => $thumbnail,
				'license' => '', 
				'credit' => '' ,
				'flash_playback_type' => 'none',
			);
			$status = 'ok';
		}

		return array('status' => $status, 'message' => $message, 'objectInfo' => $objectInfo);
	}

	public static function findAlternativeVideo($videoType, $htmlPage)
	{
		                // any character except for "
		if (preg_match('/<a href="([^"]*)">'.$videoType.'<\/a>/', $htmlPage, $pregResult))
			return $pregResult[1];		
		else
			return false;
	}
	

}
;?>