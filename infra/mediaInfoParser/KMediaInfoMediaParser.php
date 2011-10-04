<?php
/**
 * @package infra
 * @subpackage Media
 */
class KMediaInfoMediaParser extends KBaseMediaParser 
{
	protected $cmdPath;
	
	const SrteamGeneral = "general";
	const SrteamVideo = "video";
	const SrteamAudio = "audio";
	const SrteamImage = "image";
	
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
		$output = kXml::stripXMLInvalidChars($output);
		$tokenizer = new KStringTokenizer ( $output, "\t\n" );
		$mediaInfo = new KalturaMediaInfo();
		$mediaInfo->rawData = $output;
		
		$fieldCnt = 0;
		$section = self::SrteamGeneral;
		$sectionID = 0;
		$mediaInfo->streamArray = array();
		$streamMediaInfo = null;
		while ($tokenizer->hasMoreTokens()) 
		{
			$tok = strtolower(trim($tokenizer->nextToken()));
			if (strrpos($tok, ":") == false) 
			{
				if(isset($streamMediaInfo))
					$mediaInfo->streamArray[$section][]=$streamMediaInfo;
				$streamMediaInfo = new KalturaMediaInfo();
				$sectionID = strchr($tok,"#");
				if($sectionID) {
					$sectionID = trim($sectionID,"#"); 
				}
				else
					$sectionID = 0;

					if(strstr($tok,self::SrteamGeneral)==true)
						$section = self::SrteamGeneral;
					else if(strstr($tok,self::SrteamVideo)==true)
						$section = self::SrteamVideo;
					else if(strstr($tok,self::SrteamAudio)==true)
						$section = self::SrteamAudio;
//					else if(strstr($tok,"image")==true)
//						$section = "image";
					else
						$section = $tok;
			} 
			else if($sectionID<=1)
			{
				self::loadStreamMedia($mediaInfo, $section, $tok);
				$fieldCnt++;
			}
			self::loadStreamMedia($streamMediaInfo, $section, $tok);
		}

		if(isset($streamMediaInfo))
			$mediaInfo->streamArray[$section][]=$streamMediaInfo;
		
			// On no-content return null
		if($fieldCnt<5)
			return null; 
		else 
			return $mediaInfo;
	}

	/**
	 * @param $mediaInfo
	 * @param string $section
	 * @param string $tok
	 */
	private static function loadStreamMedia(KalturaMediaInfo $mediaInfo, $section, $tok) 
	{
		$key = trim(substr($tok, 0, strpos($tok, ":")));
		$val = trim(substr(strstr($tok, ":"), 1));
		switch ($section) 
		{
			case self::SrteamGeneral :
				self::loadContainerSet($mediaInfo, $key, $val);
				break;
			case self::SrteamVideo :
				self::loadVideoSet($mediaInfo, $key, $val);
				break;
			case self::SrteamAudio :
				self::loadAudioSet($mediaInfo, $key, $val);
				break;
		}
	}
	
	/**
	 * @param $mediaInfo
	 * @param string $key
	 * @param string $val
	 */
	private static function loadAudioSet(KalturaMediaInfo $mediaInfo, $key, $val) 
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
				$mediaInfo->audioDuration = self::convertDuration2msec($val);
				break;
			case "bit rate":
				$mediaInfo->audioBitRate = self::convertValue2kbits(self::trima($val));
				break;
			case "bit rate mode": 
				$mediaInfo->audioBitRateMode; // FIXME
				break;
			case "channel(s)":
				$mediaInfo->audioChannels = (int)self::trima($val);
				break;
			case "sampling rate":
				$mediaInfo->audioSamplingRate = (float)self::trima($val);
				if ($mediaInfo->audioSamplingRate < 1000)
					$mediaInfo->audioSamplingRate *= 1000;
				break;
			case "resolution":
				$mediaInfo->audioResolution = (int)self::trima($val);
				break;
		}
	}

	/**
	 * @param $mediaInfo
	 * @param string $key
	 * @param string $val
	 */
	private static function loadVideoSet(KalturaMediaInfo $mediaInfo, $key, $val) 
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
				$mediaInfo->videoDuration = self::convertDuration2msec($val);
				break;
			case "bit rate":
				$mediaInfo->videoBitRate = self::convertValue2kbits(self::trima($val));
				break;
			case "bit rate mode": 
				$mediaInfo->videoBitRateMode; // FIXME
				break; 
			case "width":
				$mediaInfo->videoWidth = (int)self::trima($val);
				break;
			case "height":
				$mediaInfo->videoHeight = (int)self::trima($val);
				break;
			case "frame rate":
				$mediaInfo->videoFrameRate = (float)self::trima($val);
				break;
			case "display aspect ratio":
				$val = self::trima($val);
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
				$scanType = self::trima($val);
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
	private static function loadContainerSet(KalturaMediaInfo $mediaInfo, $key, $val) 
	{
		switch($key) 
		{
			case "file size":
				$mediaInfo->fileSize = self::convertValue2kbits(self::trima($val));
				break;
			case "format":
				$mediaInfo->containerFormat = $val;
				break;
			case "codec id":
				$mediaInfo->containerId = $val;
				break;
			case "duration":
				$mediaInfo->containerDuration = self::convertDuration2msec($val);
				break;
			case "overall bit rate":
				$mediaInfo->containerBitRate = self::convertValue2kbits(self::trima($val));
				break;
		}
	}
	
	private static function trima($str)
	{
		$str = str_replace(array("\n", "\r", "\t", " ", "\o", "\xOB"), '', $str);
		return $str;
	}
	
	private static function convertDuration2msec($str)
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
	
	private static function convertValue2kbits($str)
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