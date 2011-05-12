<?php
/**
 * @package infra
 * @subpackage Media
 */
class KMediaInfoMediaParser extends KBaseMediaParser 
{
	protected $cmdPath;
	
	/**
	 * @param string $filePath
	 * @param string $cmdPath
	 */
	public function __construct($filePath, $cmdPath = 'mediainfo')
	{
		$this->cmdPath = $cmdPath;
		parent::__construct($filePath);
	}
	
	protected function getCommand() 
	{
		return "{$this->cmdPath} \"{$this->filePath}\"";
	}
	
	protected function parseOutput($output) 
	{
		$tokenizer = new KStringTokenizer ( $output, "\t\n" );
		$mediaInfo = new KalturaMediaInfo();
		$mediaInfo->rawData = $output;
		
		$fieldCnt = 0;
		$section = "general";
		$sectionID = 0;
		while ($tokenizer->hasMoreTokens()) 
		{
			$tok = strtolower(trim($tokenizer->nextToken()));
			if (strrpos($tok, ":") == false) 
			{
				$sectionID = strchr($tok,"#");
				if($sectionID) {
					$sectionID = trim($sectionID,"#"); 
				}
				else
					$sectionID = 0;

					if(strstr($tok,"general")==true)
						$section = "general";
					else if(strstr($tok,"video")==true)
						$section = "video";
					else if(strstr($tok,"audio")==true)
						$section = "audio";
//					else if(strstr($tok,"image")==true)
//						$section = "image";
					else
						$section = $tok;
			} 
			else if($sectionID<=1)
			{
				$key = trim(substr($tok, 0, strpos($tok, ":")));
				$val = trim(substr(strstr($tok, ":"), 1));
				switch ($section) 
				{
					case "general" :
						$this->loadContainerSet($mediaInfo, $key, $val);
						break;
					case "video" :
						$this->loadVideoSet($mediaInfo, $key, $val);
						break;
					case "audio" :
						$this->loadAudioSet($mediaInfo, $key, $val);
						break;
				}
				$fieldCnt++;
			}
		}

			// On no-content return null
		if($fieldCnt<5)
			return null; 
		else 
			return $mediaInfo;
	}
	
	/**
	 * @param $mediaInfo
	 * @param string $key
	 * @param string $val
	 */
	private function loadAudioSet(KalturaMediaInfo $mediaInfo, $key, $val) 
	{
		switch($key) 
		{
			case "format":
				$mediaInfo->audioFormat = $val;
				break;
			case "codec id":
				$mediaInfo->audioCodecId = $val;
				break;
			case "duration":
				$mediaInfo->audioDuration = $this->convertDuration2msec($val);
				break;
			case "bit rate":
				$mediaInfo->audioBitRate = $this->convertValue2kbits($this->trima($val));
				break;
			case "bit rate mode": 
				$mediaInfo->audioBitRateMode; // FIXME
				break;
			case "channel(s)":
				$mediaInfo->audioChannels = (int)$this->trima($val);
				break;
			case "sampling rate":
				$mediaInfo->audioSamplingRate = (float)$this->trima($val);
				if ($mediaInfo->audioSamplingRate < 1000)
					$mediaInfo->audioSamplingRate *= 1000;
				break;
			case "resolution":
				$mediaInfo->audioResolution = (int)$this->trima($val);
				break;
		}
	}

	/**
	 * @param $mediaInfo
	 * @param string $key
	 * @param string $val
	 */
	private function loadVideoSet(KalturaMediaInfo $mediaInfo, $key, $val) 
	{
		switch($key) 
		{
			case "format":
				$mediaInfo->videoFormat = $val;
				break;
			case "codec id":
				$mediaInfo->videoCodecId = $val;
				break;
			case "duration":
				$mediaInfo->videoDuration = $this->convertDuration2msec($val);
				break;
			case "bit rate":
				$mediaInfo->videoBitRate = $this->convertValue2kbits($this->trima($val));
				break;
			case "bit rate mode": 
				$mediaInfo->videoBitRateMode; // FIXME
				break; 
			case "width":
				$mediaInfo->videoWidth = (int)$this->trima($val);
				break;
			case "height":
				$mediaInfo->videoHeight = (int)$this->trima($val);
				break;
			case "frame rate":
				$mediaInfo->videoFrameRate = (float)$this->trima($val);
				break;
			case "display aspect ratio":
				$val = $this->trima($val);
				if(strstr($val, ":")==true){
					$darW = trim(substr($val, 0, strpos($val, ":")));
					$darH = trim(substr(strstr($val, ":"),1));
					if($darW>0)
						$mediaInfo->videoDar = $darW / $darH;
					else
						$mediaInfo->videoDar = null;
				}
				else if(strstr($val, "/")==true){
					$darW = trim(substr($val, 0, strpos($val, "/")));
					$darH = trim(substr(strstr($val, "/"),1));
					if($darW>0)
						$mediaInfo->videoDar = $darW / $darH;
					else
						$mediaInfo->videoDar = null;
				}
				else if($val) {
					$mediaInfo->videoDar = (float)$val;
				}
				break;
			case "rotation":
				$mediaInfo->videoRotation = (int)$this->trima($val);
				break;
			case "scan type":
				$scanType = $this->trima($val);
				if($scanType!="progressive") {
					$mediaInfo->scanType=1;
				}
				else {
					$mediaInfo->scanType=0;
				}
				break;
		}
	}

	/**
	 * @param $mediaInfo
	 * @param $key
	 * @param $val
	 */
	private function loadContainerSet(KalturaMediaInfo $mediaInfo, $key, $val) 
	{
		switch($key) 
		{
			case "file size":
				$mediaInfo->fileSize = $this->convertValue2kbits($this->trima($val));
				break;
			case "format":
				$mediaInfo->containerFormat = $val;
				break;
			case "codec id":
				$mediaInfo->containerId = $val;
				break;
			case "duration":
				$mediaInfo->containerDuration = $this->convertDuration2msec($val);
				break;
			case "overall bit rate":
				$mediaInfo->containerBitRate = $this->convertValue2kbits($this->trima($val));
				break;
		}
	}
	
	private function trima($str)
	{
		$str = str_replace(array("\n", "\r", "\t", " ", "\o", "\xOB"), '', $str);
		return $str;
	}
	
	private function convertDuration2msec($str)
	{
		preg_match_all("/(([0-9]*)h ?)?(([0-9]*)mn ?)?(([0-9]*)s ?)?(([0-9]*)ms ?)?/",
			$str, $res);
			
		$hour = @$res[2][0];
		$min  = @$res[4][0];
		$sec  = @$res[6][0];
		$msec = @$res[8][0];
		
		$rv = ($hour*3600 + $min*60 + $sec)*1000 + $msec;
		
		return (int)$rv;
	}
	
	private function convertValue2kbits($str)
	{
		preg_match_all("/(([0-9.]*)b ?)?(([0-9.]*)k ?)?(([0-9.]*)m ?)?(([0-9.]*)g ?)?/",
			$str, $res);

		if(@$res[2][0]!=="")
			$kbps=@$res[2][0]/1024;
		else if(@$res[4][0]!=="")
			$kbps=@$res[4][0];
		else if(@$res[6][0]!=="")
			$kbps=@$res[6][0]*1024;
		else if(@$res[8][0]!=="")
			$kbps=@$res[8][0]*1048576;
			
		return (float)$kbps;
	}
}