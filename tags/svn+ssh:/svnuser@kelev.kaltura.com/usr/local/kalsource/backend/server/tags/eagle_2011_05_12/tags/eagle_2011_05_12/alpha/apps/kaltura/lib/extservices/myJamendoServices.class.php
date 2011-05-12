<?php
class myJamendoServices extends myBaseMediaSource implements IMediaSource
{
	protected $supported_media_types = self::SUPPORT_MEDIA_TYPE_AUDIO;  
	protected $source_name = "Jamendo";
//	protected $auth_method = self::AUTH_METHOD_NONE;
	protected $search_in_user = false; 
	protected $logo = "http://img.jamendo.com/logo/jamendo-200-en.png";
	protected $id = entry::ENTRY_MEDIA_SOURCE_JAMENDO;
	
	private static $NEED_MEDIA_INFO = "1";

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
			return $this->searchSounds(  $searchText, $page, $pageSize, $authData );
		}
		else
		{
			// this provider does not supply media type $media_type
		}		
		return null;		
	}

	public function getAuthData( $kuserId, $userName, $password, $token)
	{
		return array('status' => 'ok', 'message' => '', 'authData' => $userName);
//		return ""; // empty value
	}
	
	
// ----------------------------------------------------------------
	
	private function searchSounds($searchText, $page, $pageSize)
	{
		$url = "http://api.jamendo.com/get2/id+name+stream+artist_name+album_name+album_image/track/json/track_album+album_artist/?searchquery=".urlencode($searchText);
		return $this->sendRequest($url);
	}
	
	// TODO - fix paging. For now it's hard-coded first 50
	private function sendRequest($url , $page_size=null )
	{
		$postData = "n=50&order=searchweight_desc";
		
//		$response = $this->hitUrl ( $url , $postData );

		// DON'T use the $this->hitUrl - the line 			
		// 			curl_setopt($ch, CURLOPT_HEADER, 1); 
		// is harmful !
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	
		$response = curl_exec($ch);
		
		curl_close($ch);
		
		$response = json_decode($response);
		
		$sounds = array();
		$message = '';
		
		foreach($response as $sound)
		{
			$sounds[] = array(
				'url' => $sound->stream,
				'title' => $sound->name.' ('.$sound->album_name.') by '.$sound->artist_name,
				'id' => $sound->id,
				'thumb' => $sound->album_image,
			);
		}

		$status = 'ok';
		
		return array('status' => $status, 'message' => $message, 'objects' => $sounds, "needMediaInfo" => self::$NEED_MEDIA_INFO);
	}

	

	
	
}
?>