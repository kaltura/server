<?php
/**
 * @package server-infra
 * @subpackage Media
 */
class KFFMpegMediaParser extends KBaseMediaParser
{
	protected $cmdPath;
	protected $ffprobeBin;
	protected $ffprobeBinCmd;
	
	public $checkScanTypeFlag=true;
	
	/**
	 * @param string $filePath
	 * @param string $cmdPath
	 */
	public function __construct($filePath, $ffmpegBin=null, $ffprobeBin=null)
	{
		if(isset($ffmpegBin)){
			$this->cmdPath = $ffmpegBin;
		}
		else if(kConf::hasParam(kFfmpegUtils::FFMPEG_PATH_CONF_NAME)) {
			$this->cmdPath = kConf::get(kFfmpegUtils::FFMPEG_PATH_CONF_NAME);
		}
		else{
			$this->cmdPath = "ffmpeg";
		}
		
		if(isset($ffprobeBin)){
			$this->ffprobeBin = $ffprobeBin;
		}
		else if (kConf::hasParam('bin_path_ffprobe')) {
			$this->ffprobeBin = kConf::get('bin_path_ffprobe');
		}
		else{
			$this->ffprobeBin = "ffprobe";
		}
		
		$this->ffprobeBinCmd = $this->ffprobeBin;
		$resolvedFilePath  = kFile::realPath($filePath);
		kBatchUtils::addReconnectParams("http", $resolvedFilePath, $this->ffprobeBin);
		kBatchUtils::addReconnectParams("http", $resolvedFilePath, $this->cmdPath);
		
		if(strstr($filePath, "http")===false) {
			if (!kFile::checkFileExists($filePath))
				throw new kApplicativeException(KBaseMediaParser::ERROR_NFS_FILE_DOESNT_EXIST, "File not found at [$filePath]");
		}
		parent::__construct($filePath);
	}
	
	/**
	 * @return string
	 */
	protected function getCommand($filePath=null)
	{
		if(!isset($filePath)) $filePath=$this->filePath;
		$filePath = kFile::realPath($filePath);
		
		if(isset($this->encryptionKey))
			return "{$this->ffprobeBin} -decryption_key {$this->encryptionKey} -i \"{$filePath}\" -show_streams -show_format -show_programs -v quiet -show_data  -print_format json";
		else	
			return "{$this->ffprobeBin} -i \"{$filePath}\" -show_streams -show_format -show_programs -v quiet -show_data  -print_format json";
	}
	
	/**
	 * @return string
	 */
	public function getRawMediaInfo($filePath=null)
	{
		if(!isset($filePath)) $filePath=$this->filePath;
		$filePath = kFile::realPath($filePath);
		
		$cmd = $this->getCommand($filePath);
		$output = kExecWrapper::shell_exec($cmd);
		if (trim($output) === "")
			throw new kApplicativeException(KBaseMediaParser::ERROR_EXTRACT_MEDIA_FAILED, "Failed to parse media using " . get_class($this));
			
		return $output;
	}
	
	/**
	 * 
	 * @param string $output
	 * @return KalturaMediaInfo
	 */
	protected function parseOutput($output)
	{
		$outputlower = strtolower($output);
		$jsonObj = json_decode($outputlower);
			// Check for json decode errors caused by inproper utf8 encoding.
		if(json_last_error()!=JSON_ERROR_NONE) $jsonObj = json_decode(utf8_encode($outputlower));
		if(!(isset($jsonObj) && isset($jsonObj->format))){
			/*
			 * For ARF (webex) files - simulate container ID and format.
			 * On no-content return null
			 */
			if(strstr($this->filePath,".arf")){
				$mediaInfo = new KalturaMediaInfo();
				$mediaInfo->containerFormat = "arf";
				$mediaInfo->containerId = "arf";
				$mediaInfo->fileSize = round(kFile::fileSize($this->filePath)/1024);
				return $mediaInfo;
			}
			return null;
		}
		
		$mediaInfo = new KalturaMediaInfo();
		$mediaInfo->rawData = $output;
		$this->parseFormat($jsonObj->format, $mediaInfo);
		if(isset($jsonObj->streams) && count($jsonObj->streams)>0){
			$this->parseStreams($jsonObj->streams, $mediaInfo);
		}

//		list($silenceDetect, $blackDetect) = self::checkForSilentAudioAndBlackVideo($this->cmdPath, $this->filePath, $mediaInfo);
		if(isset($this->checkScanTypeFlag) && $this->checkScanTypeFlag==true)
			$mediaInfo->scanType = self::checkForScanType($this->cmdPath, $this->ffprobeBinCmd, $this->filePath);
		else
			$mediaInfo->scanType = 0; // Progressive
		// mov,mp4,m4a,3gp,3g2,mj2 to check is format inside
		if(in_array($mediaInfo->containerFormat, array("mov","mp4","m4a","3gp","3g2","mj2")) && isset($this->ffprobeBin)){
			$mediaInfo->isFastStart = self::checkForFastStart($this->ffprobeBinCmd, $this->filePath);
		}
		
		/*
		 * Detect WVC1 files with 'Progressive Segmented' mode. FFmpeg 2.6 (and earlier) cannot handle them.
		 * To be handled by mencoder in auto-inter-src mode
		 */
		if(in_array($mediaInfo->videoCodecId,array("wvc1","wmv3"))){
			$cmd = "$this->cmdPath -i \"$this->filePath\" 2>&1 ";
			$output = kExecWrapper::shell_exec($cmd);
			if(strstr($output,"Progressive Segmented")){
				if(isset($mediaInfo->contentStreams) && count($mediaInfo->contentStreams['video'])>0){
					$mediaInfo->contentStreams['video'][0]->progressiveSegmented=true;
				}
			}
		}
		/*
		 * On missing stream durations (mostly for Webm/VP8), retrieve dur from last frame
		 */
		{
				// if the conatiner duration is missing, instruct ffmpeg to reposition 
				// beyond the EOF (assuming that thw file is <100K sec ...),
				// in that case ffmpeg repositions to the last several frames
			if(($mediaInfo->containerDuration-500)<0) 
				$startFrom = 100000;
			else
				$startFrom = ($mediaInfo->containerDuration-500)/1000;
			
				// Select the streams that need duration data retrieval
			$strms = array();
			if($this->isAudioSet($mediaInfo) and $mediaInfo->audioDuration==0)
				$strms[] = "audio";
			if($this->isVideoSet($mediaInfo) and $mediaInfo->videoDuration==0)
				$strms[] = "video";
			
			switch(count($strms)){
				case 1:
					$durs=self::retrieveDurationFromLastFrame($this->cmdPath, $this->ffprobeBinCmd, $this->filePath, $startFrom,$strms[0]);
					break;
				case 2:
					$durs=self::retrieveDurationFromLastFrame($this->cmdPath, $this->ffprobeBinCmd, $this->filePath, $startFrom);
					break;
			}
			if(isset($durs)) {
				$calcContDur=0;
				foreach($durs as $strm=>$dur){
					$dur = round($dur*1000);
					switch($strm){
						case 'audio':
							$mediaInfo->audioDuration = $dur;
							break;
						case 'video':
							$mediaInfo->videoDuration = $dur;
							break;
					}
					$calcContDur = max($calcContDur, $dur);
				}
					// If needed, calculate container dur and br
				if($mediaInfo->containerDuration==0)
					$mediaInfo->containerDuration = $calcContDur;
				if($mediaInfo->containerBitRate==0 && $mediaInfo->containerDuration>0)
					$mediaInfo->containerBitRate=round(($mediaInfo->fileSize*8*1000)/$mediaInfo->containerDuration);
				KalturaLog::log(print_r($mediaInfo,1));
			}
		}
		$mediaInfo->contentStreams = json_encode($mediaInfo->contentStreams);
		$mediaInfo = self::convertToMediaInfoNames($mediaInfo);
//KalturaLog::log(print_r($mediaInfo,1));
//die;
		return $mediaInfo;
	}
	
