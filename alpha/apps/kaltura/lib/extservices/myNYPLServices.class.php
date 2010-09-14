<?php
class myNYPLServices extends myBaseMediaSource implements IMediaSource
{
	protected $supported_media_types =  self::SUPPORT_MEDIA_TYPE_IMAGE;  
	protected $source_name = "NYPL";
//	protected $auth_method = self::AUTH_METHOD_NONE;
	protected $search_in_user = false; 
	protected $logo = "http://www.kaltura.com/images/wizard/logo_nypl.png";
	protected $id = entry::ENTRY_MEDIA_SOURCE_NYPL;
		
	private static $NEED_MEDIA_INFO = "1";
	
	
	public function getMediaInfo( $media_type ,$objectId) 
	{
		if( $media_type == entry::ENTRY_MEDIA_TYPE_IMAGE )
		{
			return self::getImageInfo( $objectId );
		}
		else
		{
			// this provider does not supply media type $media_type
		}		
		return null;		
	}

	public function searchMedia( $media_type , $searchText, $page, $pageSize, $authData = null, $extraData = null)
	{
		if( $media_type == entry::ENTRY_MEDIA_TYPE_IMAGE )
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
	
	
// -------------------------------------------------------------------------------
	
	private  static function sendRequest($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	
		$response = curl_exec($ch);
		
		curl_close($ch);
		
		$response = json_decode($response);


/* Objects returned are of the following structure
 * 
"ResultTotal":"180",
"ResultQuery":"vocal*",
"ResultSet":"Items 1 through 20",
"ResultItems":
[
 {
      "Title":"Vocal group, radio workshop, Nation Youth Administration
for",
      "ImageId":"1260379",
      "ImageLink":"http:\/\/images.nypl.org/?id=1260379&t=t",

"RecordLink":"http:\/\/digitalgallery.nypl.org\/nypldigital\/id?1260379"
 */
		$images = array();
		$message = '';
		
		if( $response->ResultTotal > 0 ) foreach($response->ResultItems as $image)
		{
			$images[] = array(
				'thumb' => $image->ImageLink,
				'title' => $image->Title,
				'id' => $image->ImageID);

		}
		else $message = 'No Images found';

		$status = 'ok';
		
		return array('status' => $status, 'message' => $message, 'objects' => $images , "needMediaInfo" => self::$NEED_MEDIA_INFO);
	}


		
	private static function searchImages($searchText, $page, $pageSize)
	{
		// num is the index within result set
		// imgs is the page size
		// word is the key word. Multiple search terms can be separated by a URLencoded space or a "+". The

		/* boolean default is "AND." The url.parm "s=[1|2|3]" can be also used to set
			the boolean operator specifically to AND (s=1), OR (s=2), or a PHRASE
			(s=3).  Truncation is also possible for terms via "*" (word) or "?"
			(single-char). */
		
		$url = "http://digitalgallery.nypl.org/feeds/json?size=150&num=". (( $page - 1 ) * $pageSize + 1 ) ."&imgs=".$pageSize . "&word=".urlencode($searchText);
		
		return self::sendRequest($url);
	}


	private function getImageInfo($photoId)
	{
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://digitalgallery.nypl.org/feeds/json/?size=760&word=".$photoId );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		$response = curl_exec($ch);
		curl_close($ch);
		$response = json_decode($response);
		
		if( $response->ResultTotal >= 1 )
		{
			$image = $response->ResultItems[0];
			
			$objectInfo = array('id' => $photoId, 
								'url' => $image->ImageLink, 
								'title' => $image->Title,
								'tags' => '',
								'license' => 'Public Domain',
								'source_link' => $image->RecordLink,
								'credit' => 'New York Public Library');
		
			$status = 'ok';
			$message = '';
		
		}
		else // if we couldn't get hi-res image, try a lower resolution
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://digitalgallery.nypl.org/feeds/json/?size=300&word=".$photoId );
			curl_setopt($ch, CURLOPT_RETURNTRANSFER , TRUE);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
			$response = curl_exec($ch);
			curl_close($ch);
			$response = json_decode($response);
		
			if( $response->ResultTotal >= 1 )
			{
				$image = $response->ResultItems[0];
				
				$objectInfo = array('id' => $photoId, 
								'url' => $image->ImageLink, 
								'title' => $image->Title,
								'tags' => '',
								'license' => 'Public Domain',
								'source_link' => $image->RecordLink,
								'credit' => 'New York Public Library');
		
				$status = 'ok';
				$message = '';	
			}
			else { 
				$message = 'Full image not found';
				$status = 'error';
				$objectInfo = null;
			}
		}
		
		return array('status' => $status, 'message' => $message, 'objectInfo' => $objectInfo );
	}
	
}
?>