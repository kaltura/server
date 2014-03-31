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
	public static function compareFields(KalturaMediaInfo $m1, KalturaMediaInfo $m2)
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
$h264_synonyms = array("h264","avc","avc1");
$mp4_synonyms = array("mp4","mpeg4");
$mp4visual_synonyms = array("mpeg4 visual","mpeg4");
$flv_synonyms = array("flv","sorenson spark","flash video");
$mp3_synonyms = array("mpeg audio","mp3");
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
				}
				if($f=="videoFormat") {
					if(in_array($f1, $h264_synonyms) && in_array($f2, $h264_synonyms))
						continue;
					if(in_array($f1, $mp4_synonyms) && in_array($f2, $mp4_synonyms))
						continue;
					if(in_array($f1, $mp4visual_synonyms) && in_array($f2, $mp4visual_synonyms))
						continue;
					if(in_array($f1, $flv_synonyms) && in_array($f2, $flv_synonyms))
						continue;
				}
				if($f=="containerFormat"){
					if(in_array($f1, $mp4_synonyms) && in_array($f2, $mp4_synonyms))
						continue;
					if(in_array($f1, $flv_synonyms) && in_array($f2, $flv_synonyms))
						continue;
				}
				
				if($f=="audioFormat"){
					if(in_array($f1, $mp3_synonyms) && in_array($f2, $mp3_synonyms))
						continue;
				}
				$msg.="$f(".$m1->$f.",".$m2->$f."),";
			}
			else if(!(isset($m1->$f) && isset($m2->$f))){
				continue;
			}
		}
		if(isset($msg)) {
			KalturaLog::log($msg);
		}
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