	/**
	 * 
	 * @param $format - generated by ffprobe
	 * @param KalturaMediaInfo $mediaInfo
	 * @return KalturaMediaInfo
	 */
	protected function parseFormat($format, KalturaMediaInfo $mediaInfo)
	{
		$mediaInfo->fileSize = isset($format->size)? round($format->size/1024,2): null;
		$mediaInfo->containerFormat = 
			isset($format->format_name)? self::matchContainerFormat($this->filePath, trim($format->format_name)): null;
		if(isset($format->tags) && isset($format->tags->major_brand)){
			$mediaInfo->containerId = trim($format->tags->major_brand);
		}
		$mediaInfo->containerBitRate = isset($format->bit_rate)? round($format->bit_rate/1000,2): null;
			// If format duration is not set or zero'ed, 
			// try to retrieve duration from format/tag section 
		$mediaInfo->containerDuration = self::retrieveDuration($format);

		if(isset($format->tags->producer))
			$mediaInfo->producer = $format->tags->producer;
		return $mediaInfo;
	}
	
	/**
	 * 
	 * @param $streams - generated by ffprobe
	 * @param KalturaMediaInfo $mediaInfo
	 * @return KalturaMediaInfo
	 */
	protected function parseStreams($streams, KalturaMediaInfo $mediaInfo)
	{
	$vidCnt = 0;
	$audCnt = 0;
	$dataCnt = 0;
	$otherCnt = 0;
		foreach ($streams as $stream){
			$copyFlag = false;
			$mAux = new KalturaMediaInfo();
			$mAux->id = $stream->index;
			$mAux->codecType = $stream->codec_type;
			switch($stream->codec_type){
			case "video":
					/*
					 * SUP-18051,SUP-18025,SUP-17840,SUP-18018
					 * For audio-only MP3's/M4A's - prevent detecting of cover JPG/PNG as a video stream
					 */
				if(in_array($stream->codec_name, array('mjpeg','png'))
				&& (in_array($mediaInfo->containerFormat, array('mp3','mpeg audio','isom','mp4','mpeg4','mpeg-4','m4a'))
				||  in_array($mediaInfo->containerId, array('mp3','mpeg audio','isom','mp4','mpeg4','mpeg-4','m4a'))) ){
					break;
				}
				$this->parseVideoStream($stream, $mAux);
				if($vidCnt==0)
					$copyFlag=true;
				$vidCnt++;
				break;
			case "audio":
				$this->parseAudioStream($stream, $mAux);
				if($audCnt==0)
					$copyFlag=true;
				$audCnt++;
				break;
			case "data":
				$this->parseDataStream($stream, $mAux);
				if($dataCnt==0)
					$copyFlag=true;
				$dataCnt++;
				break;
			default:
				$otherCnt++;
				break;
			}
			self::removeUnsetFields($mAux);
			$mediaInfo->contentStreams[$stream->codec_type][] = $mAux;
			if($copyFlag){
				self::copyFields($mAux, $mediaInfo);
			}
		}
		$mediaInfo->id = null;
		if(isset($mediaInfo->codecType)) unset($mediaInfo->codecType);
		return $mediaInfo;
	}

	/**
	 * 
	 * @param string $srcFileName
	 * @param string $formatStr
	 * @return string
	 */
	private static function matchContainerFormat($srcFileName, $formatStr)
	{
		$extStr = pathinfo($srcFileName, PATHINFO_EXTENSION);
		$formatArr = explode(",", $formatStr);
		if(!empty($extStr) && strlen($extStr)>1) {
			foreach($formatArr as $frmt){
				if(strstr($extStr, $frmt)!=false || strstr($frmt, $extStr)!=false){
					return $frmt;
				}
			}
		}
		if(in_array("mp4", $formatArr))
			return "mp4";
		else
			return $formatArr[0];
	}
	
