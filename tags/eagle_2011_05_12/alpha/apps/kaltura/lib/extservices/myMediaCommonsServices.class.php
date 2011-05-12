<?php
class myMediaCommonsServices extends myBaseMediaSource implements IMediaSource
{
	protected $supported_media_types =  self::SUPPORT_MEDIA_TYPE_IMAGE; // for now - supports only images  
	protected $source_name = "MediaCommons";
//	protected $auth_method = self::AUTH_METHOD_NONE;
	protected $search_in_user = false; 
	protected $logo = "http://www.kaltura.com/images/wizard/logo_phototbucket.png";
	protected $id = entry::ENTRY_MEDIA_SOURCE_MEDIA_COMMONS;
		
	private static $NEED_MEDIA_INFO = "1";
	
	public  function getMediaInfo( $media_type ,$objectId) 
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
//		echo __CLASS__ . " ($media_type , $searchText, $page, $pageSize, $authData)\n";
		
		if( $media_type == entry::ENTRY_MEDIA_TYPE_VIDEO )
		{
			$result = self::search("videos", $searchText, $page , $pageSize);
			return self::parse(false, $result);
		}
		elseif( $media_type == entry::ENTRY_MEDIA_TYPE_IMAGE )
		{
			$result = self::search("images", $searchText, $page , $pageSize );
			return self::parse(true, $result);
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

	

	
	private static function search($searchType, $searchText, $page, $pageSize  )
	{
	
		//http://tools.wikimedia.de/~tangotango/mayflower/search.php?q=cat&t=r
		//http://tools.wikimedia.de/~tangotango/mayflower/search.php?q=cats&t=r
		//images, videos, myimages
//		$albumUrl = "http://s152.photobucket.com/albums/s182/eranetam/?start=all";
//		$albumUrl = "http://s152.photobucket.com/$searchType/$searchText/?page=$page";
	
		//		p=2&a=3&t=r&z=15&q=cats+
		// p - page (starting at 1)
		// t - search by n-new r-relevant
		// z - page size
		// q - keyword
		// a - 1 (more detailed - will return tags)
		$albumUrl = "http://tools.wikimedia.de/~tangotango/mayflower/search.php?q={$searchText}&t=r&a=1&p={$page}&z={$pageSize}";
		
//		echo "albumUrl:$albumUrl\n";


		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $albumUrl);
//		if ($sessionCookie)			curl_setopt($ch, CURLOPT_COOKIE, $sessionCookie);
			
		$userAgent          = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061208 Firefox/2.0.0.1";
		
//		curl_setopt($ch, CURLOPT_HEADER, 1);
//		curl_setopt($ch, CURLOPT_NOBODY, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
	
		$content = curl_exec($ch);
		curl_close($ch);
		
//		echo "\n" . __CLASS__ . "\n{$content}\n";
		
		return $content;
	}

