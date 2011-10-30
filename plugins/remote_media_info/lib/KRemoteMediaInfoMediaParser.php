<?php
/**
 * @package plugins.remoteMediaInfo
 * @subpackage lib
 */
class KRemoteMediaInfoMediaParser  extends KMediaInfoMediaParser
{
	protected $taskConfig;
	
	/**
	 * @param string $filePath
	 * @param KSchedularTaskConfig $taskConfig
	 */
	public function __construct($job, KSchedularTaskConfig $taskConfig)
	{
		$this->taskConfig = $taskConfig;
		$this->job = $job;
		
		$errStr=null;
		if(!$taskConfig->params->AlldigitalApiUrl)
			$errStr="AlldigitalApiUrl";
		if(!$taskConfig->params->AlldigitalApiUser){
			if($errStr) 
				$errStr.=",";
			$errStr.="AlldigitalApiUser";
		}
		if(!$taskConfig->params->AlldigitalApiPassword){
			if($errStr) 
				$errStr.=",";
			$errStr.="AlldigitalApiPassword";
		}
		
//KalturaLog::info("taskConfig-->".print_r($taskConfig,true));
//KalturaLog::info("job-->".print_r($job,true));
		if($errStr) {
			KalturaLog::info("AlldigitalApi: missing credentials - $errStr");
			throw new KOperationEngineException("AlldigitalApi: missing credentials - $errStr");
		}
	}
	
	/**
	 * @return string
	 */
	public function getRawMediaInfo()
	{
		$adApi=new ADContentAPI;
		$adApi->setUser($this->taskConfig->params->AlldigitalApiUser);
		$adApi->setPassw($this->taskConfig->params->AlldigitalApiPassword);
		$adApi->setUrl($this->taskConfig->params->AlldigitalApiUrl);
KalturaLog::info("adApi-->".print_r($adApi,true));
		
		$fileName=$this->job->data->srcFileSyncLocalPath;
			// Calls from PostConvertJob represent 'output' AD location,
			// while calls from ExtractMediaJob represnt 'input' AD location
		if($this->job->data instanceof KalturaPostConvertJobData){
			$location='output';
			$folder=str_replace ("-", "/", $this->job->data->srcFileSyncRemoteUrl);
			$fileName="$folder/encode/$fileName";
		}
		else // if($this->job->data instanceof KalturaExtractMediaJobData)
			$location='input';
		$mi=$adApi->MediaInfoToText($location,$fileName, $err);
		if(!isset($mi)){
			KalturaLog::info("AlldigitalApi: mediainfo failure(location:$location,fileName:$fileName) - $err");
			throw new KOperationEngineException("AlldigitalApi: mediainfo failure(location:$location,fileName:$fileName) - $err");
		}
KalturaLog::info("mediaInfo-->\n".$mi);

		return $mi;
		$mediaInfoSample = "
General
Complete name                    : C:\\xampp\htdocs\mvdr6t454m.mov
Format                           : MPEG-4
Format profile                   : QuickTime
Codec ID                         : qt  
File size                        : 66.6MiB 
Duration                         : 2mn 25s
Overall bit rate                 : 3 851 Kbps
Encoded date                     : UTC 2009-08-21 18:45:38
Tagged date                      : UTC 2009-08-21 18:45:40
Writing library                  : Apple QuickTime
com.apple.finalcutstudio.media.u : 7ABA86C3-580D-4364-970F-5C8103CC66C9

Video
ID                               : 1
Format                           : AVC
Format/Info                      : Advanced Video Codec
Format profile                   : Main@L4.0
Format settings, CABAC           : No
Format settings, ReFrames        : 2 frames
Codec ID                         : vp6
Codec ID/Info                    : Advanced Video Coding
Duration                         : 2mn 25s
Bit rate mode                    : Variable
Bit rate                         : 721 Kbps
Width                            : 1 280 pixels
Height                           : 720 pixels
Display aspect ratio             : 16:9
Frame rate mode                  : Constant
Frame rate                       : 29.970 fps
Resolution                       : 24 bits
Colorimetry                      : 4:2:0
Scan type                        : Progressive
Bits/(Pixel*Frame)               : 0.060
Stream size                      : 64.4 MiB (97%)
Encoded date                     : UTC 2009-08-21 18:45:37
Tagged date                      : UTC 2009-08-21 18:45:40

Audio
ID                               : 3
Format                           : AAC
Format/Info                      : Advanced Audio Codec
Format version                   : Version 4
Format profile                   : LC
Format settings, SBR             : No
Codec ID                         : 40
Duration                         : 2mn 25s
Bit rate mode                    : Constant
Bit rate                         : 124 Kbps
Nominal bit rate                 : 128 Kbps
Channel(s)                       : 2 channels
Channel positions                : L R
Sampling rate                    : 48.0 KHz
Resolution                       : 16 bits
Stream size                      : 2.15 MiB (3%)
Encoded date                     : UTC 2009-08-21 18:45:38
Tagged date                      : UTC 2009-08-21 18:45:40

Menu
ID                               : 2
Encoded_Date                     : UTC 2009-08-21 18:45:37
Tagged_Date                      : UTC 2009-08-21 18:45:40
BitRate_Mode                     : CBR

";
	
				
		return ($mediaInfoSample);
	}
	
}
