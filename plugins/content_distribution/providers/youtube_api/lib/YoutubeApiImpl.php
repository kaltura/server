<?php

//$path = dirname(__FILE__) . '/ZendGdata-1.11.3/library';
//set_include_path('C:/kaltura/opt/kaltura/app/vendor/ZendFramework/library/' . PATH_SEPARATOR . get_include_path() );
if(!class_exists("Zend_Loader"))
{
	require_once 'Zend/Loader.php'; // the Zend dir must be in your include_path 
}


if(!class_exists("Zend_Gdata_YouTube"))
{
	Zend_Loader::loadClass('Zend_Gdata_YouTube'); 
}

if(!class_exists("Zend_Gdata_ClientLogin"))
{
	Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
}

if(!class_exists("Zend_Gdata_YouTube_Extension_Access"))
{
	Zend_Loader::loadClass('Zend_Gdata_YouTube_Extension_Access');
}

class YouTubeApiImpl
{

	private $authenticationURL= 'https://www.google.com/accounts/ClientLogin'; 
	private $httpClient;							
	private $yt;
	private $developerKey='AI39si7T03NSeg60yz6QG0JtKa_xLMRjwgN0bbjmjSqxUVoAhkhLRLhiQjcB-3W8O7jGEquzjWmv9fO1rFU15KJmdSvxn-5F7g';
	private $applicationId = 'APIDistributer'; 
	private $clientId = 'APIDistributer'; 
	
	private static $categoriesMap = array(
		'Autos' => 'Autos and Vehicles',
		'Comedy' => 'Comedy',
		'Education' => 'Education',
		'Entertainment' => 'Entertainment',
		'Film' => 'Film and Animation',
		'Games' => 'Gaming',
		'Howto' => 'Howto and Style',
		'Music' => 'Music',
		'News' => 'News and Politics',
		'Nonprofit' => 'Nonprofits and Activism',
		'People' => 'People and Blogs',
		'Animals' => 'Pets and Animals',
		'Tech' => 'Science and Technology',
		'Sports' => 'Sports',
		'Travel' => 'Travel and Events',
	);
	
	public function __construct($user, $pass, array $config = null)
	{
		$this->httpClient = Zend_Gdata_ClientLogin::getHttpClient($username = $user,               
													$password = $pass,
													$service = 'youtube',
													$client = null,
													$source =  $this->clientId, // a short string identifying your application
													$loginToken = null,
													$loginCaptcha = null,
													$this->authenticationURL);

		if (!is_null($config))
			$this->httpClient->setConfig($config);
		$this->yt = new Zend_Gdata_YouTube($this->httpClient, $this->applicationId, $this->clientId, $this->developerKey);
		$this->yt->setMajorProtocolVersion(2);
	}
	
	public function uploadVideo($fileDisk, $fileUrl, $props, $private = false)
	{
//		foreach ($props as $key => $val)
//		{
//			error_log($key . " is " . $val);
//		}
		
		// create a new VideoEntry object 
		$myVideoEntry = new Zend_Gdata_YouTube_VideoEntry();  
		// create a new Zend_Gdata_App_MediaFileSource object 
		$filesource = $this->yt->newMediaFileSource($fileDisk); 
		$filesource->setContentType('video/quicktime'); 
	//	print_r($filesource);
		// set slug header 
		$filesource->setSlug($fileUrl);  
		// add the filesource to the video entry 
		$myVideoEntry->setMediaSource($filesource);  
		$myVideoEntry->setVideoTitle($props['title']); 
		$myVideoEntry->setVideoDescription($props['description']); 
		// The category must be a valid YouTube category! 
		$myVideoEntry->setVideoCategory($props['category']);  
		// Set keywords. Please note that this must be a comma-separated string 
		// and that individual keywords cannot contain whitespace 
		$myVideoEntry->setVideoTags($props['keywords']);  
		
		if($private)
			$myVideoEntry->setVideoPrivate();
		else
			$myVideoEntry->setVideoPublic();
		
		$access = array();
		$access[] = new Zend_Gdata_YouTube_Extension_Access('comment',$props['comment']);
		$access[] = new Zend_Gdata_YouTube_Extension_Access('rate',$props['rate']);
		$access[] = new Zend_Gdata_YouTube_Extension_Access('commentVote',$props['commentVote']);
		$access[] = new Zend_Gdata_YouTube_Extension_Access('videoRespond',$props['videoRespond']);
		$access[] = new Zend_Gdata_YouTube_Extension_Access('embed',$props['embed']);
		
		$myVideoEntry->setAccess($access);
		
		// set some developer tags -- this is optional 
		// (see Searching by Developer Tags for more details) 
//		$myVideoEntry->setVideoDeveloperTags(array('mydevtag', 'anotherdevtag'));  
		// set the video's location -- this is also optional 
	//	$yt->registerPackage('Zend_Gdata_Geo'); 
	//	$yt->registerPackage('Zend_Gdata_Geo_Extension'); 
	//	$where = $yt->newGeoRssWhere(); 
	//	$position = $yt->newGmlPos('37.0 -122.0'); 
	//	$where->point = $yt->newGmlPoint($position); 
	//	$myVideoEntry->setWhere($where);  
		// upload URI for the currently authenticated user 
		$uploadUrl = 'http://uploads.gdata.youtube.com/feeds/api/users/default/uploads';  
		// try to upload the video, catching a Zend_Gdata_App_HttpException,  
		// if available, or just a regular Zend_Gdata_App_Exception otherwise 
/*		try 
		{   */
			$newEntry = $this->yt->insertEntry($myVideoEntry, $uploadUrl, 'Zend_Gdata_YouTube_VideoEntry'); 
			$newEntry -> setMajorProtocolVersion(2);
			//if(isset($props['playlists']))
				//$this->handlePlaylists($newEntry, explode(',', $props['playlists']));
			
			return $newEntry->getVideoId();
/*		}
		catch (Zend_Gdata_App_HttpException $httpException) 
		{   
	//		print_r($httpException);
			echo $httpException->getRawResponseBody(); 
			return null;
		} 
		catch (Zend_Gdata_App_Exception $e) 
		{     
	//		print_r($e);
			echo $e->getMessage(); 
			return null;
		}*/
	}