	/**
	 * 
	 * @param $stream - generated by ffprobe
	 * @param KalturaMediaInfo $mediaInfo
	 * @return KalturaMediaInfo
	 */
	protected function parseVideoStream($stream, KalturaMediaInfo $mediaInfo)
	{
		$mediaInfo->videoFormat = isset($stream->codec_name)? trim($stream->codec_name): null;
			// if 'codec_tag_string' empty or conatains crypthic '[0][0][0]..' (or like),
			// in such cases use 'codec_name' field
		if(!isset($stream->codec_tag_string) || strpos($stream->codec_tag_string, '][')==!null)
			$mediaInfo->videoCodecId = $mediaInfo->videoFormat;
		else $mediaInfo->videoCodecId = trim($stream->codec_tag_string);

			// If stream duration is not set or zero'ed, 
			// try to retrieve duration from stream/tag section 
		$mediaInfo->videoDuration = self::retrieveDuration($stream);

		$mediaInfo->videoBitRate = isset($stream->bit_rate)? round($stream->bit_rate/1000,2): null;
		$mediaInfo->videoBitRateMode; // FIXME
		$mediaInfo->videoWidth = isset($stream->width)? trim($stream->width): null;
		$mediaInfo->videoHeight = isset($stream->height)? trim($stream->height): null;
			/*
			 * Extract 'videoFrameRate' from the ffprobe::r_frame_rate.
			 * If the 'r_frame_rate' is missing or abnormally high (>120), 
			 * use 'avg_frame_rate'
			 */
		{
			$value = false;
			$mediaInfo->videoFrameRate = null;
			if(isset($stream->r_frame_rate)){
				$r_frame_rate = trim($stream->r_frame_rate);
				if(is_numeric($r_frame_rate))
					$value = $r_frame_rate;
				elseif (!kString::endsWith($r_frame_rate, "/0")) {
					//Avoid division by 0
					$value = eval("return ($r_frame_rate);");
				}
			}
			
			if(isset($value) && $value!=false && $value<120) {
				$mediaInfo->videoFrameRate = round($value, 3);
			}
			else if(isset($stream->avg_frame_rate) && $stream->avg_frame_rate>0) {
				$value = false;
				$avg_frame_rate = $stream->avg_frame_rate;
				//Avoid division by 0
				if(!kString::endsWith($avg_frame_rate, "/0")) {
					$value=eval("return ($avg_frame_rate);");
				}
				if(isset($value) && $value!=false && $value<120) {
					$mediaInfo->videoFrameRate = round($value, 3);
				}
			}
		}	
		$mediaInfo->videoDar = null;
		if(isset($stream->display_aspect_ratio)){
			$display_aspect_ratio = trim($stream->display_aspect_ratio);
			if(is_numeric($display_aspect_ratio)) {
				$mediaInfo->videoDar = $display_aspect_ratio;
			}
			else {
				$value = false;
				$darStr = str_replace(":", "/",$display_aspect_ratio);
				//Avoid division by 0
				if(!kString::endsWith($darStr, "/0")) {
					$value = eval("return ($darStr);");
				}
				if($value!=false) {
					$mediaInfo->videoDar = $value;
				}
			}
		}
			
		if(isset($stream->tags) && isset($stream->tags->rotate)){
			$mediaInfo->videoRotation = trim($stream->tags->rotate);
		}
		$mediaInfo->scanType = 0; // default 0/progressive
		
		$mediaInfo->matrixCoefficients = isset($stream->color_space)? trim($stream->color_space): null;
		$mediaInfo->colorTransfer = isset($stream->color_transfer)? trim($stream->color_transfer): null;
		$mediaInfo->colorPrimaries = isset($stream->color_primaries)? trim($stream->color_primaries): null;

		if(isset($stream->pix_fmt))
			self::parsePixelFormat($stream->pix_fmt, $mediaInfo);

		return $mediaInfo;
	}
	
	/**
	 * @param stream - generated by ffprobe
	 * @param KalturaMediaInfo
	 * @return KalturaMediaInfo
	 */
	protected function parseAudioStream($stream, $mediaInfo)
	{
		$mediaInfo->audioFormat = isset($stream->codec_name)? trim($stream->codec_name): null;
			// if 'codec_tag_string' empty or conatains crypthic '[0][0][0]..' (or like,
			// in such cases use 'codec_name' field
		if(!isset($stream->codec_tag_string) || strpos($stream->codec_tag_string, '][')==!null)
			$mediaInfo->audioCodecId = $mediaInfo->audioFormat;
		else $mediaInfo->audioCodecId = trim($stream->codec_tag_string);

			// If stream duration is not set or zero'ed, 
			// try to retrieve duration from stream/tag section 
		$mediaInfo->audioDuration = self::retrieveDuration($stream);

		$mediaInfo->audioBitRate = isset($stream->bit_rate)? round($stream->bit_rate/1000,2): null;
		$mediaInfo->audioBitRateMode; // FIXME
		$mediaInfo->audioChannels = isset($stream->channels)? trim($stream->channels): null;
			// mono,stereo,downmix,FR,FL,BR,BL,LFE
		$mediaInfo->audioChannelLayout = isset($stream->channel_layout)? self::parseAudioLayout($stream->channel_layout): null;
		$mediaInfo->audioSamplingRate = isset($stream->sample_rate)? trim($stream->sample_rate): null;
		if ($mediaInfo->audioSamplingRate < 1000)
			$mediaInfo->audioSamplingRate *= 1000;
		$mediaInfo->audioResolution = isset($stream->bits_per_sample)? trim($stream->bits_per_sample): null;
		if(isset($stream->tags) && isset($stream->tags->language)){
			$mediaInfo->audioLanguage = trim($stream->tags->language);
		}
		return $mediaInfo;
	}
	
	/**
	 * 
	 * @param unknown_type $layout
	 * @return string
	 */
	protected static function parseAudioLayout($layout)
	{
		$lout = KDLAudioLayouts::Detect($layout);
		if(!isset($lout))
			$lout = $layout;
		return $lout;
	}
	
	/**
	 * @param stream - generated by ffprobe
	 * @param KalturaMediaInfo
	 * @return KalturaMediaInfo
	 */
	protected function parseDataStream($stream, KalturaMediaInfo $mediaInfo)
	{
		$mediaInfo->dataFormat = isset($stream->codec_name)? $stream->codec_name: null;
			// if 'codec_tag_string' empty or conatains crypthic '[0][0][0]..' (or like,
			// in such cases use 'codec_name' field
		if(!isset($stream->codec_tag_string) || strpos($stream->codec_tag_string, '][')==!null)
			$mediaInfo->dataCodecId = $mediaInfo->dataFormat;
		else $mediaInfo->dataCodecId = trim($stream->codec_tag_string);

		$mediaInfo->dataDuration = isset($stream->duration)? ($stream->duration*1000): null;
		return $mediaInfo;
	}
	
