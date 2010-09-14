<?php

class youtubeRedirectAction extends kalturaAction
{
	public function execute()
	{
		$error = false;
		
		// youtube video id
		$itemId = $this->getRequestParameter("itemId");
		
		$url = "http://www.youtube.com/watch?v=". $itemId;
		
		$sourceUrl = "";
		
		$retries = 2;
		while($retries--)
		{
			$content = kFile::downloadUrlToString($url, 3);
			
			if (preg_match('/swfArgs.*?\{.*?, "t":\s*"(.*?)"/s', $content, $timestampMatch))
			//if (preg_match('/swfArgs.*?\{.*?,t:\'(.*?)\'/', $htmlPage, $timestampMatch))
			{
				$fmt_url = "";
				//"fmt_map": "35/640000/9/0/115,18/512000/9/0/115,34/0/9/0/115,5/0/7/0/0"
				if (preg_match('/swfArgs.*?\{.*?, "fmt_map":\s*"(.*?)"/s', $content, $fmt_map))
				{
					$fmt_map_array = explode(",", urldecode($fmt_map[1]));
					$fmt_details = explode("/", $fmt_map_array[0]);
		
					if ($fmt_details[0])
						$fmt_url = "&fmt=".$fmt_details[0];
				}
		
				//var swfArgs = {hl:'en',video_id:'F924-D-g5t8',l:'24',t:'OEgsToPDskL9BIntclUB-PPzMEpVQKo8',sk:'xXvbHpmFGQKgv-b9__DkgwC'};
				$tId = $timestampMatch[1];
				$sourceUrl = "http://youtube.com/get_video?video_id=".$itemId."&t=$tId$fmt_url";
			}
			else
			{
				KalturaLog::log ("youtubeRedirectAction $retries $url $content");
			}
		}
		
		if ($sourceUrl)
		{
			if (!$retries)
				KalturaLog::log ("youtubeRedirectAction retry successful $url");
		}
		else
			die;
		
		// get only the headers and don't follow the location redirect
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $sourceUrl);
		curl_setopt($ch, CURLOPT_USERAGENT, "curl/7.11.1");
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_NOBODY, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$headers = curl_exec($ch);		

		// get the location header
		$found1 = preg_match_all('/Location: (.*)/', $headers, $swfUrlResult);
		$swfUrl = str_replace('Location: ', '',$swfUrlResult[0][count($swfUrlResult[0])-1]);
		//echo $swfUrl; die();
		$this->redirect($swfUrl);

	}

}