	private function addPlaylist($playlistName, $videoEntry)
	{
		$newPlaylist = $this->yt->newPlaylistListEntry();
		$newPlaylist->description = $this->yt->newDescription()->setText($playlistName);
		$newPlaylist->title = $this->yt->newTitle()->setText($playlistName);
		// post the new playlist
		$postLocation = 'http://gdata.youtube.com/feeds/api/users/default/playlists';
/*		try 
		{*/
		  $newPlaylistEntry = $this->yt->insertEntry($newPlaylist, $postLocation, 'Zend_Gdata_YouTube_PlaylistListEntry');
		  $newPlaylistEntry->setMajorProtocolVersion(2);
/*		} catch (Zend_Gdata_App_Exception $e) {
		  echo $e->getMessage();
		  return;
		}	*/
		
		$postUrl = $newPlaylistEntry->getPlaylistVideoFeedUrl();

		// create a new Zend_Gdata_PlaylistListEntry, passing in the underling DOMElement of the VideoEntry
		$newPlaylistListEntry = $this->yt->newPlaylistListEntry($videoEntry->getDOM());

		// post
/*		try {*/
		  $this->yt->insertEntry($newPlaylistListEntry, $postUrl);
/*		} catch (Zend_App_Exception $e) {
		  echo $e->getMessage();
		}	*/	
	}
	
	private function updatePlaylist($playlist, $videoEntry)
	{
		$postUrl = $playlist->getPlaylistVideoFeedUrl();

		// create a new Zend_Gdata_PlaylistListEntry, passing in the underling DOMElement of the VideoEntry
		$newPlaylistListEntry = $this->yt->newPlaylistListEntry($videoEntry->getDOM());

		// post
/*		try {*/
		  $this->yt->insertEntry($newPlaylistListEntry, $postUrl);
/*		} catch (Zend_App_Exception $e) {
		  echo $e->getMessage();
		}	*/
	}

	private function removeFromPlaylist($playlistVideoEntry)
	{
		$playlistVideoEntry->delete();
	}
	
	private function handlePlaylists($videoEntry, $playlistNames)
	{
		$updatedPlaylists = array();
		$playlistListFeed = $this->yt->getPlaylistListFeed("default");
		for ($i=0; $i < $playlistListFeed->count(); $i++)
		{
			$playlistListEntry = $playlistListFeed->entries[$i];
			$key = array_search($playlistListEntry->title->text, $playlistNames);
			if ($key === FALSE) // not found
			{ 
				$playlistVideoFeed = $this->yt->getPlaylistVideoFeed($playlistListEntry->getPlaylistVideoFeedUrl());

				foreach ($playlistVideoFeed as $playlistVideoEntry) 
				{
					if ($playlistVideoEntry->getVideoId() == $videoEntry->getVideoId())
					{
						$this->removeFromPlaylist($playlistVideoEntry);
						break;
					}
				}
			}
			else
			{
				$playlistVideoFeed = $this->yt->getPlaylistVideoFeed($playlistListEntry->getPlaylistVideoFeedUrl());
				$found = false;
				foreach ($playlistVideoFeed as $playlistVideoEntry) 
				{
					$playlistVideoEntry->setMajorProtocolVersion(2);
					if ($playlistVideoEntry->getVideoId() == $videoEntry->getVideoId())
					{
						$found = true;
						break;
					}
				}

				$updatedPlaylists[] = $playlistListEntry->title->text;
				if ($found == false)
				{
					$this->updatePlaylist($playlistListEntry, $videoEntry);
				}
			}
		}
		
		foreach($playlistNames as $plName)
		{
			if (strlen($plName) == 0) continue;

			if (array_search($plName, $updatedPlaylists) === FALSE)
			{
				$this->addPlaylist($plName, $videoEntry);
			}
		}
	}
	