	/**
	 * 
	 * @param unknown_type $ffmpegBin
	 * @param unknown_type $srcFileName
	 * @param KalturaMediaInfo $mediaInfo
	 * @param unknown_type $detectDur
	 * @return multitype:Ambigous <string, NULL>
	 */
	public static function checkForSilentAudioAndBlackVideo($ffmpegBin, $srcFileName, KalturaMediaInfo $mediaInfo, $detectDur=null)
	{
		KalturaLog::log("contDur:$mediaInfo->containerDuration,vidDur:$mediaInfo->videoDuration,audDur:$mediaInfo->audioDuration");
	
		/*
		 * Evaluate vid/aud detection durations
		 */
		if(isset($mediaInfo->videoDuration) && $mediaInfo->videoDuration>4000)
			$vidDetectDur = round($mediaInfo->videoDuration/2000,2);
		else if(isset($mediaInfo->containerDuration) && $mediaInfo->containerDuration>4000)
			$vidDetectDur = round($mediaInfo->containerDuration/2000,2);
		else
			$vidDetectDur = 0;
			
		if(isset($mediaInfo->audioDuration) && $mediaInfo->audioDuration>4000)
			$audDetectDur = round($mediaInfo->audioDuration/2000,2);
		else if(isset($mediaInfo->containerDuration) && $mediaInfo->containerDuration>4000)
			$audDetectDur = round($mediaInfo->containerDuration/2000,2);
		else
			$audDetectDur = 0;
	
			/*
			 * Limit the aud/vid detect duration to match the global detect duration,
			 * if such duration is provided
			 */
		if(isset($detectDur) && $detectDur>0) {
			if($audDetectDur>$detectDur) $audDetectDur=$detectDur;
			if($vidDetectDur>$detectDur) $vidDetectDur=$detectDur;
		}
		
		list($silenceDetected,$blackDetected) = self::detectSilentAudioAndBlackVideoIntervals($ffmpegBin, $srcFileName, $vidDetectDur, $audDetectDur, $detectDur);
		
		if(isset($blackDetected)){
			list($blackStart,$blackDur) = $blackDetected[0];
			if($blackDur==-1) $blackDur = $vidDetectDur;
			$blackDetectMsg = "black frame content for at least $blackDur sec";
		}
		else{
			$blackDetectMsg = null;
		}

		if(isset($silenceDetected)){
			list($silenceStart,$silenceDur) = $silenceDetected[0];
			if($silenceDur==-1) $silenceDur = $audDetectDur;
			$silenceDetectMsg = "silent content for at least $silenceDur sec";
		}
		else{
			$silenceDetectMsg = null;
		}

		$detectMsg = $silenceDetectMsg;
		if(isset($blackDetectMsg))
			$detectMsg = isset($detectMsg)?"$detectMsg,$blackDetectMsg":$blackDetectMsg;
		
		if(empty($detectMsg))
			KalturaLog::log("No black frame or silent content in $srcFileName");
		else
			KalturaLog::log("Detected - $detectMsg, in $srcFileName");
		
		return array($silenceDetectMsg, $blackDetectMsg);		
	}

	/**
	 * 
	 * @param unknown_type $ffmpegBin
	 * @param unknown_type $srcFileName
	 * @param KalturaMediaInfo $mediaInfo
	 * @return boolean
	 */
	public static function checkForGarbledAudio($ffmpegBin, $srcFileName, KalturaMediaInfo $mediaInfo)
	{
		KalturaLog::log("contDur:$mediaInfo->containerDuration,audDur:$mediaInfo->audioDuration");
		if(isset($mediaInfo->audioDuration)){ 
			$audDetectDur = ($mediaInfo->audioDuration>600000)? 600: round($mediaInfo->audioDuration/1000,2);
		}
		else if(isset($mediaInfo->containerDuration)){ 
			$audDetectDur = ($mediaInfo->containerDuration>600000)? 600: round($mediaInfo->containerDuration/1000,2);
		}		
		else if(isset($mediaInfo->videoDuration)){ 
			$audDetectDur = ($mediaInfo->videoDuration>600000)? 600: round($mediaInfo->videoDuration/1000,2);
		}
		else	
			$audDetectDur = 0;
		
		if($audDetectDur>0 && $audDetectDur<10){
			KalturaLog::log("Audio OK - short audio, audDetectDur($audDetectDur)");
			return false;
		}
		
		list($silenceDetected,$blackDetected) = KFFMpegMediaParser::detectSilentAudioAndBlackVideoIntervals($ffmpegBin, $srcFileName, null, 0.05, $audDetectDur,"-90dB");
		
		$ticks = isset($silenceDetected)? count($silenceDetected): 0;
		if($ticks<=10){
			KalturaLog::log("Audio OK - low numbers of ticks($ticks)");
			return false;
		}
		
		KalturaLog::log("audDetectDur($audDetectDur),ticks($ticks)");
		if($audDetectDur>0) {
			$ticksPerMin = $ticks/($audDetectDur/60);
			KalturaLog::log("ticksPerMin($ticksPerMin)");
			
			if($ticksPerMin<15 
			||($audDetectDur<60 && $ticksPerMin<30) 
			||($audDetectDur<120 && $ticksPerMin<20) ){
				KalturaLog::log("Audio OK");
				return false;
			}
		}
		else if($ticks<100) {
			KalturaLog::log("Audio OK - no duration, number of ticks smaller than threshold(100)");
			return false;
		}
		
		KalturaLog::log("Detected garbled audio.");
		return true;
	}
	
	/**
	 * 
	 * @param unknown_type $ffmpegBin
	 * @param unknown_type $srcFileName
	 * @param unknown_type $blackInterval
	 * @param unknown_type $silenceInterval
	 * @param unknown_type $detectDur
	 * @param unknown_type $audNoiseLevel
	 * @return NULL|multitype:Ambigous <NULL, number, unknown>
	 */
	public static function detectSilentAudioAndBlackVideoIntervals($ffmpegBin, $srcFileName, $blackInterval, $silenceInterval, $detectDur=null, $audNoiseLevel=0.0001)
	{
		//		KalturaLog::log("checkSilentAudioAndBlackVideo(contDur:$mediaInfo->containerDuration,vidDur:$mediaInfo->videoDuration,audDur:$mediaInfo->audioDuration)");
	
		/*
		 * Set appropriate detection filters
		*/
		$detectFiltersStr=null;
		// ~/ffmpeg-2.1.3 -i /web//content/r71v1/entry/data/321/479/1_u076unw9_1_wprx637h_21.copy -vf blackdetect=d=2500 -af silencedetect=noise=0.0001:d=2500 -f null dummyfilename 2>&1
		if(isset($blackInterval) && $blackInterval>0) {
			$detectFiltersStr = "-vf blackdetect=d=$blackInterval";
		}
		if(isset($silenceInterval) && $silenceInterval>0) {
			$detectFiltersStr.= " -af silencedetect=noise=$audNoiseLevel:d=$silenceInterval";
		}
	
		if(empty($detectFiltersStr)){
			KalturaLog::log("No duration values in the source file metadata. Cannot run black/silence detection for the $srcFileName");
			return null;
		}
	
		$cmdLine = "$ffmpegBin ";
		if(isset($detectDur) && $detectDur>0){
			$cmdLine.= "-t $detectDur";
		}
		
		$srcFileName = kFile::realPath($srcFileName);
		$cmdLine.= " -i \"$srcFileName\" $detectFiltersStr -nostats -f null dummyfilename 2>&1";
	
		/*
		 * Execute the black/silence detection
		*/
		$lastLine=kExecWrapper::exec($cmdLine , $outputArr, $rv);
		if($rv!=0) {
			KalturaLog::err("Black/Silence detection failed on ffmpeg call - rv($rv),lastLine($lastLine)");
			return null;
		}
	
	
		/*
		 * Searce the ffmpeg printout for
		 * - blackdetect or black_duration
		 * - silencedetect or silence_duration
		 */
		$silenceDetected= self::parseDetectionOutput($outputArr,"silencedetect", "silence_duration", "silence_start");
		$blackDetected  = self::parseDetectionOutput($outputArr,"blackdetect", "black_duration", "black_start");
		return array($silenceDetected, $blackDetected);
		
	}
	