	private static function parse($searchImages, $content)
	{
		$results = array();
		$message = '';
		
/*
 * 			$object = array ( "id" => $entry->getId() ,
				"url" => $entry->getDataUrl() , 
				"tags" => $entry->getTags() ,
				"title" => $entry->getName() , 
				"thumb" => $entry->getThumbnailUrl() , 
				"description" => $entry->getTags() );
 */		
				
/*
 * The result is in one single line - ends with new line
 * 
 * <div class="tb pa">
 * 	<div class="tm" id="Komondor_Westminster_Dog_Show.jpg" onclick="g('Komondor_Westminster_Dog_Show.jpg')">
 * 		<div class="cl">
 * 			<a href="http://commons.wikimedia.org/wiki/Image:Komondor_Westminster_Dog_Show.jpg">
 * 				<img src="http://commons.wikimedia.org/w/thumb.php?f=Komondor_Westminster_Dog_Show.jpg&amp;w=78" alt="Komondor_Westminster_Dog_Show.jpg" title="Komondor_Westminster_Dog_Show.jpg" />
 * 			</a>
 * 		</div>
 * 		<div class="caption">Komondor_Westmins...</div>
 * 	</div>
 * 	<div class="ct">
 * 		<strong>Categories:</strong> 
 * 		<a href="http://commons.wikimedia.org/wiki/Category:Westminster_Kennel_Club_Dog_Show">Westminster Kennel Club Dog Show</a> 
 * 		(<a href="search.php?a=1&amp;t=r&amp;z=30&amp;q=dog+%2BCategory:Westminster_Kennel_Club_Dog_Show">+</a>/<a href="search.php?a=1&amp;t=r&amp;z=30&amp;q=dog+-Category:Westminster_Kennel_Club_Dog_Show">-</a>), 
 * 		<a href="http://commons.wikimedia.org/wiki/Category:White_dogs">White dogs</a> 
 * 		(<a href="search.php?a=1&amp;t=r&amp;z=30&amp;q=dog+%2BCategory:White_dogs">+</a>/<a href="search.php?a=1&amp;t=r&amp;z=30&amp;q=dog+-Category:White_dogs">-</a>), 
 * 		<a href="http://commons.wikimedia.org/wiki/Category:Dogs">Dogs</a> 
 * 		(<a href="search.php?a=1&amp;t=r&amp;z=30&amp;q=dog+%2BCategory:Dogs">+</a>/<a href="search.php?a=1&amp;t=r&amp;z=30&amp;q=dog+-Category:Dogs">-</a>)
 * 		<br /><br />
 * 		<img src="images/by_icon.png" alt="BY" title="Attribution required" />
 * 		<img src="images/sa_icon.png" alt="SA" title="Share-alike" />
 * 	</div>
 * </div>
 * 
 * 
 */		
		//<div class="tb pa">
		// analyze search page thumbnails
		if (preg_match_all ('/<div [^>]*class="tb [^"]*">(.*?"cl".*?"ct".*?)<\/div><\/div>/s', $content, $entryContainers )  )
		{
			$i=1;
			
			foreach ( $entryContainers[1] as $entryContainer )
			{
				$data = null;
				$tag = "";
				if (preg_match ('/<div class="cl">.*<a href="([^"]*)".*<img src="([^"]*)".*title="([^"]*)".*<\/div>/s', $entryContainer, $entryData ))
				{
					$url = $entryData[1]; // 1
					$thumbnail = urldecode( $entryData[2] );  //2
					$title = kFile::getFileNameNoExtension( $entryData[3] );
					if ( $title )
						$title = str_replace( "_" , " " , $title );
					
					$data = array('thumb' => $thumbnail, 'title' => $title, 'id' => $url );
				}


// We need to hit again any way to fetch the actual image - no use in extracting the tags here 				
				//if (preg_match_all('/class="ct">.*?(.*?)$/s', $entryContainer, $entryTags ))			
 				//if (preg_match_all('/class="ct">.*(<a [^>]*>(.*)<\/a>)*$/s', $entryContainer, $entryTags))
				//if (preg_match_all('/class="ct">.*?(<a [^>]*>(.*?)<\/a>)+.*$/ms', $entryContainer, $entryTags )) //, PREG_SET_ORDER))
				//if (preg_match_all('(class.*?(<a\s*[^>]*>))', $entryContainer, $entryTags )) //, PREG_SET_ORDER))
 				if (preg_match ('/class="ct">.*?$/s', $entryContainer, $entryTagsContainer))
				{
					$tag = "";
					
					if ( preg_match_all('/<a [^>]*>(.*?)<\/a>/s', $entryTagsContainer[0], $entryTags)) 
					{	
						foreach ( $entryTags[1] as $entryTag )
						{
							if ( strlen ( $entryTag) > 3 )
							{
								$tag .= ($tag ? ", "  : "" ) . $entryTag;
							}
						}
						$data ["tags"] = $tag;
					}
				}
			
				$results[] = $data;
				
				$i++;
				
				
			}			
			$status = "ok";
		}
		else
		{
			$status = "error";
		}
		
		return array('status' => $status, 'message' => $message, 'objects' => $results, "needMediaInfo" => self::$NEED_MEDIA_INFO);
	}


	
	// The $objectId is the full URL in which the media is placed
	private static function getObjectInfo($objectId)
	{
		$status = 'error';
		$message = '';
		$objectInfo = null;

		$content = $this->hitUrl( $objectId , null , $userAgent );
/*		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $objectId);
		$userAgent          = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061208 Firefox/2.0.0.1";
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
	
		$content = curl_exec($ch);
		curl_close($ch);
	*/	
		// the URL of the media is in the href of the first 'a' tag
		if ( preg_match ( '/<div .*id="file".*?>.*?<a href="(.*?)".*?<\/div>/' , $content , $file_data ) )
		{
			$url = $file_data[1];
			$objectInfo = array('id' => $objectId, 'url' => $url, 
//					'title' => $title,
//					'tags' => $tags,
					'license' => 'Creative Commons',
//					'license' => '',  	// fetch data from 'Licensing' section 
					'credit' => 'Media Commons',
					'source_link' => $objectId
			);
				
			$status = 'ok';
			//$objectInfo = array('id' => $objectId, 'url' => $objectId);
		}
		
		
		
		return array('status' => $status, 'message' => $message, 'objectInfo' => $objectInfo);
	}
	

}
?>