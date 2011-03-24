<?php

$path = 'C:/kaltura/opt/kaltura/app/plugins/content_distribution/providers/youtube_api/ZendGdata-1.11.3/library';
set_include_path($path . PATH_SEPARATOR . get_include_path() );

require_once 'Zend/Loader.php'; // the Zend dir must be in your include_path 
Zend_Loader::loadClass('Zend_Gdata_YouTube'); 
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');

class YouTubeApiImpl
{

	private $authenticationURL= 'https://www.google.com/accounts/ClientLogin'; 
	private $httpClient;							
	private $yt;
	private $developerKey='AI39si7T03NSeg60yz6QG0JtKa_xLMRjwgN0bbjmjSqxUVoAhkhLRLhiQjcB-3W8O7jGEquzjWmv9fO1rFU15KJmdSvxn-5F7g';
	private $applicationId = 'APIDistributer'; 
	private $clientId = 'APIDistributer'; 
	
	public function __construct($user, $pass)
	{
		$this->httpClient = Zend_Gdata_ClientLogin::getHttpClient($username = $user,               
													$password = $pass,
													$service = 'youtube',
													$client = null,
													$source =  $this->clientId, // a short string identifying your application
													$loginToken = null,
													$loginCaptcha = null,
													$this->authenticationURL);
													
		$this->yt = new Zend_Gdata_YouTube($this->httpClient, $this->applicationId, $this->clientId, $this->developerKey);
		$this->yt->setMajorProtocolVersion(2);
	}
	
	public function uploadVideo($fileDisk, $fileUrl,$props)
	{
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
		$myVideoEntry->SetVideoTags($props['keywords']);  
		$myVideoEntry->setVideoPublic();
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
		try 
		{   
			$newEntry = $this->yt->insertEntry($myVideoEntry, $uploadUrl, 'Zend_Gdata_YouTube_VideoEntry'); 
			$newEntry -> setMajorProtocolVersion(2);
			
			return $newEntry->getVideoId();
		}
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
		$videoEntry = $this->yt->getVideoEntry($remoteId); 
		$this->printVideoEntry($videoEntry);
	}

	function updateEntry($remoteId, $props)
	{
		$videoEntry = $this->yt->getVideoEntry($remoteId,null, true); 
		$putUrl = $videoEntry->getEditLink()->getHref();

		$videoEntry->setVideoTitle($props['title']); 
		$videoEntry->setVideoDescription($props['description']); 
		// The category must be a valid YouTube category! 
		$videoEntry->setVideoCategory($props['category']);  
		// Set keywords. Please note that this must be a comma-separated string 
		// and that individual keywords cannot contain whitespace 
		$videoEntry->SetVideoTags($props['keyword']);  	

		$this->yt->updateEntry($videoEntry, $putUrl);
	}

	function deleteEntry($remoteId)
	{
		$videoEntry = $this->yt->getVideoEntry($remoteId,'http://gdata.youtube.com/feeds/users/default/uploads', true); 
		$this->yt->delete($videoEntry);
	}
}

$impl = new YouTubeApiImpl('kalturasb', '250vanil');
$props = Array();
$props['title'] = 'My Test Movie';
$props['description'] = 'My Test Movie';
$props['category'] = 'Autos';
$props['keywords'] = 'cars, funny';

print $impl -> uploadVideo('snake.wmv','snake.wmv', $props);
//$newEntry = uploadVideo($yt, 'snake.wmv','snake.wmv', 'My Test Movie', 'My Test Movie', 'Autos', 'cars, funny');
//$newEntry -> setMajorProtocolVersion(2);
//getEntry($yt, 'J9JY0k8nLiE');
//updateEntry($yt, $newEntry->getVideoId(), 'nwe title', 'new desc', 'Autos', 'cars, flickr');

//echo  $newEntry->getVideoId();

//deleteEntry($yt, '1rzHS0G9B-Q');

//if (!empty($newEntry))
{
//	$newEntry -> setMajorProtocolVersion(2);
//	$remoteId = $newEntry -> getVideoId();
}
?>