	/**
	 * 
	 * @param unknown_type $outputStr
	 * @param unknown_type $detectString
	 * @param unknown_type $durationString
	 * @return NULL|number|unknown
	 */
	private static function parseDetectionOutput(array $outputArr, $detectString, $durationString, $startString=null)
	{
		$detectedArr = array();
		$start = null;
		$dur = null;
		$isDetected = false;
		foreach ($outputArr as $line){
			if(strstr($line, $detectString)==false){
				continue;
			}
			$isDetected = true;
			if(isset($startString) && ($str=strstr($line, $startString))!=false){
				sscanf($str,"$startString:%f", $start);
			}
			if(($str=strstr($line, $durationString))!=false){
				sscanf($str,"$durationString:%f", $dur);
				if(!isset($start)) {
					$start = 0; 
				}
				$detectedArr[] = array($start,$dur);
				$start = $dur = null;
			}
		}
		if($isDetected==true) {
			if(count($detectedArr)==0){
				$detectedArr[] = array(0,-1);	
			}
			return $detectedArr;
		}
		else
			return null;
	}
	
	/**
	 * 
	 * @param unknown_type $ffprobeBin
	 * @param unknown_type $srcFileName
	 * @return array of scene cuts
	 */
	public static function retrieveSceneCuts($ffprobeBin, $srcFileName)
	{
		KalturaLog::log("srcFileName($srcFileName)");
	
		$cmdLine = "$ffprobeBin -show_frames -select_streams v -of default=nk=1:nw=1 -f lavfi \"movie='$srcFileName',select=gt(scene\,.4)\" -show_entries frame=pkt_pts_time";
		$lastLine=kExecWrapper::exec($cmdLine , $outputArr, $rv);
		if($rv!=0) {
			KalturaLog::err("SceneCuts detection failed on ffmpeg call - rv($rv),lastLine($lastLine)");
			return null;
		}
		/*
		 * The resultant array contains in sequential lines - pairs of time & scene-cut value 
		 */
		$sceneCutArr = array();
		for($i=1; $i<count($outputArr); $i+=2){
			$sceneCutArr[$outputArr[$i-1]] = $outputArr[$i];
		}
		return $sceneCutArr;
	}
	
	/**
	 * 
	 * @param unknown_type $ffprobeBin
	 * @param unknown_type $srcFileName
	 * @return array of keyframes
	 */
	public static function retrieveKeyFrames($ffprobeBin, $srcFileName,$start=null,$duration=null)
	{
		KalturaLog::log("srcFileName($srcFileName)");
		
		$trimStr=null;
		if(isset($start) && $start>0){
			$trimStr = ",trim=start=$start";
		}
		if(isset($duration) && $duration>0){
			if(isset($trimStr))
				$trimStr.= ":duration=$duration";
			else
				$trimStr = ",trim=duration=$duration";
		}
		
		$srcFileName = kFile::realPath($srcFileName);
		$cmdLine = "$ffprobeBin -show_frames -select_streams v -of default=nk=1:nw=1 -f lavfi \"movie='$srcFileName',select=eq(pict_type\,PICT_TYPE_I)$trimStr\" -show_entries frame=pkt_pts_time";
		$lastLine=kExecWrapper::exec($cmdLine , $outputArr, $rv);
		if($rv!=0) {
			KalturaLog::err("Key Frames detection failed on ffmpeg call - rv($rv),lastLine($lastLine)");
			return null;
		}
		return $outputArr;
	}

	/**
	 * 
	 * @param unknown_type $ffprobeBin
	 * @param unknown_type $srcFileName
	 * @return array with detcted GOP values (min, max, dectected)
	 */
	public static function detectGOP($ffprobeBin, $srcFileName, $start=null, $duration=null)
	{
		$kFrames = KFFMpegMediaParser::retrieveKeyFrames($ffprobeBin, $srcFileName, $start, $duration);
		if(!isset($kFrames) || count($kFrames)<2){
			return null;
		}
			/*
			 * Turn the KF timings into integers representing 10th seconds
			 */
		foreach($kFrames as $k=>$kF){
			$kFrames[$k] = (int)round($kF*100);
		}
KalturaLog::log("KFrames:".serialize($kFrames));
			/*
			 * Calculate GOP minimum, maximum and histogram counters
			 */
		$gopMin = $gopMax = $kFrames[1]-$kFrames[0];
//		$gopHist = array();		//  GOP Histogram array - counts number occurences of each GOP
//		$gopHist[$gopMin] = 1;

			 // If there are more than 1 gop (2 KF's), then For more than With only 2 KF's - no reason to continue
		for($i=2;$i<count($kFrames); $i++){
			$gop = $kFrames[$i]-$kFrames[$i-1];
			$gopMin = min($gopMin,$gop);
			$gopMax = max($gopMax,$gop);
			
/*			if(key_exists($gop, $gopHist)){
				$gopHist[$gop] = $gopHist[$gop]+1;
			}
			else{
				$gopHist[$gop] = 1;
			}*/
		}
		
			/*
			 * Detect 0.5-4sec gops
			 *  Create GOP hustogram
			 *  Calculte the appeared to expected number of GOPs
			 *  The GOP with hihest ratio considered to be the 'detected' GOP
			 */
		$kf2gopHist = array(50=>0, 100=>0, 150=>0, 200=>0, 250=>0, 300=>0, 350=>0, 400=>0);
		$kf2gopHist = array(200=>0, 400=>0);
		$delta=6;
		for($tm=$kFrames[0]; $tm<=$kFrames[count($kFrames)-1];$tm+=50){
			for($t=$tm-$delta; $t<=$tm+$delta; $t++){
				if(array_search($t, $kFrames)!==false){
					break;
				}
			}
			if($t>$tm+$delta){
				continue;
			}
			foreach($kf2gopHist as $gop=>$cnt) {
				if(($tm % $gop)<5){
					$kf2gopHist[$gop]++;
				}
			}
		}
KalturaLog::log("kf2gopHist raw:".serialize($kf2gopHist));
			/*
			 * Calculate the appeared-to-expected-number-of-GOPs ratio.
			 */ 
		foreach($kf2gopHist as $gop=>$cnt) {
			$kf2gopHist[$gop] = $cnt/(round(($kFrames[count($kFrames)-1]-$kFrames[0])/$gop-0.5)+1);
		}
			// Sort the histogram array and get the GOP value that had the higest ratio
		asort($kf2gopHist);
KalturaLog::log("kf2gopHist norm:".serialize($kf2gopHist));
		end($kf2gopHist);
		$gopDetected = key($kf2gopHist);
		
			// Turn back the timing values from 10th's of sec to seconds
		$rv = array(($gopMin/100), ($gopMax/100), ($gopDetected/100));
		return $rv;
	}
	