	private function printVideoEntry($videoEntry)  
	{   
		// the videoEntry object contains many helper functions   
		// that access the underlying mediaGroup object   
		echo 'Video: ' . $videoEntry->getVideoTitle() . "\n";   
		echo 'Video ID: ' . $videoEntry->getVideoId() . "\n";   
		echo 'Updated: ' . $videoEntry->getUpdated() . "\n";   
		echo 'Description: ' . $videoEntry->getVideoDescription() . "\n";   
		echo 'Category: ' . $videoEntry->getVideoCategory() . "\n";   
		echo 'Tags: ' . implode(", ", $videoEntry->getVideoTags()) . "\n";   
		echo 'Watch page: ' . $videoEntry->getVideoWatchPageUrl() . "\n";   
		echo 'Flash Player Url: ' . $videoEntry->getFlashPlayerUrl() . "\n";   
		echo 'Duration: ' . $videoEntry->getVideoDuration() . "\n";   
		echo 'View count: ' . $videoEntry->getVideoViewCount() . "\n";   
		echo 'Rating: ' . $videoEntry->getVideoRatingInfo() . "\n";   
		echo 'Geo Location: ' . $videoEntry->getVideoGeoLocation() . "\n";   
		echo 'Recorded on: ' . $videoEntry->getVideoRecorded() . "\n";      
		// see the paragraph above this function for more information on the    
		// 'mediaGroup' object. in the following code, we use the mediaGroup   
		// object directly to retrieve its 'Mobile RSTP link' child   
		foreach ($videoEntry->mediaGroup->content as $content) 
		{     
			if ($content->type === "video/3gpp") 
			{
				echo 'Mobile RTSP link: ' . $content->url . "\n";
			}
		}      
		echo "Thumbnails:\n";   
		$videoThumbnails = $videoEntry->getVideoThumbnails();    
		foreach($videoThumbnails as $videoThumbnail) 
		{
			 echo $videoThumbnail['time'] . ' - ' . $videoThumbnail['url'];     
			 echo ' height=' . $videoThumbnail['height'];     
			 echo ' width=' . $videoThumbnail['width'] . "\n";   
		} 
	}

	public function getEntry($remoteId)
	{
		$videoEntry = $this->yt->getVideoEntry($remoteId, null, true); 
		$this->printVideoEntry($videoEntry);
	}

	function updateEntry($remoteId, $props, $private = false)
	{
		$videoEntry = $this->yt->getVideoEntry($remoteId, null, true); 
		$putUrl = $videoEntry->getEditLink()->getHref();

		$videoEntry->setVideoTitle($props['title']); 
		$videoEntry->setVideoDescription($props['description']); 
		// The category must be a valid YouTube category! 
		$videoEntry->setVideoCategory($props['category']);  
		// Set keywords. Please note that this must be a comma-separated string 
		// and that individual keywords cannot contain whitespace 
		$videoEntry->SetVideoTags($props['keywords']);  	
		$access = array();
		$access[] = new Zend_Gdata_YouTube_Extension_Access('comment',$props['comment']);
		$access[] = new Zend_Gdata_YouTube_Extension_Access('rate',$props['rate']);
		$access[] = new Zend_Gdata_YouTube_Extension_Access('commentVote',$props['commentVote']);
		$access[] = new Zend_Gdata_YouTube_Extension_Access('videoRespond',$props['videoRespond']);
		$access[] = new Zend_Gdata_YouTube_Extension_Access('embed',$props['embed']);
		
		$videoEntry->setAccess($access);
		
		if($private)
			$videoEntry->setVideoPrivate();
		else
			$videoEntry->setVideoPublic();
		
		$newEntry = $this->yt->updateEntry($videoEntry, $putUrl);
		$newEntry->setMajorProtocolVersion(2);
		//$this->handlePlaylists($newEntry, explode(',', $props['playlists']));
	}

