<?php
class myMySpaceServices extends myBaseMediaSource implements IMediaSource
{
	protected $supported_media_types =  self::SUPPORT_MEDIA_TYPE_VIDEO ;  
	protected $source_name = "MySpace";
	protected $auth_method = array ( self::AUTH_METHOD_PUBLIC ,  self::AUTH_METHOD_USER_PASS );
	protected $search_in_user = true; 
	protected $logo = "http://www.kaltura.com/images/wizard/logo_myspace.png";
	protected $id = entry::ENTRY_MEDIA_SOURCE_MYSPACE;
		
	private static $NEED_MEDIA_INFO = "1";
		
	const USER_AGENT = "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3 (.NET CLR 3.5.30729)";
	
	public function getMediaInfo( $media_type ,$objectId) 
	{
		if( $media_type == entry::ENTRY_MEDIA_TYPE_VIDEO )
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
			return self::searchVideos(  $searchText, $page, $pageSize, $authData );
		}
		else
		{
			// this provider does not supply media type $media_type
		}		
		return null;		
		
	}	
	
	public function getAuthData($kuserId, $userName, $password, $token)
	{
		//curl -c cookie_file -d "NextPage=fuseaction=user&email=USER@XYZ.COM&password=PASSWORD" "http://login.myspace.com/index.cfm?fuseaction=login.process"
		//curl -b cookie_file "http://vids.myspace.com/index.cfm?fuseaction=vids.myVideos"
		
		$postData = array(
			'ctl00$ctl00$cpMain$cpMain$LoginBox$Email_Textbox' => $userName, 
			'ctl00$ctl00$cpMain$cpMain$LoginBox$Password_Textbox' => $password
		);

		/*
		$o="";
		foreach ($postData as $k => $v)
			$o.= "$k=".utf8_encode($v)."&";
		$postData = substr($o,0,-1);
		*/
		
		$loginUrl = "http://secure.myspace.com/index.cfm?fuseaction=login.process";
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $loginUrl);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , TRUE);
		
		$response = curl_exec($ch);
		curl_close($ch);

		//print($header);

		$status = 'error';
		$message = '';
		$authData = null;
		
		if (preg_match('/Set-Cookie: (USER=[^;].*?);/', $response, $sessionCookieMatch))
		{
			$status = 'ok';
			$authData = base64_encode(trim($sessionCookieMatch[1]));
		}
		
		return array('status' => $status, 'message' => $message, 'authData' => $authData );
	}

	private  static function search($searchText, $page)
	{
		$url = "http://searchservice.myspace.com/index.cfm?fuseaction=sitesearch.results&orig=search_Header&origpfc=VidsSearch&type=MySpaceTV&qry=$searchText&submit=Search&pg=".$page;
		//$url = "http://vids.myspace.com/index.cfm?fuseaction=vids.search&q=$searchText&page=".($page - 1);
		//$url = "http://vidsearch.myspace.com/index.cfm?fuseaction=vids.fullsearch";
		//$postData = "page=$page&t=".$searchText."&orderby=1&limit=1";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);

		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_NOBODY, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

		$content = curl_exec($ch);
		curl_close($ch);

		return $content;
	}
	
	private static function parse($content, $tableRegex)
	{
		/*
		<div class="videoYDiv clearfix">
		  <div class="videoLeftDiv">
		    <div class="videoThumb">
		      <a href="http://vids.myspace.com/index.cfm?fuseaction=vids.individual&amp;videoid=55025799&amp;searchid=f222330f-fd3c-4643-b7c9-a182ebd486f7"
		      class="">
			<img src="http://d3.ac-videos.myspacecdn.com/videos02/114/thumb1_cc50838664fd4cc281ef3b85f64a2b36.jpg"
			alt="" title="" onerror="UseNoPicImage(this)" />
		      </a>
		    </div>
		  </div>
		  <div class="videoCenterDiv">
		    <div id="videoTitle">
		      <a href="http://vids.myspace.com/index.cfm?fuseaction=vids.individual&amp;videoid=55025799&amp;searchid=f222330f-fd3c-4643-b7c9-a182ebd486f7">
	      
			<b>test</b>
		      </a>
		    </div>
		    <div id="videoAuthorContainer">by 
		    <a href="http://vids.myspace.com/index.cfm?fuseaction=vids.channel&amp;contributorid=30985862&amp;searchid=f222330f-fd3c-4643-b7c9-a182ebd486f7">
		    Content</a></div>
		    <div class="ssOrange clearfix">(Official)</div>
		    <div id="videoDesc">tes</div>
		    <div id="videoCategories">Categories: 
		    <a href="http://vids.myspace.com/index.cfm?fuseaction=vids.charts&amp;category=11&amp;searchid=f222330f-fd3c-4643-b7c9-a182ebd486f7">
		    Animals</a>, 
		    <a href="http://vids.myspace.com/index.cfm?fuseaction=vids.charts&amp;category=17&amp;searchid=f222330f-fd3c-4643-b7c9-a182ebd486f7">
		    Comedy &amp; Humor</a></div>
		    <div id="videoTags">Tags: 
		    <a href="http://searchservice.myspace.com/index.cfm?fuseaction=sitesearch.results&amp;t=test&amp;type=MySpaceTV&amp;videotype=0">
	      
		      <b>test</b>
		    </a></div>
		  </div>
		  <div class="videoRightDiv">
		    <ul>
		      <li>
		      <span class="labelPrefix">Time:</span>02:28</li>
		      <br />
		      <li>
		      <span class="labelPrefix">Rating:</span>92%</li>
		      <br />
		      <li>
		      <span class="labelPrefix">Plays:</span>3,766</li>
		      <br />
		      <li>
		      <span class="labelPrefix">Comments:</span>1</li>
		      <br />
		    </ul>
		  </div>
		</div>
		*/
		$images = array();
		$message = '';

		// analyze search page thumbnails
		if (preg_match($tableRegex, $content, $containerTag))
		{
			if (preg_match_all('/<div class="videoYDiv clearfix">(.*?)<span class="labelPrefix">Comments/ms', str_replace(PHP_EOL, '', $containerTag[1]), $entryContainers))
			{
				foreach($entryContainers[1] as $entryContainer)
				{
					$title = "";
					$videoId = "";
		
					if (preg_match('/<div id="videoTitle">.*?<a.*?videoid=(\d+).*?>(.*?)<\/a>/is', $entryContainer, $titleTag))
					{
						$videoId = $titleTag[1];
						$title = strip_tags($titleTag[2]);
					}
					if(!self::validateObject($videoId))
						continue;

					$duration = "";
					if (preg_match('/<span class="labelPrefix">Time:<\/span>(.*?)<\/li>/is', $entryContainer, $durationTag))
					{
						$duration = ltrim(rtrim($durationTag[1]));
					}

					$thumbnail = "";
					$videoFlvId = "";
					if (preg_match('/<img src="(.*?)".*?>/', $entryContainer, $imgTag))
					{
						$thumbnail = $imgTag[1];
					}

					$images[] = array('thumb' => $thumbnail,
						'title' => $title, 
						'description' => "$title ($duration)",
						'id' => $videoId);
				}
			}

			$status = "ok";
		}
		else
		{
			$status = "error";
		}

		return array('status' => $status, 'message' => $message, 'objects' => $images , "needMediaInfo" => self::$NEED_MEDIA_INFO);
	}

	private  static function parse_auth($content, $tableRegex)
	{
		/*
		 <tr>
			<td class="still">
			<a class="still" href="/index.cfm?fuseaction=vids.individual&videoid=7648935">
			<img width="120" height="90" src="http://myspace-562.vo.llnwd.net/02019/26/50/2019350562_thumb1.jpg"/>
			</a>
			</td>
			<td class="summary">
			<div class="rating">
			Rating:
			<strong>90%</strong>
			</div>
			<h2 class="title">
			<a href="/index.cfm?fuseaction=vids.individual&videoid=7648935">Timmy and the Sun</a>
			</h2>
			<div class="text">
			<span>
			<strong>0:17</strong>
			</span>
			</div>
			<div class="description" wbr="true">Timmy and the Sun</div>
			<div class="text">
			Categories:
			<a href="/index.cfm?fuseaction=vids.charts&category=4">Comedy and Humor</a>
			<br/>
			Tags:
			<a href="/index.cfm?fuseaction=vids.search&t=Sun">Sun</a>
			<br/>
			Added:
			<span>3 months ago</span>
			<br/>
			By:
			<a href="/index.cfm?fuseaction=vids.channel&ChannelID=42960487">Peter Anderson</a>
			<br/>
			Plays:
			<span>4,381</span>
			| Comments:
			<span>10</span>
			</div>
			</td>
			</tr>
		 */
		$images = array();
		$message = '';

		// analyze search page thumbnails
		if (preg_match($tableRegex, $content, $containerTag))
		{
			if (preg_match_all('/<tr>(.*?)<\/tr>/ms', $containerTag[1], $entryContainers))
			{
				foreach($entryContainers[1] as $entryContainer)
				{
					$title = "";
					$videoId = "";
		
					if (preg_match('/<h2 class="title">.*?<a.*?videoid=(\d+).*?>(.*?)<\/a>/is', $entryContainer, $titleTag))
					{
						$videoId = $titleTag[1];
						$title = $titleTag[2];
					}

					$duration = "";
					if (preg_match('/<div class="text">.*?<span><strong>(.*?)<\/strong><\/span>.*?<\/div>/is', $entryContainer, $durationTag))
					{
						$duration = $durationTag[1];
					}

					$thumbnail = "";
					$videoFlvId = "";
					if (preg_match('/<img src="(.*?)".*?>/', $entryContainer, $imgTag))
					{
						$thumbnail = $imgTag[1];
					}

					$images[] = array('thumb' => $thumbnail,
						'title' => $title, 
						'description' => "($duration secs) $title",
						'id' => $videoId);
				}
			}

			$status = "ok";
		}
		else
		{
			$status = "error";
		}

		return array('status' => $status, 'message' => $message, 'objects' => $images , "needMediaInfo" => self::$NEED_MEDIA_INFO);
	}
	
	private static function validateObject($objectId)
	{
		$rssFeed = kFile::downloadUrlToString("http://mediaservices.myspace.com/services/rss.ashx?type=video&videoID=$objectId", 3);
		if (preg_match('/Location: (.*?)\/Error/', $rssFeed))
			return false;
		if (preg_match('/The item is not available/', $rssFeed))
			return false;
		
		return true;
	}

		
	private static function getObjectInfo($objectId)
	{
		$htmlPage = kFile::downloadUrlToString("http://vids.myspace.com/index.cfm?fuseaction=vids.individual&videoid=$objectId");
		
		$status = 'error';
		$message = '';
		$objectInfo = null;

		$title = '';
		$tags = '';
		$flvUrl = '';

		// 2009-10-19: mySpace changed the HTML result.
		// It may be that there are 2 <h1> tags  
		if (preg_match('/<h1.*?tv_tbar.*?>(.*?)<\/h1>/', $htmlPage, $titleTag))
		{
			$title = $titleTag[1];
  			if ( preg_match('/<a.*?>(.*?)<\/a>/', $title, $titleInnerValue))
			{
        		$title = $titleInnerValue[1];
			}		

			if (preg_match('/<div.*?>.*?Tags:(.*?)<\/div>/ms', $htmlPage, $tagsContainer))
			{
				if (preg_match_all('/<a href.*?>(.*?)<\/a>/ms', $tagsContainer[1], $tagsWords))
				{
					$tags = implode(',', $tagsWords[1]);
				}
			}

			$rssFeed = kFile::downloadUrlToString("http://mediaservices.myspace.com/services/rss.ashx?type=video&videoID=$objectId");
			if (preg_match_all('/<media:content url="(.*?)"/ms', $rssFeed, $urlAttr))
			{
				$flvUrl = $urlAttr[1][0];
			}
			/*
			else
			{
				$len = strlen($videoFlvId);
				$flvUrl = sprintf("http://content.movies.myspace.com/%07d/%s%s/%s%s/%d.flv",
				floor($videoFlvId / 100000), $videoFlvId[--$len], $videoFlvId[--$len],
				$videoFlvId[--$len], $videoFlvId[--$len], $videoFlvId);
			}
			*/

			$objectInfo = array('id' => $objectId, 'title' => $title,
				'url' => $flvUrl,
				'tags' => $tags,
				'license' => '', 'credit' => '');

				$status = 'ok';
		}

		return array('status' => $status, 'message' => $message, 'objectInfo' => $objectInfo);
	}



	
	private  static function searchVideos($searchText, $page, $pageSize, $authData = null)
	{
		$searchText = str_replace(' ', '+', $searchText);
		if ($authData)
		{
			$result = self::searchYourOwn($searchText, $page, $pageSize, $authData);
			return self::parse_auth($result, '/<table class="myUploadsList">(.*?)<\/table>/ms');
		}
		else
		{
			$result = self::search($searchText, $page);
			return self::parse($result, '/<div class="videoYContainer clearfix">(.*?)<div class="pagination">/ms');
		}
	}


	
	private static function searchYourOwn($searchText, $page, $pageSize, $authData)
	{
		$authData = base64_decode($authData);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://vids.myspace.com/index.cfm?fuseaction=vids.myVideos&page=".($page - 1));
		curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
		curl_setopt($ch, CURLOPT_COOKIE, $authData);
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , TRUE);

		$body = curl_exec($ch);
		curl_close($ch);
		
		return $body;
	}
}
?>