	/**
	 * 
	 * @param $ffmpegBin
	 * @param $ffprobeBin
	 * @param $srcFileName
	 * @return number
	 */
	private static function checkForScanType($ffmpegBin, $ffprobeBin, $srcFileName, $seconds=5)
	{
		$srcFileName = kFile::realPath($srcFileName);
			// The cmdLine was fixed with '-map v' to copy to the pipe ONLY the video stream.
		$cmdLine = "$ffmpegBin -i \"$srcFileName\" -c:v copy -map v -f matroska -y -v quiet -t $seconds - ";
		$cmdLine.= "| $ffprobeBin -show_frames -select_streams v - -of csv -show_entries frame=interlaced_frame,pkt_pts_time,top_field_first| head -10 2>&1";

		$lastLine=kExecWrapper::exec($cmdLine , $outputArr, $rv);
		if($rv!=0) {
			KalturaLog::err("ScanType detection failed on ffmpeg call - rv($rv),lastLine($lastLine)");
			return 0;
		}
		$interlaced=0;$tffed=0;
		$samples=0;
		foreach($outputArr as $line){
			$stam=$pts=$inter=$tff=0;
			$valsArr=explode(',', $line);
			if(count($valsArr)<4)
				continue;
			list($stam,$pts,$inter,$tff) = $valsArr;
			if($stam!="frame")
				continue;
			$samples++;
			KalturaLog::log("$stam,pts:$pts,inter:$inter,tff:$tff");
			$interlaced+=$inter;
			$tffed+=(int)$tff;
		}
		if($samples==0)
			$scanType=0;
		else {
			if($samples>5)	$thresh = 3;
			else $thresh = 1;
			
			if($interlaced>$thresh || $tffed>$thresh) {
				$scanType=1;
			}
			else
				$scanType=0;
		}
		KalturaLog::log("ScanType: $scanType");
		return $scanType;
	}

	/**
	 * 
	 * @param unknown_type $ffprobeBin
	 * @param unknown_type $srcFileName
	 * @return boolean
	 */
	private function checkForFastStart($ffprobeBin, $srcFileName)
	{
/*
	dd if=anatol/0_2s6bf81e.fs.mp4 count=1 | ffmpeg -i pipe:
	[mov,mp4,m4a,3gp,3g2,mj2 @ 0x1493100] error reading header: -541478725
	[mov,mp4,m4a,3gp,3g2,mj2 @ 0xcb3100] moov atom not found
*/
		if(!isset($ffprobeBin))
			return false;
		/*
		 * Cannot run linux 'dd' command on Win
		 */
		if(stristr(PHP_OS,'win')) return 1;
		
		$srcFileName = kFile::realPath($srcFileName);
		if(kString::beginsWith($srcFileName, 'http'))
			return 1;
		
		$cmdLine = "dd if=$srcFileName count=1 | $ffprobeBin -i pipe:  2>&1";
		$lastLine=kExecWrapper::exec($cmdLine, $outputArr, $rv);
		{
			KalturaLog::log("FastStart detection results printout - lastLine($lastLine),output-\n".print_r($outputArr,1));
		}
		$fastStart = 1;
		foreach($outputArr as $line){
			if(strstr($line, "moov atom not found")==false)
				continue;
			$fastStart = 0;
			KalturaLog::log($line);
		}
		KalturaLog::log("FastStart: $fastStart");
		return $fastStart;
/*		
		$hf=fopen($srcFileName,"rb");
		$sz = filesize($srcFileName);
		$sz = 10000;
		$contents = fread($hf, $sz);
		fclose($hf);
		$auxFilename = "d:\\tmp\\aaa1.mp4";
		$hf=fopen($auxFilename,"wb");
		$rv = fwrite($hf, $contents);
		
		
		$str=$this->getRawMediaInfo($auxFilename);
*/
	}

	/**
	 *
	 * @param unknown_type $ffprobeBin
	 * @param unknown_type $srcFileName
	 * @return array of keyframes
	 */
	public static function retrieveFramesTimings($ffprobeBin, $srcFileName)
	{
		KalturaLog::log("srcFileName($srcFileName)");
			
		$trimStr=null;
		
		$srcFileName = kFile::realPath($srcFileName);
		$cmdLine = "$ffprobeBin \"$srcFileName\" -show_frames -select_streams v -v quiet -of json -show_entries frame=pkt_pts_time,key_frame,coded_picture_number";
		$lastLine=kExecWrapper::exec($cmdLine , $outputArr, $rv);
		if($rv!=0) {
			KalturaLog::err("Key Frames detection failed on ffmpeg call - rv($rv),lastLine($lastLine)");
			return null;
		}
		$jsonObj = json_decode(implode("\n",$outputArr));
		if(isset($jsonObj) && isset($jsonObj->frames))
			return $jsonObj->frames;
		else
			return null;
	}
	