	function deleteEntry($remoteId)
	{
		$videoEntry = $this->yt->getVideoEntry($remoteId,'http://gdata.youtube.com/feeds/users/default/uploads', true); 
		$this->yt->delete($videoEntry);
	}
	
	public static function getCategoriesMap()
	{
		return self::$categoriesMap;
	}
	
	public function uploadCaption($videoRemoteId, $captionContent, $language) {
		$headerParams = array ();
		$headerParams[] = 'Content-Language: '.$language;
		$contetType = 'application/vnd.youtube.timedtext; charset=UTF-8';
		$url = "http://gdata.youtube.com/feeds/api/videos/$videoRemoteId/captions";
		$response = $this->yt->post($captionContent,$url,null,$contetType,$headerParams);
		KalturaLog::debug('YouTube api response:'.print_r($response,true));
		//getting the new caption remote id
		$location = $response->getHeader('Location');
		$matches = array();
		preg_match('/captions\/(.+)\?/', $location, $matches);
		if($matches)
			return $matches[1];
		return null;
	}
	
	public function updateCaption($videoRemoteId, $captionRemoteId, $captionContent) {
		$contetType = 'application/vnd.youtube.timedtext; charset=UTF-8';
		$url = "http://gdata.youtube.com/feeds/api/videos/$videoRemoteId/captiondata/$captionRemoteId";
		$this->yt->put($captionContent,$url,null,$contetType,null);
	}
	
	public function deleteCaption($videoRemoteId, $captionRemoteId) {
		$url = "http://gdata.youtube.com/feeds/api/videos/$videoRemoteId/captions/$captionRemoteId";
		$this->yt->delete($url,null);
	}

	/**
	 * 
	 * @param string $videoEntryId
	 * @param Zend_Gdata_YouTube_PlaylistListEntry $playlistEntry
	 * @return boolean
	 */
	public function isVideoEntryPartOfPlaylist( $videoEntryId, $playlistEntry ) {
		$done = false;
		while ( ! $done ) {
			$playlistVideoFeed = $this->yt->getPlaylistVideoFeed($playlistEntry->getPlaylistVideoFeedUrl());
			foreach ($playlistVideoFeed as $playlistVideoEntry) {
				if ($playlistVideoEntry->getVideoId() == $videoEntryId) {
					return true;
				}
			}

			// Try to get the feed's next page of results 
			try {
				$playlistVideoFeed = $playlistVideoFeed->getNextFeed();
			}
			catch ( Exception $e ) {
				$done = true; // No more results
			}
		}

		return false;
	}

	/**
	 * Add a video entry to all specified youtube playlist IDs.
	 * Note: The video will not be added if it is already a part of a playlist.
	 * 
	 * Playlist Id example: PLuZdKFy9k0mMdYHeXLd_h7ymC_ayXU9ga
	 * 
	 * @param string $videoEntryId
	 * @param atring|array $playlistIds Comma separated string or an array of YouTube playlist IDs
	 */
	public function attachVideoEntryToPlaylistIds( $videoEntryId, $playlistIds ) {
		if ( is_string( $playlistIds ) ) {
			$playlistIds = explode(',', $playlistIds);
		}

		if ( empty( $playlistIds ) || !is_array( $playlistIds ) ) {
			return; // Do nothing
		}

		// Trim potential whitespace in playlist IDs 
		$playlistIds = array_map( 'trim', $playlistIds );

		// Fetch and loop YT's playlists - attach the video entry to any playlist
		// that matches a given playlist id.
		$playlistFeed = $this->yt->getPlaylistListFeed( "default" );

		$done = false;
		while ( ! $done ) {
			foreach ( $playlistFeed as $playlistEntry ) {
				$playlistId = $playlistEntry->getPlaylistId();
				if ( in_array( $playlistEntry->getPlaylistId(), $playlistIds ) ) {
	 				$partOfPlaylist = $this->isVideoEntryPartOfPlaylist( $videoEntryId, $playlistEntry );
	
					if ( ! $partOfPlaylist ) {
						KalturaLog::log( "Adding video entry $videoEntryId to playlist $playlistId" );
	 					$videoEntry = $this->yt->getVideoEntry( $videoEntryId );
						$this->updatePlaylist($playlistEntry, $videoEntry);
					}
					else {
						KalturaLog::debug( "Video entry $videoEntryId already exists in playlist $playlistId" );
					}					
				}
			}
			
			// Try to get the feed's next page of results 
			try {
				$playlistFeed = $playlistFeed->getNextFeed();
			}
			catch ( Exception $e ) {
				$done = true; // No more results
			}
		}
	}
}
