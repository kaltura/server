<?php
/**
 * @package server-infra
 * @subpackage Media
 */
abstract class KBaseMediaParser
{
	const MEDIA_PARSER_TYPE_MEDIAINFO = '0';
	const MEDIA_PARSER_TYPE_FFMPEG = '1';
	
	const ERROR_NFS_FILE_DOESNT_EXIST = 21; // KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST
	const ERROR_EXTRACT_MEDIA_FAILED = 31; // KalturaBatchJobAppErrors::EXTRACT_MEDIA_FAILED
	
	/**
	 * @var string
	 */
	protected $filePath;
	
	/**
	 * @param string $type
	 * @param string $filePath
	 * @param KSchedularTaskConfig $taskConfig
	 * @return KBaseMediaParser
	 */
	public static function getParser($type, $filePath, KSchedularTaskConfig $taskConfig, KalturaBatchJob $job)
	{
		switch($type)
		{
			case self::MEDIA_PARSER_TYPE_MEDIAINFO:
				return new KMediaInfoMediaParser($filePath, $taskConfig->params->mediaInfoCmd);
				
			case self::MEDIA_PARSER_TYPE_FFMPEG:
				return new KFFMpegMediaParser($filePath, $taskConfig->params->FFMpegCmd);
				
			default:
				return KalturaPluginManager::loadObject('KBaseMediaParser', $type, array($job, $taskConfig));
		}
	}
	
	/**
	 * @param string $filePath
	 */
	public function __construct($filePath)
	{
		if (!file_exists($filePath))
			throw new kApplicativeException(KBaseMediaParser::ERROR_NFS_FILE_DOESNT_EXIST, "File not found at [$filePath]");

		$this->filePath = $filePath;
	}
	
	/**
	 * @return KalturaMediaInfo
	 */
	public function getMediaInfo()
	{
		$output = $this->getRawMediaInfo();
		return $this->parseOutput($output);
	}

	/**
	 * @param string $filePath
	 */
	public function setFilePath($filePath)
	{
		$this->filePath = $filePath;
	}

	/**
	 * @return string
	 */
	public function getRawMediaInfo()
	{
		$cmd = $this->getCommand();
		KalturaLog::debug("Executing '$cmd'");
		$output = shell_exec($cmd);
		if (trim($output) === "")
			throw new kApplicativeException(KBaseMediaParser::ERROR_EXTRACT_MEDIA_FAILED, "Failed to parse media using " . get_class($this));
			
		return $output;
	}
	
	/**
	 * 
	 * @param KalturaMediaInfo $mediaInfo
	 * @return KalturaMediaInfo
	 */
	public static function removeUnsetFields(KalturaMediaInfo $mediaInfo)
	{
		foreach($mediaInfo as $key => $value) {
           	if(!isset($value)){
          		unset($mediaInfo->$key);
           	}
       	}
		return $mediaInfo;
	}
	
	/**
	 * 
	 * @param KalturaMediaInfo $mIn
	 * @param KalturaMediaInfo $mOut
	 * @return KalturaMediaInfo
	 */
	public static function copyFields(KalturaMediaInfo $mIn, KalturaMediaInfo $mOut)
	{
		foreach($mIn as $key => $value) {
			$mOut->$key = $mIn->$key;
       	}
		return $mOut;
	}