	/**
	 * detectEmptyFrames
	 *	scan for very low frames pkt sizes (thresh) to detect empty frames 
	 * 	threshold - empty frame size threshold (bellow considerd as empty)
	 *		if not passed - calculated automatically from scanned file portion 
	 */
	public static function detectEmptyFrames($ffmpegBin, $ffprobeBin, $srcFileName, $threshold=0, $startFrom=0, $duration=4)
	{
		if($duration==0)
			$duration=4;
		KalturaLog::log("Src:$srcFileName, thresh:$threshold, start:$startFrom, dur:$duration");
		
		$srcFileName = kFile::realPath($srcFileName);
		$cmdLine = "$ffmpegBin -ss $startFrom -t ".($duration+1)." -i '$srcFileName' -copyts -c:v copy -f matroska -y -v quiet -vsync 0 - | $ffprobeBin -show_frames -select_streams v - -of csv -show_entries frame=pkt_pts_time,pkt_duration_time,pkt_pos,pkt_size,pict_type,coded_picture_number,interlaced_frame -v quiet 2>&1";
		KalturaLog::log("cmdLine: $cmdLine");

		$lastLine=kExecWrapper::exec($cmdLine , $outputArr, $rv);
		if($rv!=0) {
			KalturaLog::log("FAILED: to acquire list of frames - rv($rv),lastLine($lastLine)");
			return false;
		}

		$dataArr = array();
		$frmSizeAcc=0;
		$frmSizeAccArr=array();
		$cntArr=array();
		foreach($outputArr as $line){
			$valsArr = explode(',', $line);
			if(count($valsArr)<8)
				continue;
			$stam=$pts=$pktDur=$pktPos=$pktSize=$pictType=$pictNum=$inter=0;
			$data = list($stam,$pts,$pktDur,$pktPos,$pktSize,$pictType,$pictNum,$inter) = $valsArr;
			$dataArr[] = $data;
//KalturaLog::log(json_encode($data));
			{
				if(array_key_exists($pictType, $cntArr)===true) {
					$cntArr[$pictType]+= 1;
					$frmSizeAccArr[$pictType]+= $pktSize;
				}
				else {
					$cntArr[$pictType] = 1;
					$frmSizeAccArr[$pictType] = $pktSize;
				}
				$frmSizeAcc+= $pktSize;
			}
		}
		
		if($frmSizeAcc==0 || count($outputArr)==0){
			KalturaLog::log("FAILED: bad frames stat data");
			return false;
		}
			// Evaluate threshold value, if unset
		if($threshold==0){
			$threshold = ($frmSizeAcc/count($outputArr)*0.02);
			KalturaLog::log("auto calculated threshold:$threshold");
		}
			// Scan for empty frames
		$emptyArr = array();
		$fst=PHP_INT_MAX;$lst=0;
		foreach($dataArr as $idx=>$data){
			if($data[4]<$threshold) {
				$emptyArr[] = array("ix"=>$idx,"nm"=>$data[6],"sz"=>$data[4],"tp"=>$data[5]);
				$fst = min($fst,$data[6]); $lst = max($lst,$data[6]);
			}
		}

		$statStr = "TOT=av:".round($frmSizeAcc/count($outputArr)).",nm:".count($outputArr).",";
		foreach($frmSizeAccArr as $tp=>$acc){
			if($cntArr[$tp]==0)
				$statStr.= "$tp=av:0,nm:0";
			else
				$statStr.= "$tp=av:".round($acc/$cntArr[$tp]).",nm:".$cntArr[$tp].",";
		}

		$emptyCnt=count($emptyArr);
		if($fst==0 && $lst==0)
			$empIntervalSz = count($dataArr);
		else 
			$empIntervalSz = ($fst>$lst)?0:($lst-$fst+1);
		if($emptyCnt>3 && $empIntervalSz>0 && $emptyCnt/$empIntervalSz>0.8) {
			KalturaLog::log("Empty frames:".serialize($emptyArr));
			KalturaLog::log("RESULT:detected empty frames - num:$emptyCnt, fst:$fst,lst:$lst, emptiness ratio:".($emptyCnt/$empIntervalSz)." ($statStr)");
			return count($emptyArr);
		}
		else {
			KalturaLog::log("RESULT:no empty frames! ($statStr)");
			return false;
		}
	}
	
	/**
	 * 
	 * @param unknown_type $ffprobeBin
	 * @param unknown_type $srcFileName
	 * $param unknown_type $reset
	 * @return array of volumeLevels
	 */	
	public static function retrieveVolumeLevels($ffprobeBin, $srcFileName, $reset=1)
	{
		KalturaLog::log("srcFileName($srcFileName)");
		
		$srcFileName = kFile::realPath($srcFileName);
		$cmdLine = "$ffprobeBin -f lavfi -i \"amovie='$srcFileName',astats=metadata=1:reset=$reset\" -show_entries frame=pkt_pts_time:frame_tags=lavfi.astats.Overall.RMS_level -of csv=p=0 -v quiet";
		$lastLine=kExecWrapper::exec($cmdLine , $outputArr, $rv);
		if($rv!=0) {
			KalturaLog::err("Volume level detection failed on ffprobe call - rv($rv),lastLine($lastLine)");
			return null;
		}
		$volumeLevels = array();
		foreach($outputArr as $line) {
			list($tm,$vol) = explode(',', $line);
			$tm = (int)($tm*1000);
			if(!isset($tm) || !isset($vol))
				continue;
			$vol = trim($vol);
			if($vol!='-inf')
				$volumeLevels[$tm] = $vol;
			else
				$volumeLevels[$tm] = -1000;
		}

		return $volumeLevels;
	}

	/**
	 * retrieveDuration
	 *
	 * @param unknown_type $stream
	 * @return int or null
	 */	
	private static function retrieveDuration($stream)
	{
			// If stream duration is not set or zero'ed, 
			// try to retrieve duration from stream/tag section 
		if(isset($stream->duration) && $stream->duration>0)
			return round($stream->duration*1000);
		else if(isset($stream->tags->duration))
			return self::convertDuration2msec($stream->tags->duration);
		else
			return null;
	}
	
	/**
	 * 
	 * @param $ffmpegBin
	 * @param $ffprobeBin
	 * @param $srcFileName
	 * $param $startFrom
	 * $param $stream
	 * @return streams dur array or null
	 */
	private static function retrieveDurationFromLastFrame($ffmpegBin, $ffprobeBin, $srcFileName, $startFrom, $stream=null)
	{
		KalturaLog::log("src:$srcFileName,start:$startFrom,stream:$stream");
		switch($stream){
			case null:
				$copyStreams = "-c:v copy -c:a copy";
				$streams = array("video", "audio");
				break;
			case "video":
			case "audio":
				$strmCh = $stream[0];
				$copyStreams = "-c:$strmCh copy";
				$streams = array($stream);
				break;
			default:
				KalturaLog::log("Invalid stream id ($stream)");
				return null;
		}

		$srcFileName = kFile::realPath($srcFileName);
		$cmdLine = "$ffmpegBin -ss $startFrom -i \"$srcFileName\" -copyts $copyStreams -f matroska -y -v quiet - | $ffprobeBin - -show_frames -of csv -show_entries frame=media_type,pkt_pts_time,pkt_duration_time   2>&1";
		KalturaLog::log($cmdLine);
		$lastLine=kExecWrapper::exec($cmdLine , $outputArr, $rv);

		if($rv!=0) {
			KalturaLog::err("Duration retrieval detection failed on ffmpeg/ffprobe call - rv($rv),lastLine($lastLine)");
			return null;
		}
		
		$durs = array();
		for (end($outputArr); key($outputArr)!==null; prev($outputArr)){
			$line = current($outputArr);
//KalturaLog::log("line:$line");
			$stam=$pts=$strm=$dur=0;
			$valsArr=explode(',', $line);
			if(count($valsArr)<4)
				continue;
			list($stam,$strm,$pts,$dur) = $valsArr;
			if($stam!="frame")
				continue;
			if(isset($pts) && isset($dur)) {
				$pts=(float)$pts; $dur=(float)$dur;
				if(in_array($strm, $streams) && !key_exists($strm, $durs))
					$durs[$strm] = $pts+$dur;
				$duration = $pts+$dur;
				KalturaLog::log("$stam,pts:$pts,stream:$strm,dur:$dur,$duration");
			}
			if(count($streams)==count($durs)){
				KalturaLog::log(print_r($durs,1));
				return $durs;
			}
				
		}
		KalturaLog::log("Missing last frame duration");
		return null;
	}
	
	/**
	 * parsePixelFormat
	 *
	 * @param KalturaMediaInfo $mediaInfo
	 */	
	private static function parsePixelFormat($pixelFormat, KalturaMediaInfo $mediaInfo)
	{
		KalturaLog::log("In - pixelFormat:$pixelFormat");
		$rv = preg_match('/\s*([a-z]+)\s*([0-9]+)\s*([a-z]*)\s*([0-9]*)/', $pixelFormat, $matches, PREG_OFFSET_CAPTURE);
		if($rv===false || !(isset($matches) && is_array($matches) and count($matches)>=3)){
			KalturaLog::log("Out - Unrecognized pixelFormat");
			return;
		}
		$mediaInfo->pixelFormat = $pixelFormat;
		$mediaInfo->colorSpace = $matches[1][0];
		$mediaInfo->chromaSubsampling = $matches[2][0];
		if(count($matches)>=5)
			$mediaInfo->bitsDepth = $bitsDepth = $matches[4][0];
		else
			$bitsDepth = null;
		KalturaLog::log("Out - colorSpace:$mediaInfo->colorSpace, chromaSubsampling:$mediaInfo->chromaSubsampling, bitsDepth:$bitsDepth");
	}
	
	/**
	 * convertToMediaInfoNames
	 *
	 * @param KalturaMediaInfo $mediaInfo
	 */	
	private static function convertToMediaInfoNames($mediaInfo)
	{
			$containerFormatList = array (
				"mov" => 		"mpeg-4",	
				"mpegts" =>     "mpeg-ts",		
				"wav" =>		"wave",		
				"mpegvideo" =>  "mpeg video",
				"mp4" =>        "mpeg-4",		
				"rm" =>         "realmedia",	
				"3gp" =>        "mpeg-4",	
			);
			$audioFormatList = array (
				"pcm_s24le" =>	"pcm",	
				"pcm_dvd" =>    "pcm",	
				"aac_latm" =>   "aac",	
				"wmalossless" =>"wma",	
				"adpcm_ima_wav" =>  "adpcm",	
				"cook" =>		"cooker",
				"pcm_s16be" =>	"pcm",
				"pcm_s16le" =>  "pcm",	
				"pcm_s24be" =>	"pcm",
				"pcm_s24le" =>  "pcm",	
				"pcm_f32be" =>  "pcm",	
				"pcm_f32le" =>	"pcm",	
				"pcm_u8" =>     "pcm",	
			);
			$videoFormatList = array (
				"h264" =>		"avc",		
				"hevc" =>	    "hvc1",			
				"mjpeg" =>	    "jpeg",			
				"vp9" =>	    "v_vp9",			
				"flv1" =>	    "sorenson spark",
				"mss2" =>	    "windows media",	
				"rv30" =>	    "realvideo 3",	
				"msmpeg4v3" =>	"mpeg-4 visual",	
				"msmpeg4v2" =>	"mpeg-4 visual",	
				"cinepak" =>	"cinepack",		
				"svq3" =>		"sorenson 3",
			);

			if($mediaInfo->containerFormat!='image2') {
				if(key_exists($mediaInfo->videoFormat,$videoFormatList)==true)
					$mediaInfo->videoFormat = $videoFormatList[$mediaInfo->videoFormat];
			}

			$mkvCodecList = array (
				"vp9" =>	"v_vp9",			
				"v_vp9" =>	"v_vp9",			
				"vp8" =>	"v_vp8",			
				"vorbis" =>	"a_vorbis"
			);
			if(key_exists($mediaInfo->videoFormat,$mkvCodecList)==true)
				$mediaInfo->videoCodecId = $mkvCodecList[$mediaInfo->videoFormat];

			if(key_exists($mediaInfo->audioFormat,$audioFormatList)==true)
				$mediaInfo->audioFormat = $audioFormatList[$mediaInfo->audioFormat];
			if(key_exists($mediaInfo->audioFormat,$mkvCodecList)==true)
				$mediaInfo->audioCodecId = $mkvCodecList[$mediaInfo->audioFormat];

			if(key_exists($mediaInfo->containerFormat,$containerFormatList)==true)
				$mediaInfo->containerFormat = $containerFormatList[$mediaInfo->containerFormat];

		return $mediaInfo;
	}

};