	/**
	 * 
	 * @param KalturaMediaInfo $m1
	 * @param KalturaMediaInfo $m2
	 */
	public static function compareFields($m1, $m2)
	{
		$fields = array(
"fileSize",
"containerFormat",
"containerId",
"containerDuration",
"containerBitRate",

"audioFormat",
"audioCodecId",
"audioDuration",
"audioBitRate",
"audioChannels",
"audioSamplingRate",
"audioResolution",

"videoFormat",
"videoCodecId",
"videoDuration",
"videoBitRate",
"videoBitRateMode",
"videoWidth",
"videoHeight",
"videoFrameRate",
"videoDar",
"videoRotation",
"scanType",
		);

$container_format_synonyms = array(
	array("mp4","mpeg4"),
	array("flv","sorenson spark","flash video"),
	array("asf","windows media"),
	array("mpeg","mpegps"),
	array("mpeg audio","mp3"),
);
$video_format_synonyms = array(
	array("h264","avc","avc1"),
	array("mp4","mpeg4"),
	array("mpeg4 visual","mpeg4"),
	array("flv","sorenson spark","flash video"),
	array("vc1","wmv3"),
	array("mpeg video","mpeg2video","mpeg1video","mpegps"),
	array("intermediate codec","apple intermediate codec","icod","aic"),
	array("vp6","vp6f"),
	array("ms video","msvideo1"),
);
$video_codec_id_synonyms = array(
	array("4","[0][0][0][0]"),
	array("2","[0][0][0][0]","[2][0][0][0]"),
	array("20","mp4v"),
	array("v_vp8","[0][0][0][0]"),
	array("wmv3","[0][0][0][0]"),
);
$audio_format_synonyms = array(
	array("mpeg audio","mp3", "mp2"),
	array("wma","wmapro"),
	array("wma","wmav2","a[1][0][0]"),
	array("pcm","pcm_s16le","pcm_s16be"),
	array("2","[0][0][0][0]"),
);
$audio_codec_id_synonyms = array(
	array("aac","40","mp4a"),
	array("161","a[1][0][0]"),
	array("50","p[0][0][0]"),
	array("162","b[1][0][0]"),
	array("55","u[0][0][0]"),
	array("2","[0][0][0][0]"),
	array("a_vorbis","[0][0][0][0]"),
	array("5","6","[0][0][0][0]"),
	array("1","[1][0][0][0]"),
	array("4","[4][0][0][0]"),
);

		if(!isset($m1) && !isset($m2)) {
			return("(missing,missing)");
		}
		else if(!isset($m1)) {
			return("(missing,exists)");
		}
		else if(!isset($m2)) {
			return("(exists,missing)");
		}
		
		$msg = null;
		foreach ($fields as $f){
			if(isset($m1->$f) && isset($m2->$f)){
				$f1 = str_replace(array(".","-"),array("",""), $m1->$f);
				$f2 = str_replace(array(".","-"),array("",""), $m2->$f);
				if($f1==$f2)
					continue;
				
				if(is_numeric($m1->$f) && is_numeric($m1->$f)){
					if($m1->$f>0) {
						if(abs(1-$m2->$f/$m1->$f)<0.01)
							continue;
					}
					if(stristr($f, "duration")!=false){
						$a1 = $m1->$f - $m1->$f%1000;
						$a2 = $m2->$f - $m2->$f%1000;
						if($a1==$a2)
							continue;
					}
				}
				
				if($f=="containerFormat" && self::isSynonym($f1, $f2, $container_format_synonyms)==true){
					continue;
				}
				
				if($f=="videoFormat" && self::isSynonym($f1, $f2, $video_format_synonyms)==true){
					continue;
				}
				
				if($f=="videoCodecId" && self::isSynonym($f1, $f2, $video_codec_id_synonyms)==true){
					continue;
				}
				
				if($f=="audioFormat" && self::isSynonym($f1, $f2, $audio_format_synonyms)==true){
					continue;
				}
				
				if($f=="audioCodecId" && self::isSynonym($f1, $f2, $audio_codec_id_synonyms)==true){
					continue;
				}
				
				$msg.="$f(".$m1->$f.",".$m2->$f."),";
			}
			else if(!(isset($m1->$f) && isset($m2->$f))){
				continue;
			}
			else if(isset($m1->$f)) {
				$msg.="$f(".$m1->$f.",missing),";
			}
			else {
				$msg.="$f(missing,".$m2->$f."),";	
			}
		}
		return ($msg);
	}
	
	/**
	 * 
	 * @param unknown_type $f1
	 * @param unknown_type $f2
	 * @param unknown_type $synonyms
	 * @return boolean
	 */
	private static function isSynonym($f1, $f2, $synonyms)
	{
		foreach($synonyms as $syn){
			if(in_array($f1, $syn) && in_array($f2, $syn)){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 
	 * @param KalturaMediaInfo $mediaInfo
	 * @return boolean
	 */
	public static function isVideoSet(KalturaMediaInfo $mediaInfo)
	{
		if(isset($mediaInfo->videoCodecId))
			return true;
		if(isset($mediaInfo->videoFormat))
			return true;
		if(isset($mediaInfo->videoDuration))
			return true;
		if(isset($mediaInfo->videoBitRate))
			return true;
		
		
		if(isset($mediaInfo->videoWidth))
			return true;
		if(isset($mediaInfo->videoHeight))
			return true;
		if(isset($mediaInfo->videoFrameRate))
			return true;
		if(isset($mediaInfo->videoDar))
			return true;
		
		return false;
	}
	
	/**
	 * 
	 * @param KalturaMediaInfo $mediaInfo
	 * @return boolean
	 */
	public static function isAudioSet(KalturaMediaInfo $mediaInfo)
	{
		if(isset($mediaInfo->audioCodecId))
			return true;
		if(isset($mediaInfo->audioFormat))
			return true;
		if(isset($mediaInfo->audioDuration))
			return true;
		if(isset($mediaInfo->audioBitRate))
			return true;
	
		if(isset($mediaInfo->audioSamplingRate))
			return true;
		if(isset($mediaInfo->audioResolution))
			return true;
		if(isset($mediaInfo->audioChannels))
			return true;
		
		return false;
	}
	
	/**
	 * @return string
	 */
	protected abstract function getCommand();
	
	/**
	 * 
	 * @param string $output
	 * @return KalturaMediaInfo
	 */
	protected abstract function parseOutput($output);
}

