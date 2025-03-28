<?php

 /*****************************
 * Includes & Globals
 */
//ini_set("memory_limit","512M");

		/********************
		 * 
		 */
	class KFFmpegToPartnerMatch {
		static protected $ffmpegVersion=6;
		static protected $ffmpegBin="ffmpeg";
		static protected $ffprobeBin="ffprobe";
		static protected $isMatched=false;
		static protected $partnerId=null;
		
//		static public	$kConfEmulateFilename = "/web/tmp/anatol/emulateKConf.txt";
		static public   $kConfEmulateFilename = "/tmp/emulateKConf.txt";
		/********************
		 * saveData
		 *	for dev/debug purposes
		 */
		public static function saveData() {
			$zzz4 = new stdClass();
			$zzz4->version = 4;
			$zzz4->ffmpegBin="ffmpeg";
			$zzz4->ffprobeBin="ffprobe";
			$zzz4->whiteList = array(1,2,3);
			$zzz4->blackList = array(11,12,3);
			
			$zzz6 = new stdClass();
			$zzz6->version = 6;
			$zzz6->ffmpegBin="/web/content/shared/bin/ffmpeg-6.0-ub16-bin/ffmpeg.sh";
			$zzz6->ffprobeBin="/web/content/shared/bin/ffmpeg-6.0-ub16-bin/ffprobe.sh";
			$zzz6->whiteList = array(1,2,3);
			$zzz6->blackList = array(11,12,3);
			
			$zzz = new stdClass();
			$zzz->ffmpeg4 = $zzz4;
			$zzz->ffmpeg6 = $zzz6;
			$jsonStr = json_encode($zzz);
			KalturaLog::log($jsonStr);
			file_put_contents(self::$kConfEmulateFilename,$jsonStr);
		}
		
		/********************
		 * loadData
		 *	retrieves the migration date from kConf (or from a file on the dev/debug stage)
		 */
		protected static function loadData() {
		$data=null;
			if(file_exists(self::$kConfEmulateFilename)) {
				KalturaLog::log("kConfEmulateFilename:".self::$kConfEmulateFilename." exists");
				$jsonStr=file_get_contents(self::$kConfEmulateFilename);
				$data = json_decode($jsonStr);
				if(isset($data->emulate) && $data->emulate==0){
					$data=null;
					KalturaLog::log("not emulate mode !!!");
				}
				else KalturaLog::log("emulate mode");
			}
			else 
				KalturaLog::log("not emulate mode");
				
			if(!isset($data)) {
				$data = kconf::get('ffmpeg', 'runtime_config', array());
				if(count($data)==0) {
					KalturaLog::log("no kConf");
					$data = new stdClass;
					$ffmpegObj = new stdClass;
					$ffmpegObj->version=4;
					$ffmpegObj->ffmpegBin="ffmpeg";
					$ffmpegObj->ffprobeBin="ffprobe";
					$ffmpegObj->whiteList="";
					$ffmpegObj->blackList="";
					$data->ffmpeg4 = clone($ffmpegObj);
					$ffmpegObj->version=6;
					$data->ffmpeg6 = clone($ffmpegObj);
				}

				KalturaLog::log("raw kConf:".print_r($data,1));
				$tmp = new stdClass;
				foreach($data as $ver=>$ffmpegConf) {
					$ffmpegObj = new stdClass;
					foreach($ffmpegConf as $fld=>$val) {
						$ffmpegObj->$fld = $val;
					}
					$tmp->$ver = $ffmpegObj;
				}
								
				$data = $tmp;
				KalturaLog::log("converted kConf:".print_r($data,1));
			}

			$data->ffmpeg4->whiteList = self::valueToList($data->ffmpeg4->whiteList);
			$data->ffmpeg4->blackList = self::valueToList($data->ffmpeg4->blackList);
			$data->ffmpeg6->whiteList = self::valueToList($data->ffmpeg6->whiteList);
			$data->ffmpeg6->blackList = self::valueToList($data->ffmpeg6->blackList);

//KalturaLog::log("final:".print_r($data,1));
//die;
			return $data;
		}
		
		/********************
		 * valueToList
		 *	translate comma separated list of partners, into an array
		 */
		protected static function valueToList($val) {
			if(isset($val) && is_string($val)) {
//KalturaLog::log($val);
				if(strlen($val)>0)
					$val = explode(',',$val);
				else $val = array();
			}
			return $val;
		}
		
		/********************
		 * getVersion
		 */
		public static function getVersion() {
			KalturaLog::log("version:".KFFmpegToPartnerMatch::$ffmpegVersion);
			return KFFmpegToPartnerMatch::$ffmpegVersion;
		}
	
		/********************
		 * getPartner
		 */
		public static function getPartner() {
			KalturaLog::log("partnerId:".KFFmpegToPartnerMatch::$partnerId);
			return KFFmpegToPartnerMatch::$partnerId;
		}
	
		/********************
		 * getAll
		 */
		public static function getAll() {
			KalturaLog::log("version:".KFFmpegToPartnerMatch::$ffmpegVersion);
			return array(KFFmpegToPartnerMatch::$ffmpegVersion, KFFmpegToPartnerMatch::$ffmpegBin, KFFmpegToPartnerMatch::$ffprobeBin);
		}
	
		/********************
		 * isMatched
		 */
		public static function isMatched() {
			return self::$isMatched;
		}
		
		/********************
		 * match
		 */
		public static function match($val) {
KalturaLog::log(print_r($val,1));
			if(!isset($val))
				return;

			if(is_numeric($val)) {
				$partnerId=$val;
			}
/*
	the partner id optionally could have been retrieved from various objs'
			else if(get_class($val)=='mediaInfo') {
				$asset = assetPeer::retrieveById($val->getFlavorAssetId());
				$partnerId = $asset->getPartnerId();
			}
			else if(is_array($val) && get_class($val[0])=='mediaInfo') {
				$asset = assetPeer::retrieveById($val[0]->getFlavorAssetId());
				$partnerId = $asset->getPartnerId();
			}
*/			
			if(!isset($partnerId))
				return;
			
			$data = self::loadData();
			if(array_search($partnerId, $data->ffmpeg4->whiteList)!==false) {
				self::$ffmpegVersion=$data->ffmpeg4->version;
				self::$ffmpegBin=$data->ffmpeg4->ffmpegBin;
				self::$ffprobeBin=$data->ffmpeg4->ffprobeBin;
				self::$isMatched = true;
				self::$partnerId = $partnerId;
KalturaLog::log("partner:$partnerId, ver:".self::$ffmpegVersion);
			}
			else if(array_search($partnerId, $data->ffmpeg4->blackList)!==false) {
				self::$ffmpegVersion=$data->ffmpeg6->version;
				self::$ffmpegBin=$data->ffmpeg6->ffmpegBin;
				self::$ffprobeBin=$data->ffmpeg6->ffprobeBin;
				self::$isMatched = true;
				self::$partnerId = $partnerId;
KalturaLog::log("partner:$partnerId, ver:".self::$ffmpegVersion);
			}
			else if(array_search($partnerId, $data->ffmpeg6->whiteList)!==false) {
				self::$ffmpegVersion=$data->ffmpeg6->version;
				self::$ffmpegBin=$data->ffmpeg6->ffmpegBin;
				self::$ffprobeBin=$data->ffmpeg6->ffprobeBin;
				self::$isMatched = true;
				self::$partnerId = $partnerId;
KalturaLog::log("partner:$partnerId, ver:".self::$ffmpegVersion);
			}
			else if(array_search($partnerId, $data->ffmpeg6->blackList)!==false) {
				self::$ffmpegVersion=$data->ffmpeg4->version;
				self::$ffmpegBin=$data->ffmpeg4->ffmpegBin;
				self::$ffprobeBin=$data->ffmpeg4->ffprobeBin;
				self::$isMatched = true;
				self::$partnerId = $partnerId;
KalturaLog::log("partner:$partnerId, ver:".self::$ffmpegVersion);
			}
			else {
				self::$isMatched = false;
				self::$partnerId = null;
				KalturaLog::log("partner:$partnerId, not in lists");
			}
		}
		
		/********************
		 * embedPartnerId
		 */
		public static function embedPartnerId($inputString, $pattern='__partner') {
			if(isset(KFFmpegToPartnerMatch::$partnerId)) {
				KalturaLog::log("partner:".KFFmpegToPartnerMatch::$partnerId);
				return $inputString."__partner".KFFmpegToPartnerMatch::$partnerId;
			}
			else {
				return $inputString;
			}
		}
		
		/********************
		 * extractPartnerId
		 */
		public static function extractPartnerId($embeddedString, $pattern='__partner') 
		{
			$patternPos = strrpos($embeddedString, $pattern);
			
			if ($patternPos === false) {
				return [null, $embeddedString];
			}

			$numberPos = $patternPos + strlen($pattern);
			$number = substr($embeddedString, $numberPos);
			if (!ctype_digit($number)) {
				return [null, $embeddedString];
			}
			$newString = substr($embeddedString, 0, $patternPos);

			return [$number, $newString];
		}
	}
	
	/********************
	 * Chunked Encoding module
	 */
	class KChunkedEncode4 {
		const	MaxInaccuracyValue = 0.100;	// 100 msec, ~3 frames
		const 	CHUNK_ENCODE_POSTFIX = 'chunkenc';
		const 	SupportedOutputCodecs = array("libx264","libx265","h264","h264b","h264m","h264h",
					"h265","libvpx-vp9","vp9","libaom-av1","av1");
		
		public $params = null;			// Additional encoding parameters, evaluated from the cmd-line
		public $setup = null;
		
		protected $chunkDataArr = array();	// Chunk design, processing and resultant data

		public $sourceFileDt = null;	// Source file mediaInfo
		public $mergedFileDt = null;	// Generated file mediaInfo

		public $maxInaccuracyValue=null;// Max allowed duration inaccuracy 

		public $cmdLine = null;
		public $chunkEncodeToken = self::CHUNK_ENCODE_POSTFIX;	// Used to identify the chunked encode jobs
		public $videoChunkPostfix = 'vid';		// File postfix's
		public $audioFilePostfix = 'aud';		//
		
			// - raw - simple file level concatenation (linux cat, fread/fwrite, ffmpeg 'concat' protocol)
			// - demuxer - ffmpeg demuxer concat
		public $concatMethod = "raw";
		
			// - mpegts - for H264/H265 codecs
			// - mp4 - for VP9 / AV1 (they does not mux into mpegts)
		public $chunkFileFormat = "mpegts";
		public $concatSettings = null;
		public $chunkDurThreshInFrames = 10;
		
		/********************
		 * 
		 */
		public function __construct($setup)
		{
			$this->setup = $setup;
			$this->params  = new KChunkedEncodeParams4();
				/* 
				 * VP9 and AV1 cannot be muxed into MPEGTS,
				 * Therefore their chunks generated with MP4,
				 * and require more complicated concat method - demuxer.
				 * (H264/5 can use demuxer method too)
				 */
			if((strstr($setup->cmd,"libvpx-vp9")!==false) 
			|| (strstr($setup->cmd,"libaom-av1")!==false)) {
				$this->concatMethod="demuxer";
				$this->chunkFileFormat = "mp4";
				$this->concatSettings = "-f concat -safe 0";
			}
			kBatchUtils::tryLoadKconfConfig();
		}
		
		
		/********************
		 * Initialize the chunked encode session
		 */
		public function Initialize(&$msgStr)
		{
			KalturaLog::log(date("Y-m-d H:i:s"));

			$setup = $this->setup;
			$params = $this->params;

			$params->GetEncodingSettings($setup->cmd);

			if(isset($setup->source)){
				$params->source = $setup->source;
			}
			else if(!isset($params->source)) {
				KalturaLog::log($msgStr="ERROR: missing essential - source");
				return false;
			}

				// Get source mediaData. Required for 'supported' validation
			$this->sourceFileDt = $this->getMediaData($params->source);
			if(!isset($this->sourceFileDt)){
				KalturaLog::log($msgStr="ERROR: failed on media data retrieval of the source file ($params->source)");
				return false;
			}
			
// Workarround for long audio conversions of large MXF file stored on S3
if($this->sourceFileDt->containerFormat=="mxf" && isset($params->unResolvedSourcePath)) {
	$params->sourceForAudio=kfile::buildDirectUrl($params->unResolvedSourcePath);
}
				/*
				 * Setup work folders 
				 */
			$pInfo = pathinfo($params->output);
			$setup->output = realpath($pInfo['dirname'])."/".$pInfo['basename'];
			
			//Build remote shared path to follow same setting as output path
			if($setup->sharedChunkPath)
			{
				list($baseDirName, $uniqId) = explode(".", $setup->output);
				$setup->sharedChunkPath = kFile::fixPath($setup->sharedChunkPath . '/' .
					substr($baseDirName, -4, 2). '/' .
					substr($baseDirName, -2) . '/' .
					$pInfo['basename']);
			}

			if(isset($setup->createFolder) && $setup->createFolder==1) {
				$setup->output.= "_".$this->chunkEncodeToken."/";
				if(!kFile::checkFileExists($setup->output)) {
					KalturaLog::log("Create tmp folder:".$setup->output);
					kFile::mkdir($setup->output);
				}
				$setup->output.= $pInfo['filename'];
				
				if($setup->sharedChunkPath)
				{
					$setup->sharedChunkPath .= "_".$this->chunkEncodeToken."/";
					if(!kFile::checkFileExists($setup->sharedChunkPath)) {
						KalturaLog::log("Create tmp folder:".$setup->sharedChunkPath);
						kFile::fullMkdir($setup->sharedChunkPath);
					}
					$setup->sharedChunkPath.= $pInfo['filename'];
				}
			}
			
				/*
				 * Evaluate session duration 
				 */
			if($this->setup->duration==-1) {
				$params->duration = ($this->sourceFileDt->videoDuration>0)? 
					$this->sourceFileDt->videoDuration: $this->sourceFileDt->containerDuration;
				$params->duration = round($params->duration/1000,4);
			}
			else $params->duration = $this->setup->duration;
			
			if(isset($setup->fps)){
				$params->fps = $setup->fps;
			}
			if(isset($setup->gop)){
				$params->gop = round($setup->gop*$params->fps);
			}

			if(self::verifySupport($params->vcodec, $params->acodec, $params->format, $params->fps, $params->gop, $params->duration, $params->height, $msgStr)!=true){
				KalturaLog::log($msgStr);
				return false;
			}
			
			if(($key=array_search("-vsync", $params->cmdLineArr))!==false && $params->cmdLineArr[$key+1]!=1) {
				KalturaLog::log($msgStr="UNSUPPORTED: vsync param (".($params->cmdLineArr[$key+1])."), should be 1 (cfr)");
				return false;
			}

				/*
				 * Adjust the thread number to ouput frame size 
				 * -  SD - dec:7, enc:4
				 * - >SD - dec:5, enc:4
				 */
			if(isset($setup->threadsDec)){
				$params->threadsDec = $setup->threadsDec;
			}
			else {
				if($params->height>360) {		// >SD
					$params->threadsDec = 5;
				}
				else {							
					$params->threadsDec = 7;	// SD
				}
			}
			
			if(isset($setup->threadsEnc)){
				$params->threads = $setup->threadsEnc;
			}
			else {
						// In some static contents AV1 encoder fails to handle multiple tiny frames,
						// generated by 4 enc threads. It results messed rendition file.
				if($params->vcodec=="libaom-av1") {
					$params->threads = 2;
				}
				else {
					$params->threads = 4;
				}
			}

			if(isset($setup->passes) && $setup->passes>0){
				$params->passes = $setup->passes;
			}
			else if(!isset($params->passes)){
				$params->passes = 1;
			}

				/*
				 * Evaluate various defaults
				 * - frame duration
				 * - chunk overlap
				 * - chunk duration 
				 */
			$params->frameDuration = 1/$params->fps;
			$this->setup->Update($params);
			
			$this->buildTemplateVideoCommandLine();

			KalturaLog::log("data:".print_r($this,1));

				/*
				 * Inaccuracy threshold
				 * - maxInaccuracyValue - max thresh on merge
				 */
			$this->maxInaccuracyValue = ($params->frameDuration*1.5>KChunkedEncode::MaxInaccuracyValue)? ($params->frameDuration*1.5): KChunkedEncode::MaxInaccuracyValue; 
			
			KalturaLog::log("data:".print_r($this,1));
			KalturaLog::log("duration(".($params->duration)."), frameDuration($params->frameDuration)\n");

				/*
				 * Generate the pre-planned chunk params (start, frames, ...)
				 */
			$this->calculateChunkTimings();
			
				// Preparations to solve ffmpeg precise positioning anomaly/bug with MPEG files and B-Frames
				// To be activated in the future
//			self::fixChunkStartTimingsForBfarmes($params->source, $params->frameDuration, $this->chunkDataArr);

				/*
				 * Handle Subs(SRT) file splitting, if it is required
				 */
			if(isset($this->params->videoFilters->subsFilename)){
				$subsFileHd = fopen($this->params->videoFilters->subsFilename,'r');
				if(!$subsFileHd)
				{
					KalturaLog::log('ERROR: missing caption file ['.$this->params->videoFilters->subsFilename.'] - exiting.');
					return false;
				}
				$subsArr = array();
				foreach($this->chunkDataArr as $idx=>$chunkData){
						/*
						 * SRT splitting
						 */
					$chunkSrtFile = $this->getChunkName($chunkData->index,"srt");
					if(file_exists($chunkSrtFile))
						unlink($chunkSrtFile);
					KSrtText::SplitSrtFile($subsFileHd, $chunkSrtFile, $chunkData->start, $chunkData->duration, $subsArr);
					KalturaLog::log("$chunkSrtFile, $chunkData->start, $chunkData->duration");
				}
			}
			
			foreach($this->chunkDataArr as $idx=>$chunkData){
				KalturaLog::log("chunk($idx)==>".json_encode($chunkData));
			}

				// Get mediaData for external reference file, if such provided 
			if(isset($this->setup->ref)) {
				$this->refFileDt = $this->getMediaData($this->setup->ref);
			}

			KalturaLog::log("cmdLine:$this->cmdLine");
			return true;
		}
		
		/********************
		 *
		 */
		public function GetChunk($idx)
		{
			if($idx>count($this->chunkDataArr)-1) {
				KalturaLog::log("Bad index ($idx), max allowed".(count($this->chunkDataArr)-1)."!!!");
				return null;
			}
			return $this->chunkDataArr[$idx];
		}
		
		/********************
		 *
		 */
		public function GetMaxChunks()
		{
			return count($this->chunkDataArr);
		}
		
		/********************
		 * Check for supported codecs/formats, conditions
		 */
		static public function verifySupport($vcodec, $acodec=null, $format, $fps, $gop, $duration, $height, &$msgStr){
			KalturaLog::log("vcodec($vcodec), acodec($acodec), format($format), fps($fps), gop($gop), duration($duration), height($height)");


			if(!in_array($vcodec, KChunkedEncode::SupportedOutputCodecs)){
				KalturaLog::log($msgStr="UNSUPPORTED: video codec ($vcodec)");
				return false;
			}

			if(isset($acodec) && strstr($acodec, "aac")===false) {
				KalturaLog::log($msgStr="UNSUPPORTED: audio codec ($acodec)");
				return false;
			}
			
			if(!in_array($format, array("mp4","mov","ismv"))){
				KalturaLog::log($msgStr="UNSUPPORTED: media file format ($format)");
				return false;
			}
			
			if(!isset($fps) || $fps==0){
				KalturaLog::log($msgStr="UNSUPPORTED: missing essential - fps");
				return false;
			}

			if(!isset($gop) || $gop==0){
				KalturaLog::log($msgStr="UNSUPPORTED: missing essential - gop");
				return false;
			}
				/*
				 * WV session fail with chunked encoding. Detect chunks by very large GOP value
					***** Disable this check, WV related flavors can disable chunks via conv-prof 
			if($gop>100){
				KalturaLog::log($msgStr="UNSUPPORTED: WV conversion detected (large GOP=$gop). Currently chunked encoding does not work with WV.");
				return false;
			}*/

			
				/*
				 * Verify session duration - it should be at least twice the chunk duration
				 *
			if(isset($height)) {
				$minimalDuration = KChunkedEncodeSetup::calculateChunkDuration($height)*2;
				if($duration<$minimalDuration){
					KalturaLog::log($msgStr="UNSUPPORTED: duration ($duration sec) too short for the frame size ($height pix), must be at least $minimalDuration sec");
					return false;
				}
			}
			else if($duration<180){
				KalturaLog::log($msgStr="UNSUPPORTED: duration ($duration) too short, must be at least 180sec");
				return false;
			}*/
			if($duration<1){
				KalturaLog::log($msgStr="UNSUPPORTED: duration ($duration) too short, must be at least 1 sec");
				return false;
			}			
			return true;
		}
	
		/********************
		 * 0:  OK
		 * 1:  first pass
		 * -1: error
		 */
		protected function buildTemplateVideoCommandLine() 
		{
			$params = $this->params;
			$cmdLineArr = $params->cmdLineArr;

			/*
			 * 'Open' symbolic links into full real path's
			 * to allow inter-server access via shared storage
			 */
			$srcIndexes = array_keys($cmdLineArr,'-i');
			foreach($srcIndexes as $idx) {
				$cmdLineArr[$idx+1] = '"' . kFile::realPath($cmdLineArr[$idx+1]) . '"';
			}
			
			$toAddFps = false;
			$toAddGop = false;
			if(($key=array_search("-g", $cmdLineArr))!==false) {
				$cmdLineArr[$key+1] = $params->gop;
			}
			else {
				$toAddGop = true;
			}

			if(($key=array_search("-r", $cmdLineArr))!==false) {
				$cmdLineArr[$key+1] = $params->fps;
			}
			else {
				$toAddFps = true;
			}
			if(($key=array_search("-force_key_frames", $cmdLineArr))!==false) {
				$cmdLineArr[$key+1]=self::handleForcedKeyFrames($cmdLineArr[$key+1], $cmdLineArr, $params, $this->setup->chunkDuration);
			}
			
			if(($key=array_search("-c:a", $cmdLineArr))!==false
			|| ($key=array_search("-acodec", $cmdLineArr))!==false) {
				unset($cmdLineArr[$key+1]);
				$cmdLineArr[$key]='-an';
			}

			$toRemove = array("-ac","-ar", "-b:a","-ab","-async","-filter_complex","-flags","-f","-bsf:v");
			foreach($cmdLineArr as $idx=>$opt){
				if(in_array($opt, array("-rc_eq"))){
					if($cmdLineArr[$idx+1][0]!="'")
						$cmdLineArr[$idx+1] = "'".$cmdLineArr[$idx+1]."'";
				}
				else if(in_array($opt, $toRemove)){
					unset($cmdLineArr[$idx+1]);
					unset($cmdLineArr[$idx]);
				}
			}
			
				/*
				 * On merge point 'Breathing'/blurry effect - for 1 pass 
				 * add fixed QP for teh first 10 frames.
				 * 2-pass transcoding does not need it.
				 */
			if($params->passes==1) {
				if($params->vcodec=='libx264'){
					if(($key=array_search("-x264opts", $cmdLineArr))!==false) {
						if(strstr($cmdLineArr[$key+1],"zones")===false) {
							$cmdLineArr[$key+1] = $cmdLineArr[$key+1].":zones=0,10,q=12";
						}
					}
					else {
						$cmdLineArr[] = "-x264opts zones=0,10,q=12";
					}
				}
				else if($params->vcodec=='libx265'){
					if(($key=array_search("-x265-params", $cmdLineArr))!==false) {
						if(strstr($cmdLineArr[$key+1],"zones")===false) {
							$cmdLineArr[$key+1] = $cmdLineArr[$key+1].":zones=0,10,q=12";
						}
					}
					else {
						$cmdLineArr[] = "-x265-params zones=0,10,q=12";
					}
				}
			}
			
			$cmdLineArr[] = "-map 0:v:0";
			if($this->chunkFileFormat=="mpegts")
				$cmdLineArr[] = "-flags -global_header";
			if(isset($params->videoFilters->filterStr))
				$cmdLineArr[] = "-filter_complex ".($params->videoFilters->filterStr);
			if($params->vcodec=='libx264')
				$cmdLineArr[] = "-bsf h264_mp4toannexb";
			if($toAddFps)
				$cmdLineArr[] = " -r $params->fps";
			if($toAddGop)
				$cmdLineArr[] = " -g $params->gop";

			if(isset($params->threads)){
				if(($key=array_search("-threads", $cmdLineArr))!==false) {
					$cmdLineArr[$key+1] = $params->threads;
				}
				else {
					$cmdLineArr[] = "-threads";
					$cmdLineArr[] = $params->threads;
				}
			}
			$params->cmdLineArr = $cmdLineArr;
			$this->cmdLine = implode(" ",$cmdLineArr);
			KalturaLog::log("cmdLine:$this->cmdLine");
		}

		/********************
		 * handleForcedKeyFrames
		 *	
		 */
		protected static function handleForcedKeyFrames($forcedKeyFramesStr, $cmdLineArr, $params, $chunkDuration)
		{
				// Fetch the gopInSec size from the forcedKeyFramesStr or calculate it via gop and fps
			if(($auxStr=strstr($forcedKeyFramesStr,"n_forced"))!==false){
				sscanf($auxStr,"n_forced*%d",$gopInSecs);
			}
			else 
				$gopInSecs=round($params->gop/$params->fps);

				// Calculate the time interval that are needed to generate extra I frames 
				// that are needed for to fix/fill-in missing frames from the 2nd segment 
			$setExtraIFramesTimingEnd = round($chunkDuration+$params->frameDuration*2,3);
			
				/*
				  Following is required only for renditions with B Frames. 
				  BF's can be set either explicitly (via main/high profiles),
				  or implicitly by providing '-bf' operand
				  '__FORCED_KF_START_SHIFT__' and '__FORCED_KF_EXTRA_IFARMES__' used later
				  
				  The Forced KF operand might contain following -
				  - gte(t__FORCED_KF_START_SHIFT__,n_forced*$gopInSecs) - the original forced KF setup with shift to the 'time 0'
				  - gte(t,__FORCED_KF_EXTRA_IFARMES__)*lt(t,$setExtraIFramesTimingEnd)) - generate I frames starting from half-frame before EOF chunk, till 3 frames after the chunk end (into the 2nd segement)
				*/
/*
	Temporally disable the handling of redundant I-Frames at the chunk end.
	The 'expr' was incorrect
			if(!isset($params->bf)){
				if($params->vcodec=='libx264' && in_array($params->vprofile, array('main','high') ))
					$forcedKeyFramesStr = "'expr:if(gte(t__FORCED_KF_START_SHIFT__,n_forced*$gopInSecs),gte(t,__FORCED_KF_EXTRA_IFARMES__)*lt(t,$setExtraIFramesTimingEnd))'";
				else
					$forcedKeyFramesStr = "'expr:gte(t__FORCED_KF_START_SHIFT__,n_forced*$gopInSecs)'";
			}
			else if($params->bf>0)
				$forcedKeyFramesStr = "'expr:if(gte(t__FORCED_KF_START_SHIFT__,n_forced*$gopInSecs),gte(t,__FORCED_KF_EXTRA_IFARMES__)*lte(t,$setExtraIFramesTimingEnd))'";
			else
*/
				$forcedKeyFramesStr = "'expr:gte(t__FORCED_KF_START_SHIFT__,n_forced*$gopInSecs)'";
				
//$forcedKeyFramesStr = "'expr:gte(t__FORCED_KF_START_SHIFT__,n_forced*$gopInSecs)'";
			KalturaLog::log("forcedKeyFramesStr:".$forcedKeyFramesStr);
			return $forcedKeyFramesStr;
		}
		
		/********************
		 * adjustTimeRelatedFilters
		 *	Handles WM and Subs that require per chunk adjustments
		 */
		protected function adjustTimeRelatedFilters($cmdLine, $chunkIdx, $start, $chunkWithOverlap)
		{
			KalturaLog::log("$start, $chunkWithOverlap");
			if(!isset($this->params->videoFilters->filterGraph)){
				return $cmdLine;
			}
			$cmdLineArr = explode(' ',$cmdLine);
			if(($filterIdx=array_search("-filter_complex", $cmdLineArr))===false){
				return $cmdLine;
			}
			$filterGraphBase = $this->params->videoFilters->filterGraph;
			
				/*
				 * Handle WM fade in's/out's
				 */
			$lastLabelOut = null;
			$adjustedFilterStr = null;
			if(isset($this->params->videoFilters->fadeFilters)){
				$filterGraph = clone $filterGraphBase;
				$filterGraph->entities = array();
				$filterGraphBase->LoopEntities($filterGraphBase,'iterFuncClone', $filterGraph);

				$fadeArr = array();
				$fadeArr = self::filterOutFadeFilters($this->params->videoFilters->fadeFilters, $start, $chunkWithOverlap);
				foreach($fadeArr as $idx=>$fade) {
					if(isset($fade))
						continue;
					$fade = $this->params->videoFilters->fadeFilters[$idx];
					$chainIdx = $fade->_chain->id;
					if(!array_key_exists($chainIdx,  $filterGraph->entities))
						continue;
					$filterGraph->RemoveChain($filterGraph->entities[$chainIdx]);
				}
				$adjustedFilterStr = $filterGraph->CompoundString($lastLabelOut);
			}
			
				/*
				 * Handle Subs/SRT - change original file name to splitted 
				 */
			if(isset($this->params->videoFilters->subsFilename)){
				$adjustedSubsFilename = $this->getChunkName($chunkIdx,"srt");
				if(!isset($adjustedFilterStr)){
					$adjustedFilterStr = $filterGraphBase->CompoundString($lastLabelOut);
				}
				$adjustedFilterStr = str_replace($this->params->videoFilters->subsFilename, 
										$adjustedSubsFilename, $adjustedFilterStr);
			}
			else if(isset($adjustedFilterStr)) {
				$adjustedFilterStr = str_replace($lastLabelOut, "", $adjustedFilterStr);
			}
			
				/*
				 * For the last chunk, make sure that the loop/t period does not overflow the end of the file, 
				 * causing failure on duration validation
				 */
			if($this->params->duration-$start<$chunkWithOverlap){
				$keys=array_keys($cmdLineArr,'-loop');
				if(count($keys)>0){
					foreach($keys as $key) {
						if($cmdLineArr[$key+2]=='-t')
							$cmdLineArr[$key+3] = 1;
					}
				}
			}
			
			if(isset($adjustedFilterStr) || ($adjustedFilterStr=$filterGraphBase->CompoundString($lastLabelOut))!==null) {
				KalturaLog::log("adjustedFilterStr:".$adjustedFilterStr);
				$cmdLineArr[$filterIdx+1] = '\''.$adjustedFilterStr.'\'';
				$cmdLine = implode(' ',$cmdLineArr);
			}
			
			return $cmdLine;
		}
		
		/********************
		 * adjustForcedKeyFrames
		 *	per every chunk
		 */
		protected function adjustForcedKeyFrames($cmdLine, $start, $chunkData)
		{
				// Calc the shift - round reminder
			$shift = round($start-floor($start),4);
				// Calc the timing or the extra I Frames - half frame shorter than the chunk dur
			if(isset($chunkData->frames)) {
				$extraIFramesStart = round(($chunkData->frames-0.5)*$this->params->frameDuration,4);
			}
			else {
				$extraIFramesStart = round($chunkData->duration,4);
			}
			$cmdLine = str_replace(array("__FORCED_KF_START_SHIFT__","__FORCED_KF_EXTRA_IFARMES__"), 
								array("+$shift",$extraIFramesStart), $cmdLine);
			return $cmdLine;
		}
		
		/********************
		 * 
		 */
		public function BuildVideoCommandLine($start, $chunkIdx)
		{
			$chunkFilename = $this->getChunkName($chunkIdx,"base");
			KalturaLog::log("start($start), chunkIdx($chunkIdx), chunkFilename($chunkFilename) :".date("Y-m-d H:i:s"));
			$setup = $this->setup;
			$params = $this->params;
			$chunkWithOverlap = $setup->chunkDuration + $setup->chunkOverlap;
			$cmdLine = "";
			$chunkData = $this->chunkDataArr[$chunkIdx];
			{
				kBatchUtils::addReconnectParams("http", $params->source, $cmdLine);
				$cmdLine .= " -i ".$this->cmdLine." -t $chunkWithOverlap";
				if(isset($params->httpHeaderExtPrefix)){
					$cmdLine = " -headers \"$params->httpHeaderExtPrefix,chunk($chunkIdx)\"".$cmdLine;
				}
					/*
					 * Timing repositioning should be split into two steps 
					 * - input step to 'start-backOffset sec' (default backOffset=5sec)
					 * - output step to 'backOffset' sec forward
					 * This is required to overcome some sources that does not reposition correctly. Better solution would be to reposition to the nearest KF, 
					 * but this will require long source query.
					 */
				$backOffset = 5; 
				if($start<$backOffset) {
					$cmdLine = " -ss $start".$cmdLine;
				}
				else {
					$cmdLine = " -ss ".($start-$backOffset).$cmdLine." -ss $backOffset";
				}
			
				if(isset($params->decryption_key))
					$cmdLine = " -decryption_key $params->decryption_key$cmdLine";
				
				$cmdLine = " -threads ".$this->params->threadsDec.$cmdLine;
				
				$cmdLine = $this->adjustTimeRelatedFilters($cmdLine, $chunkIdx, $start, $chunkWithOverlap);
				$cmdLine = $this->adjustForcedKeyFrames($cmdLine, $start, $chunkData);
			}
			$cmdLine = "$setup->ffmpegBin $cmdLine";

			
			if(isset($this->params->passes) && $this->params->passes==2){
				$cmdPass1 = "$cmdLine -passlogfile $chunkFilename.pass2log -pass 1 -fastfirstpass 1 -f $this->chunkFileFormat /dev/null";
				$cmdLine = "$cmdPass1 && $cmdLine -passlogfile $chunkFilename.pass2log -pass 2";
			}
			$cmdLine.= " -f segment -segment_format $this->chunkFileFormat -initial_offset $start";
			if(isset($chunkData->frames)) {
				$cmdLine.= " -segment_frames ".($chunkData->frames);
			}
			else {
				$cmdLine.= " -segment_time ".$chunkData->duration;
			}
			$cmdLine.= " -segment_start_number $chunkIdx -segment_list /dev/null $chunkFilename%d ";
			KalturaLog::log($cmdLine);
			return $cmdLine;
		}
		
		/********************
		 * fetchChunkFile
		 */
		private static function fetchChunkFile($fhd, $fileName, $rdSz=10000000, $expectedFileSize = null)
		{
KalturaLog::log("fhd:$fhd, fileName:$fileName, rdSz:$rdSz, expectedFileSize:$expectedFileSize");
			$fileName = kFile::realPath($fileName);
			if(($ifhd=fopen($fileName,"rb"))===false){
				return false;
			}
			
			$wrSz=0;
			while(!feof($ifhd)){
				$iBuf=fread($ifhd, $rdSz);
				if($iBuf===false){
					KalturaLog::log("Failed to download - on read");
					return false;
				}
				if(($sz=fwrite($fhd, $iBuf, $rdSz))===false){
					KalturaLog::log("Failed to download - on write");
					return false;
				}
				$wrSz+=$sz;
			}
			fclose($ifhd);
			KalturaLog::log("sz:$wrSz,ex:$expectedFileSize,ftell:".(ftell($fhd)).",file:".$fileName);
			
			if($expectedFileSize && $expectedFileSize != $wrSz)
				return false;
			
			return $wrSz;
		}

		/********************
		 * fetchChunkFileWithRetries
		 */
		protected static function fetchChunkFileWithRetries($fhd, $fileName, &$mergedFileSize, $retries=3, $rdSz=10000000, $expectedFileSize = null)
		{
KalturaLog::log("fhd:$fhd, fileName:$fileName, mergedFileSize:$mergedFileSize, retries:$retries=3, rdSz:$rdSz, expectedFileSize:$expectedFileSize");
			$rv = false;
			while($retries > 0) {
				$bytesWritten=self::fetchChunkFile($fhd, $fileName, $rdSz, $expectedFileSize);
				if($bytesWritten !== false) {
					$mergedFileSize += $bytesWritten;
					$rv = true;
					break;
				}
				
				$retries--;
				fseek($fhd, $mergedFileSize, SEEK_SET);
				$remoteFileSize = kFile::fileSize($fileName);
				KalturaLog::debug("Failed to download [$fileName] - rfs:$remoteFileSize,ofs:$expectedFileSize,mrg:$mergedFileSize, retrLeft:$retries");
				sleep(rand(1,3));
			}
			return $rv;
		}

		/********************
		 * ConcatChunks
		 */
		public function ConcatChunks()
		{
			/*
			Remove return to revert back to using ts concat flow
			return true;
			*/
			if($this->concatMethod=="raw")
				return $this->concatRawChunkFiles();
			else
				return $this->fetchChunkFilesForDemuxerConcat();
		}

		/********************
		 * concatRawChunkFiles
		 *	Concat chunk files by ordinary file copy, one after the other, into a concat'ed file
		 */
		private function concatRawChunkFiles()
		{
				// To contain concatenated mpegts video file
			$videoFilename = $this->getSessionName("video");
			
			stream_wrapper_restore('http');
			stream_wrapper_restore('https');
			
			$rv = true;
			$mergedFileSize = 0;
			$mode = isset($this->setup->sharedChunkPath) ? "shared" : null;
			
			$oFh=fopen($videoFilename,"wb");
			if($oFh===false){
				return false;
			}
			foreach($this->chunkDataArr as $idx=>$chunkData) {
				$originalFileSize = null;
				if(isset($chunkData->toFix)) {
					$chunkFileName = $this->getChunkName($idx,"fix");
					$originalFileSize = filesize($chunkFileName);
				}
				else {
					$chunkFileName = $this->getChunkName($idx, $mode);
					if( isset($chunkData->outFileSizes) && isset($chunkData->outFileSizes[basename($chunkFileName)]) ) {
						$originalFileSize = $chunkData->outFileSizes[basename($chunkFileName)];
					}
				}

				$rv = self::fetchChunkFileWithRetries($oFh, $chunkFileName, $mergedFileSize, 3, 10000000, $originalFileSize);
				
				if($rv==false) {
					KalturaLog::debug("Failed to build merged file, Convert will fail, bytes fetched [$mergedFileSize]");
					break;
				}
			}
			fclose($oFh);
			
			stream_wrapper_unregister('https');
			stream_wrapper_unregister('http');
			
			return $rv;
		}
		
		/********************
		 * fetchChunkFilesForDemuxerConcat
		 *	Fetch chunk files the remote storage, into a local folder
		 *	and generate FFmpeg demuxer concat list file 
		 */
		public function fetchChunkFilesForDemuxerConcat()
		{
				// To contain ffmpeg's demuxer concat list file
			$videoFilename = $this->getSessionName("video");
			
			stream_wrapper_restore('http');
			stream_wrapper_restore('https');
			
			$rv = true;
			$mergedFileSize = 0;
			$mode = isset($this->setup->sharedChunkPath) ? "shared" : null;
			
			$concatChunkListStr = null;
			foreach($this->chunkDataArr as $idx=>$chunkData) {
				$originalFileSize = null;
				$chunkDuration = 0;
				if(isset($chunkData->toFix)) {
					$chunkFileName = $this->getChunkName($idx,"fix");
					$originalFileSize = filesize($chunkFileName);
					$chunkDuration = $chunkData->toFix*$this->params->frameDuration;
				}
				else {
					$chunkFileName = $this->getChunkName($idx, $mode);
					if( isset($chunkData->outFileSizes) && isset($chunkData->outFileSizes[basename($chunkFileName)]) ) {
						$originalFileSize = $chunkData->outFileSizes[basename($chunkFileName)];
					}
					$chunkDuration = $chunkData->duration;
				}

				$fetchedChunkName = $videoFilename."_chk$idx";
				$oFh=fopen($fetchedChunkName,"wb");
				if($oFh===false){
					return false;
				}
					// Fix for chk fetch retry - 
					//   on DemuxerConcat, each chk file is downloaded separately, 
					//   thus the 'mergedFileSize' should b reset per each chk
				$mergedFileSize = 0;
KalturaLog::log("oFh:$oFh, chunkFileName:$chunkFileName, mergedFileSize:$mergedFileSize, originalFileSize:$originalFileSize");
				$rv = self::fetchChunkFileWithRetries($oFh, $chunkFileName, $mergedFileSize, 3, 10000000, $originalFileSize);
				fclose($oFh);
KalturaLog::log("fetchedFileSize:".filesize($fetchedChunkName));	
				if($rv==false) {
					KalturaLog::debug("Failed to build merged file, Convert will fail, bytes fetched [$mergedFileSize]");
					break;
				}
				$concatChunkListStr.= "file $fetchedChunkName\n"."duration $chunkDuration\n";
			}
			KalturaLog::log("List file :$videoFilename - \n$concatChunkListStr ");
			kFile::filePutContents($videoFilename, $concatChunkListStr);
			
			stream_wrapper_unregister('https');
			stream_wrapper_unregister('http');
			
			return $rv;
		}
		
		/********************
		 * BuildMergeCommandLine
		 */
		public function BuildMergeCommandLine()
		{
			$mergedFilename= $this->getSessionName();
			$vidConcatStr = $this->getSessionName("video");
			
			$mode = null;
			$setup = $this->setup;
			$mergeCmd = "$setup->ffmpegBin $this->concatSettings";
			
			/* Remove comment to re-enable chunk concat via ffmpeg
			$vidConcatStr = "concat:'";
			if($this->setup->sharedChunkPath) {
				$mode = "shared";
				$mergeCmd .= " -protocol_whitelist \"concat,file,https,http,tls,tcp\" ";
			}
			
			foreach($this->chunkDataArr as $idx=>$chunkData){
				if(isset($chunkData->toFix))
				{
					$chunkFileName = $this->getChunkName($idx,"fix");
				}
				else
				{
					$chunkFileName = $this->getChunkName($idx, $mode);
					$chunkFileName = kFile::realPath($chunkFileName);
				}
				$vidConcatStr.= $chunkFileName.'|';
			}
			
			$vidConcatStr = rtrim($vidConcatStr, '|');
			$vidConcatStr.= "'";
			*/
			
			$params = $this->params;
			$audioInputParams = null;
			if(isset($params->acodec)) {
				$mode = $this->setup->sharedChunkPath ? "shared_audio" : "audio";
				$audioFilename = $this->getSessionName($mode);
				if($setup->duration!=-1){
					$fileDt = self::getMediaData($audioFilename);
					if(isset($fileDt) && round($fileDt->containerDuration,4)>$params->duration) {
						$audioInputParams = " -t ".$params->duration;
						KalturaLog::log("cut input audio to ".$params->duration);
					}
				}
				if($this->chunkFileFormat=="mpegts")
					$audioInputParams.= " -itsoffset -1.4";
				
				$resolvedAudioFileName = kfile::realPath($audioFilename);
				kBatchUtils::addReconnectParams("http", $resolvedAudioFileName, $audioInputParams);
				$audioInputParams.= " -i '$resolvedAudioFileName'";
				$audioCopyParams = "-map 1:a -c:a copy";
				if($params->acodec=="libfdk_aac" || $params->acodec=="libfaac")
					$audioCopyParams.= " -bsf:a aac_adtstoasc";
			}
			else{
				$audioCopyParams = null;
			}
			
			if(isset($params->fps)) $mergeCmd.= " -r ".$params->fps;
			if($this->chunkFileFormat=="mpegts") {
					// Main/High h264 porfiles come with larger (1.48) offset
				if($params->vcodec=="libx264" && $params->vprofile!="baseline")
					$mergeCmd.= " -itsoffset -1.48";
				else
					$mergeCmd.= " -itsoffset -1.4";
			}
			
			/* Disable separate merging of video chunks
			//$mergeCmd.= " -i $videoFilename";
			*/
			
			$mergeCmd.= " -i $vidConcatStr";
			$mergeCmd.= "$audioInputParams -map 0:v:0 -c:v copy $audioCopyParams";
			if(isset($params->formatParams))
				$mergeCmd.= " ".$params->formatParams;
			$mergeCmd.= " -f $params->format -copyts";
				/*
				 * Following code uses ffmpeg patch to clean up mpeg-ts leftovers from remuxed mp4 files.
				 * Required for udrm support
				 */
			if($params->format=="mp4") {
				$ffmpegVerStr = self::getFFMpegVersion($setup->ffmpegBin);
				/*
				 * This fix is required for uDRM support. 
				 * FFmpeg 3.2 got Kaltura patch. FFMpeg 4 comes with native solution that includes H265 as well.
				 * Manually set 'bsf' will override the auto stiing. Needed for CAE caps passthrough support
				 */
				if(isset($params->bsf)) {
					$mergeCmd.= " -bsf:v $params->bsf";
				}
				else if($params->vcodec=="libx264")
					$mergeCmd.= ((int)$ffmpegVerStr<4)?" -nal_types_mask 0x3e":" -bsf:v filter_units=pass_types=1-5";
				else if($params->vcodec=="libx265")
					$mergeCmd.= ((int)$ffmpegVerStr<4)?" -nal_types_mask 0xffffffff":" -bsf:v filter_units=pass_types=0-31";
			}

			if(($key=array_search("-tag:v", $params->cmdLineArr))!==false) {
				$mergeCmd.= " -tag:v ".$params->cmdLineArr[$key+1];
			}
			
			if(isset($params->metadata)) {
				$metadataStr = null;
				foreach($params->metadata as $metadata){
					$metadataStr.=" ".implode(" ", $metadata);
				}
				$mergeCmd.= $metadataStr;
			}
			
			$mergeCmd.= " -y $mergedFilename";
			KalturaLog::log("mergeCmd:\n$mergeCmd ".date("Y-m-d H:i:s"));
			return $mergeCmd;
		}

		/********************
		 *
		 */
		public function BuildFixVideoCommandLine($idx, $chunkOutputFileList = array())
		{
			$chunkData = $this->chunkDataArr[$idx];
			$chunkFixName = $this->getChunkName($idx, "fix");
			$params = $this->params;

			$cmdLine = $this->setup->ffmpegBin." $this->concatSettings";
			if($this->chunkFileFormat=="mpegts"){
					/* Main/High h264 porfiles come with larger (1.48) offset
						*** it turned ou thet on fix flow it should be always 1.4
				if($params->vcodec=="libx264" && $params->vprofile!="baseline")
					$cmdLine.= " -itsoffset -1.48";
				else */
					$cmdLine.= " -itsoffset -1.4";
			}
			if(isset($params->fps)) 
				$cmdLine.= " -r ".$params->fps;
			
			$mode = "base";
			if($this->setup->sharedChunkPath) {
				$mode = "shared_base";
				$cmdLine .= " -protocol_whitelist \"concat,file,https,http,tls,tcp\"";
			}
			
			$firstSegmentName = $this->getChunkName($idx, $mode).($idx);
			$secondSegmentName = $this->getChunkName($idx, $mode).($idx+1);
			KalturaLog::debug("firstSegmentName: $firstSegmentName , secondSegmentName: $secondSegmentName");
			
			$resolvedFirstSegmentName = kFile::realPath($firstSegmentName);
			$resolvedSecondSegmentName = kFile::realPath($secondSegmentName);
			KalturaLog::debug("resolvedFirstSegmentName: $resolvedFirstSegmentName, resolvedSecondSegmentName: $resolvedSecondSegmentName");
			
			$chunkSegmentsConcatListStr = null;
			if( (count($chunkOutputFileList) && in_array($secondSegmentName, $chunkOutputFileList)) || kFile::checkFileExists($secondSegmentName)) {
				if($this->concatMethod=="raw")
						// Use ffmpeg concat protocol method
					$cmdLine.= " -i concat:'".$resolvedFirstSegmentName."|".$resolvedSecondSegmentName."'";
				else {
						// Use ffmpeg concat demuxer method
					$chunkSegmentsConcatListStr = "file $resolvedFirstSegmentName\n"."duration $chunkData->duration\n";
					$chunkSegmentsConcatListStr.= "file $resolvedSecondSegmentName\n"."duration ".($this->setup->chunkOverlap)."\n";
					$chunkSegmentsConcatListName = $chunkFixName."list";
					if(kFile::filePutContents($chunkSegmentsConcatListName, $chunkSegmentsConcatListStr)==false)
						KalturaLog::log("FAILED to write $chunkSegmentsConcatListName");
					$cmdLine.= " -i $chunkSegmentsConcatListName";
					KalturaLog::log("chk $idx segment concat list -\n".$chunkSegmentsConcatListStr);
				}
			}
			else {
				kBatchUtils::addReconnectParams("http", $resolvedFirstSegmentName, $cmdLine);
				$cmdLine.= " -i \"$resolvedFirstSegmentName\"";
			}
				
			$start = $chunkData->start; 
			$segmentTime = $chunkData->duration+(1/$params->fps*1.5);
			$cmdLine.= " -map 0:v -c:v copy -f ".$this->chunkFileFormat;
			$cmdLine.= " -frames:v ".$chunkData->toFix;
			$cmdLine.= " -copyts -y ".$chunkFixName;
			KalturaLog::log("fix cmdLine: $cmdLine");
			
			return $cmdLine;
		}
		
		/********************
		 * rv  - null(no audio)
		 */
		public function BuildAudioCommandLine()
		{
			$params = $this->params ;
			$sourceFileDt = $this->sourceFileDt;
			/*
			 * Check whether the audio is required - 
			 * - is there audio in the source
			 * - does the asset needs audio
			 */
			if(!((isset($sourceFileDt->audioFormat) || isset($sourceFileDt->audioCodecId) 
			 || isset($sourceFileDt->audioDuration) || isset($sourceFileDt->audioBitRate) 
			 || isset($sourceFileDt->audioBitRateMode) ||	isset($sourceFileDt->audioChannels)
			 || isset($sourceFileDt->audioSamplingRate))
			&& (isset($params->acodec) || isset($params->abitrate) 
			 || isset($params->ar) || isset($params->ac)) )){
				KalturaLog::log("No audio in the source!");
				return null;
			}

			/*
			 * Generate audio cmd line
			 */
			 
			 /*
			  * 'amerge' does not work properly with source side 'async'
			  * The code below removes 'async' this case
			  */
			$asyncStr = "-async 1";
			if(isset($params->audioFilters)){
				$filterStr = "-filter_complex ".($params->audioFilters);
				if(strstr($filterStr,"amerge")!==false){
					$asyncStr = null;
				}
			}
			else 
				$filterStr = null;
			
			$setup = $this->setup;
			$cmdLine = $setup->ffmpegBin;
			if(isset($asyncStr))
				$cmdLine.= " $asyncStr";
			if(isset($setup->startFrom))
				$cmdLine.= " -ss $setup->startFrom";
			if(isset($params->decryption_key))
				$cmdLine.= " -decryption_key $params->decryption_key";
			if(isset($params->httpHeaderExtPrefix)){
				$cmdLine.= " -headers \"$params->httpHeaderExtPrefix,audio\"";
			}
			if(isset($params->sourceForAudio))
				$sourcePath = $params->sourceForAudio;
			else
				$sourcePath = $params->unResolvedSourcePath;
			kBatchUtils::addReconnectParams('http', $params->source, $cmdLine);
			$cmdLine.= " -i \"$sourcePath\"";
			$cmdLine.= " -vn";
			if(isset($params->acodec)) $cmdLine.= " -c:a ".$params->acodec;
			if(isset($filterStr))
				$cmdLine.= " $filterStr";
			
			if(isset($params->mappings)) {
				$mappings = implode(" -map ",$params->mappings);
				$cmdLine.= " -map ".implode(" -map ",$params->mappings);
			}
			else
				$cmdLine.= " -map 0:a:0";
			
			$cmdLine.= " -metadata:s:a:0 language=";

			if(isset($params->abitrate)) $cmdLine.= " -b:a ".$params->abitrate."k";
			if(isset($params->ar)) $cmdLine.= " -ar ".$params->ar;
			if(isset($params->ac)) $cmdLine.= " -ac ".$params->ac;
			if(isset($params->volume)) $cmdLine.= " -vol ".$params->volume;
			$durStr = null;
			if($setup->duration!=-1) $durStr = " -t ".$setup->duration;
			$cmdLine.= "$durStr -f $this->chunkFileFormat -y ".$this->getSessionName("audio");
			
			KalturaLog::log("cmdLine:$cmdLine");
			return $cmdLine;
		}

		/********************
		 * Calculate the drift that is caused by non-integer fps (29.97, ...)
		 */
		protected function calcChunkDrift($duration=null)
		{
			if(!isset($duration)){
				$duration = $this->setup->chunkDuration;
			}
			$driftRatio = round($this->params->fps*$duration)-$this->params->fps*$duration;
			if($driftRatio<0) $driftRatio = 1 + $driftRatio;
			
			$drift = $driftRatio/$this->params->fps;
			return $drift;
		}
		
		/********************
		 *
		 */
		public function CheckChunksContinuity() 
		{
			$prevObjIdx = null;
			$toFixCnt = 0;
			$frameDuration = $this->params->frameDuration;
			foreach($this->chunkDataArr as $idx=>$chunkData){
				$chunkName = $this->getChunkName($idx);
/* Discontinuity check by analyzing chunk generation log file */
				$stat = $chunkData->stat;
				if(isset($stat->finish) && isset($stat->type)) 
				{
					if(isset($prevObjIdx)) {
						$prevObj = $this->chunkDataArr[$prevObjIdx]->stat;
						if(isset($prevObj->finish) && isset($prevObj->type)) 
						{
							$timeGap = $stat->start - $prevObj->finish;
							KalturaLog::log($chunkName.":".json_encode($stat).":timeGap:".round($timeGap,9));
							if($timeGap>1.5*$frameDuration){
								$toFix = $prevObj->frame+round($timeGap/$frameDuration)-1;
								$toFixCnt++;
								KalturaLog::log("Discontinuity: Hole, index($prevObjIdx), frameDur($frameDuration), gap($timeGap), frame($prevObj->frame), toFix($toFix)");
								$this->chunkDataArr[$prevObjIdx]->toFix = $toFix;
							}
							else if($timeGap<0.5*$frameDuration){
								$toFix = $prevObj->frame-round(abs($timeGap/$frameDuration))-1;
								$toFixCnt++;
								KalturaLog::log("Discontinuity: Overlap, index($prevObjIdx), frameDur($frameDuration), gap($timeGap), frame($prevObj->frame), toFix($toFix)");
								$this->chunkDataArr[$prevObjIdx]->toFix = $toFix;
							}
						}
						else KalturaLog::log("Invalid prev ChunkData (idx=$prevObjIdx)");
					}
				}
				else KalturaLog::log("Invalid current ChunkData (idx=$idx)");
				$prevObjIdx = $idx;
				$stat = null;
			}
			if($toFixCnt>0) {
				KalturaLog::log("Discontinuity cases ($toFixCnt out of ".count($this->chunkDataArr).")");
			}
			else 
				KalturaLog::log("Contiguous chunks!");
			return $toFixCnt;
		}

		/********************
		 * 
		 */
		public function ValidateMergedFile(&$msgStr)
		{
			$mergedFilename = $this->getSessionName();
			$fileDt = $this->getMediaData($mergedFilename);
			if(!isset($fileDt)){
				KalturaLog::log($msgStr="FAILED to merge, missing merged file ($mergedFilename)!");
				return false;
			}
			$this->mergedFileDt = $fileDt;
			
			$fileDtSrc = $this->sourceFileDt;
			$vDelta = ($fileDtSrc->videoDuration>0)? round(($fileDt->videoDuration - $fileDtSrc->videoDuration)/1000,4): null;
			$aDelta = ($fileDtSrc->audioDuration>0)? round(($fileDt->audioDuration - $fileDtSrc->audioDuration)/1000,4): null;
			$cDelta = ($fileDtSrc->containerDuration>0)? round(($fileDt->containerDuration - $fileDtSrc->containerDuration)/1000,4): null;
			$maxMergeDelta = 2;
			if((isset($vDelta) && abs($vDelta)>$maxMergeDelta) 
			|| (isset($aDelta) && abs($aDelta)>$maxMergeDelta) 
//			|| (isset($cDelta) && abs($cDelta>$maxMergeDelta))
			){
				KalturaLog::log($msgStr="FAILED to merge, delta to source is too large - (v:$vDelta,a:$aDelta,c:$cDelta), max allowed $maxMergeDelta sec!");
				return false;
			}
			return true;
		}

		/********************
		 * updateChunkFileStatData
		 *	Retrieve chunk stat data and update the chunks data array
		 */
		public function updateChunkFileStatData($idx, $stat, $outFileSizes = array())
		{
			$this->chunkDataArr[$idx]->stat = $stat;
			$this->chunkDataArr[$idx]->outFileSizes = $outFileSizes;
		}
		
		/********************
		 * mode: base, fix, [0...n]
		 */
		public function getSessionName($mode="merged")
		{
			switch($mode){
			case "merged":
				$name = $this->params->output;
				//$name = $this->setup->output."_merged";
				break;
			case "audio":
				$name = $this->setup->output."_audio";
				break;
			case "shared_audio":
				$name = $this->setup->sharedChunkPath . "_audio";
				break;
			case "qpfile":
				$name = $this->setup->output."_qpfile";
				break;
			case "log":
				$name = $this->setup->output."_session.log";
				break;
			case "session":
				$name = $this->setup->output.".ses";
				break;
			case "concat":
				$name = $this->setup->output."_concat.log";
				break;
			case "video":
				$name = sys_get_temp_dir() . '/' . basename($this->setup->output) . "_$mode";
				break;
			default:
				$name = $this->setup->output."_$mode";
				break;
			}
			return $name;
		}

		/********************
		 * mode: base, fix, [0...n]
		 */
		public function getChunkName($chunkIdx, $mode=null)
		{
			$name = $this->setup->output."_$this->chunkEncodeToken"."_$chunkIdx.";
			switch($mode){
			case null:
				$name.= "$this->videoChunkPostfix".$chunkIdx;
				break;
			case "shared":
				$name = $this->setup->sharedChunkPath . "_$this->chunkEncodeToken"."_$chunkIdx.";
				$name.= "$this->videoChunkPostfix".$chunkIdx;
				break;
			case "fix":
				$name.= "$this->videoChunkPostfix".$chunkIdx.".fix";
				$name='/tmp/'.basename($name);
				break;
			case "base":
				$name.= "$this->videoChunkPostfix";
				break;
			case "shared_base":
				$name = $this->setup->sharedChunkPath."_$this->chunkEncodeToken"."_$chunkIdx.";
				$name.= "$this->videoChunkPostfix";
				break;
			case "srt":
				//When splitting subtitles we need to write output file to shared location for handling env that run tmp as local path (cloud storage)
				$name = dirname($this->params->videoFilters->subsFilename) . "/" . basename($name) . "srt";
				break;
			default:
				$name.= "$this->videoChunkPostfix".$mode;
				break;
			}
			return $name;
		}

		/********************
		 *
		 */
		protected function calculateChunkTimings_forBframeFixing()
		{
			$setup = $this->setup;
			$params = $this->params;

				/*
				 * Generate the pre-planned chunk params (start, frames, ...)
				 */
			$start = $setup->startFrom;
			$finish = $setup->startFrom+$params->duration;
			$frameRate = $params->fps;
			
			$framesInChunk =  $setup->chunkDuration*$frameRate;
			$framesInChunkRemainder = $framesInChunk-(int)(floor($framesInChunk));
			$frameDuration = $params->frameDuration;
			$totalChunksNumber = ceil($params->duration/$setup->chunkDuration);
			KalturaLog::log("framesInChunk:$framesInChunk, framesInChunkRemainder:$framesInChunkRemainder, totalChunksNumber:$totalChunksNumber, frameDur:$frameDuration");
			$chunkFirstFrame = 0; 
			$framesTotal = 1;
			$remainder = 0;
			
			for($idx=0; $idx<$totalChunksNumber; $idx++) {
				$chunkFirstFrame = $framesTotal;
				$framesCount = (int)(floor($framesInChunk));
				if($remainder>=1) {
					$framesCount++;
					$remainder-=1;
				}
				$framesTotal += $framesCount;
				$chunkStartTime = round($chunkFirstFrame*$frameDuration,6);
				$chunkLastFrameTime = round(($framesTotal-1)*$frameDuration,6);
				$chunkDur = round(($framesTotal-$chunkFirstFrame)*$frameDuration,6);
				KalturaLog::log("idx:$idx, fstFrm:$chunkFirstFrame, frmCnt:$framesCount, fstFrmTm(sec):$chunkStartTime, chkDur:$chunkDur, lstFrmTm(sec):$chunkLastFrameTime, framesTotal:$framesTotal, remainder:$remainder");
				$remainder = round($remainder+$framesInChunkRemainder,6);
				$chunkData = new KChunkData($idx, $chunkStartTime, $chunkDur, $framesCount);
				KalturaLog::log(json_encode($chunkData));
				$this->chunkDataArr[$idx] = $chunkData;
			}
		}
			
		/********************
		 *
		 */
		protected static function fixChunkStartTimingsForBfarmes($sourceFilename, $frameDuration, &$chunkDataArr)
		{
			foreach($chunkDataArr as $chkIdx=>$chunkData){
				if($chkIdx==0)
					continue;
				$framesStatArr = KChunkFramesStat::getFrameData($sourceFilename, $chunkData->start-1, 1);
				if(!isset($framesStatArr))
					continue;
				
				$iFrame = new stdClass(); $iFrame->index = $iFrame->delta = null;
				$pFrame = clone $iFrame;
				$bFrame = clone $iFrame;
				
				foreach($framesStatArr as $fIdx=>$frameStat){
					switch($frameStat->type){
					case 'I':
						if(!isset($iFrame->index) || (abs($chunkData->start-$frameStat->start)<abs($iFrame->delta))){
							$iFrame->delta = $chunkData->start-$frameStat->start;
							$iFrame->index = $fIdx;
						}
						break;
					case 'P':
						if(!isset($pFrame->index) || (abs($chunkData->start-$frameStat->start)<abs($pFrame->delta))){
							$pFrame->delta = $chunkData->start-$frameStat->start;
							$pFrame->index = $fIdx;
						}
						break;
					case 'B':
						if(!isset($bFrame->index) || (abs($chunkData->start-$frameStat->start)<abs($bFrame->delta))){
							$bFrame->delta = $chunkData->start-$frameStat->start;
							$bFrame->index = $fIdx;
						}
						break;
					}
						// Stop the scan through the frameStat array when it passes over the 
						// alleged chunk start position
					if($iFrame->delta<0 && $pFrame->delta<0 && $bFrame->delta<0)
						break;
				}
				
				if(abs($bFrame->delta)<abs($iFrame->delta) && abs($bFrame->delta)<abs($pFrame->delta)){
					if(abs($pFrame->delta)<abs($iFrame->delta))
						$fixFrmSt = $framesStatArr[$pFrame->index];
					else
						$fixFrmSt = $framesStatArr[$iFrame->index];
					$gap = $fixFrmSt->start-$chunkData->start;
					$framesInGap = $gap/$frameDuration;
					KalturaLog::log("BINGO - chunkStart:$chunkData->start, fixStart:$fixFrmSt->start(".round($gap,6)."),".json_encode($fixFrmSt));
					if(abs($gap>0)) {
						$chunkData = $chunkDataArr[$chkIdx-1];
						$chunkData->duration+= $gap;
						$chunkData->frames += round($framesInGap);
						$chunkData->start+= $gap;
						$chunkDataArr[$chkIdx-1] = $chunkData;
						$chunkData = $chunkDataArr[$chkIdx];
						$chunkData->duration-= $gap;
						$chunkData->frames -= round($framesInGap);
						$chunkDataArr[$chkIdx] = $chunkData;
						KalturaLog::log(json_encode($chunkDataArr[$chkIdx]));
					}
//					$chunkDataArr[$chkIdx] = $chunkData;
				}
			}
			return;
		}

		/********************
		 *
		 */
		protected function calculateChunkTimings()
		{
			$setup = $this->setup;
			$params = $this->params;

				/*
				 * Generate the pre-planned chunk params (start, frames, ...)
				 */
$roundDgts=4;
			$start = round($setup->startFrom,$roundDgts);
//			$this->chunkDataIdx=round($start/$this->setup->chunkDuration);
			$finish = round($setup->startFrom+$params->duration,$roundDgts);
			$duration = round($setup->chunkDuration+$this->calcChunkDrift(),$roundDgts);
			$frameDuration = $params->frameDuration;
			$idx = 0;
			while($finish-$start>$frameDuration) {
				$chunkData = new KChunkData($idx, $start, $duration);
				if($idx>0) {
					$this->chunkDataArr[$idx-1]->calcGapToNext($start, $frameDuration);
				}
				$this->chunkDataArr[$idx++] = $chunkData;
		
				$start += round($setup->chunkDuration+$this->calcChunkDrift(),$roundDgts);
				$delta = round($start-$idx*$setup->chunkDuration,$roundDgts);
				$duration = round($setup->chunkDuration+$this->calcChunkDrift(),$roundDgts);
				if($frameDuration<$delta) {
					KalturaLog::log("idx($idx)- remove frame - frameDuration($frameDuration), delta($delta)");
					$start-=($frameDuration);
				}
				else if($delta<0 && $frameDuration>-$delta) {
					KalturaLog::log("idx($idx)- add frame - frameDuration($frameDuration), delta($delta)");
					$start+=($frameDuration);
				}
			}
			$chunkData->calcGapToNext($params->duration, $frameDuration);
		}

		/********************
		 *
		 */
		protected static function getMediaData($fileName)
		{
			try {
				$medPrsr = new KFFMpegMediaParser($fileName);//new KMediaInfoMediaParser($fileName);
				$m=$medPrsr->getMediaInfo();
				return $m;
			}
			catch(Exception $ex){
				KalturaLog::log($ex->getMessage()."... Leaving");
				return null;
			}
		}

		/********************
		 *
		 */
		protected static function filterOutFadeFilters($filters, $start, $dur)
		{
			$arr = array();
			
			foreach($filters as $fade){
				KalturaLog::log("chunk(start:$start, dur:$dur),fade($fade->start_time,$fade->duration)");
				if(!isset($fade->start_time)){
					$arr[] = $fade;
					continue;
				}
					// The whole 'fade' contained inside the current single chunk
				if($start<=$fade->start_time && $start+$dur>=$fade->start_time){
					$arr[] = $fade;
					continue;
				}
					// The 'fade' start contained inside the current chunk
				if($start<=$fade->start_time && $start+$dur>=$fade->start_time){
					$arr[] = $fade;
					continue;
				}
					// The 'fade' end contained inside the current chunk
				if(isset($fade->duration)) {
					if($start<=$fade->start_time+$fade->duration && $start+$dur>=$fade->start_time+$fade->duration){
						$arr[] = $fade;
						continue;
					}
				}
				
				$arr[] = null;
					
			}
			KalturaLog::log(print_r($arr,1));
			return $arr;
		}

		/********************
		 *
		 */
		protected static function getFFMpegVersion($ffmpegBin)
		{
			KalturaLog::log($ffmpegBin);
			$lastLine=exec("$ffmpegBin -version" , $outputArr, $rv);
			if($rv!=0) {
				KalturaLog::log("ERROR: failed to run $ffmpegBin binary.");
				return null;
			}
			$str = $strVer = null;
			sscanf($outputArr[0],"%s %s %s ",$str,$str,$strVer);
			KalturaLog::log("$strVer");
			return $strVer;
		}
		
		/********************
		 * deleteTmpMergedVideoFile
		 */
		public function deleteTmpMergedVideoFile()
		{
			$localTmpConcatVideoFilePath = $this->getSessionName("video");
			
			if($this->concatMethod=="raw") {
				$localTmpConcatVideoFilePath = $this->getSessionName("video");
				foreach($this->chunkDataArr as $idx=>$chunkData) {
					$localTmpChunkFilePath = $localTmpConcatVideoFilePath."_chk$idx";
					if(file_exists($localTmpChunkFilePath)) {
						if(!unlink($localTmpChunkFilePath)) {
							KalturaLog::warning("Failed to delete local the tmp video concat file from [$localTmpChunkFilePath]");
						}
					}
				}
			}
			
			if(file_exists($localTmpConcatVideoFilePath)) {
				KalturaLog::debug("Deleting local copy of the tmp video concat file from [$localTmpConcatVideoFilePath]");
				if(!unlink($localTmpConcatVideoFilePath)) {
					KalturaLog::warning("Failed to delete local the tmp video concat file from [$localTmpConcatVideoFilePath]");
				}
			}
		}
		/********************
		 * calcMaxExecutionTime
		 */
		public function calcMaxExecutionTime($maxExecutionTime)
		{
			KalturaLog::log("max:$maxExecutionTime");
			/*
			 * Adjust chunk's default maxExecutionTime (matches FHD)
			 * to all resolutions and to audio stream generation
			 */
$vMax = 0;
$aMax = 0;
				// Video maxExecutionTime adjusted relatively to resolution pix count/frame area
			if(isset($this->params->width) && isset($this->params->height) && isset($this->setup->chunkDuration)) {
				$vMax=round(($maxExecutionTime/(1920*1080))*($this->params->width*$this->params->height)*($this->setup->chunkDuration/60));
			}
			if($vMax==0) $vMax = $maxExecutionTime;
			else if($vMax<1800)	$vMax = 1800;
			$vMax = max($vMax,30);
			
				// Audio maxExecutionTime adjusted to content duration (no chunks for audio)
			if(isset($this->params->duration) && $this->params->duration>0)
				$aMax=round($this->params->duration/2);
			else $aMax = round($maxExecutionTime);
				// Min execution timeout - at least 30s, to prevent TO's of very short contents
			$aMax = max($aMax,30);

				// Workarround for long conversions of large MXF file stored on S3
			if($this->sourceFileDt->containerFormat=="mxf"){
				$aMax*= 2;
				$vMax*= 2;
			}
				// Very HI br sources tend to cause VERY slow wud transcoding
			else if($this->sourceFileDt->videoBitRate > 200000) {
				$aMax*= 10;
				$vMax*= 2;
			}
if($this->params->vcodec=="libaom-av1")
$vMax*=2;
			KalturaLog::log("vMax:$vMax, aMax:$aMax");
			return array($vMax,$aMax);
		}
		
		/* ---------------------------
		 * validateChunkDuration
		 */
		public function validateChunkDuration($chunkData, $start, $finish)
		{
			$chunkDurThreshInSec=$this->chunkDurThreshInFrames*$this->params->frameDuration;
			{
					/*
					 * Validate chunk dur
					 */
					// Calc the generated chunk duration
				$generatedChunkDur = $finish-$start;
				if($chunkData->gap-$chunkDurThreshInSec > $generatedChunkDur){
					$msgStr = "Chunk id ($chunkData->index): too short chunk dur - $generatedChunkDur";
					$msgStr.= ", should be ".round($chunkData->gap,4).", thresh:".round($chunkDurThreshInSec,4);
					$msgStr.= ", delta:".round($chunkData->gap-$generatedChunkDur,4);
					KalturaLog::log($msgStr);
					return false;
				}
			}
			return true;
		}
	}
	
	/********************
	 *
	 */
	class KChunkedEncodeParams4 {
		public $vcodec = null;
		public $vprofile =  null;
		public $vbitrate =  null;
		public $width =  null;
		public $height =  null;
		public $fps =  null;
		public $gop =  null;
		public $aspect = null;
		public $acodec =  null;
		public $aprofile =  null;
		public $abitrate =  null;
		public $ar =  null;
		public $ac =  null;
		public $volume =  null;
		public $format =  null;
		public $flags =  null;
		public $x264opts = null;
		public $filter_complex = null;
		public $source =  null;
		public $output = null;
		
		public $sc_threshold = null;	// scene cut
		public $subq = null;
		public $qcomp = null;
		public $qmin = null;
		public $qmax = null;
		public $qdiff = null;
		public $coder  = null;
		public $bf = null;
		public $force_key_frames = null;
//		-pix_fmt yuv420p
		public $cmp = null; // 256
		public $partitions = null; // +parti4x4+partp8x8+partb8x8
		public $trellis = null; // 1
		public $refs = null; // 1
		public $me_range = null; // 16
		public $keyint_min = null; // 20
		public $i_qfactor = null; // 0.71
		public $bt = null; // 100k
		public $maxrate = null; // 400k
		public $bufsize = null; // 1200k
		public $rc_eq = null; // 'blurCplx^(1-qComp)'
		public $level = null; // 30
									// If not set -
		public $threads = null; 	// 	The encoding threads set to 4
		public $threadsDec = null;	//	The decoding threads - for SD:7, >SD:5
		public $decryption_key = null;
		
		public $passes = null;

		public $videoFilters =  null;
		public $audioFilters =  null;
		public $formatParams =  null;
		
		public $duration = null;
		public $frameDuration = null;

		public $cmdLineArr = array();
		
		public $session = null;
		public $httpHeaderExtPrefix = null;
				// AWS 'direct' signed source urls expire after 12hrs,
				// on long contents chunks might have longer pending times,
				// therefore the AWS path resolving need to be done as close as possible 
				// to chunk processing. resolved url stored in the 'source' field.
				// The fields bellow used to manage the path resolving process
		public $unResolvedSourcePath = null;
		public $pathResolveTime = null;			// Last source path resolve time
		public $pathResolveInterval = 3600*10;	// Time interval between resolves (10hrs)
		
		/********************
		 * 
		 */
		public function __construct() 
		{
			$this->videoFilters =  new stdClass();
		}

		/********************
		 *
		 */
		public function GetEncodingSettings($cmdLine) 
		{
			$cmdLineArr = explode(" ",$cmdLine);
			
			KalturaLog::log("cmdLineArr:".print_r($cmdLineArr,1));
				// On 'firstpass' session - get out. 
			if(($key=array_search("-pass", $cmdLineArr))!==false){
				$cmdLineArr = array_slice($cmdLineArr, $key);
				if(($key=array_search("-i", $cmdLineArr))!==false) {
					$cmdLineArr = array_slice($cmdLineArr, $key);
				}
				if(($key=array_search("-passlogfile", $cmdLineArr))!==false) {
					unset($cmdLineArr[$key+1]);
					unset($cmdLineArr[$key]);
				}
				if(($key=array_search("-pass", $cmdLineArr))!==false) {
					unset($cmdLineArr[$key+1]);
					unset($cmdLineArr[$key]);
				}
				$this->passes = 2;
			}
			foreach($cmdLineArr as $key=>$opt) 
				if(trim($opt)==null || strlen($opt)==0) unset($cmdLineArr[$key]);

			$this->parseEncodingSettings($cmdLineArr);
				// Remove the '-i' and the 'output' from the cmd-line, those will be managed at the last stage
			array_pop($cmdLineArr);
			array_shift($cmdLineArr);
			$this->cmdLineArr = $cmdLineArr;
			
			$this->session = basename($this->output);
			
				// In case of remote source, set http-header-ext to improve access logging
			$urlArr = parse_url ($this->source);
			if($urlArr!==false && key_exists('host',$urlArr)) {
				$this->httpHeaderExtPrefix = "User-Agent: Kaltura Chunked Encoding,session($this->session)";
			}
			return 0;
		}
		
		/********************
		 *
		 */
		protected function parseEncodingSettings(&$cmdLineArr) 
		{
			foreach($cmdLineArr as $idx=>$val){
				switch($val){
				case "-c:v":
				case "-vcodec":
					$this->vcodec = $cmdLineArr[$idx+1];
					break;
				case "-vprofile":
				case "-profile:v":
					$this->vprofile = $cmdLineArr[$idx+1];
					break;
				case "-b":
				case "-b:v":
					$this->vbitrate = (int)$cmdLineArr[$idx+1];
					break;
				case "-s":
					list($wid,$hgt) = explode("x",$cmdLineArr[$idx+1]);
					$this->width = $wid;
					$this->height = $hgt;
					break;
				case "-r":
					$this->fps = $cmdLineArr[$idx+1];
					break;
				case "-g":
					$this->gop = $cmdLineArr[$idx+1];
					break;
					
				case "-aprofile":
				case "-profile:a":
					$this->aprofile = $cmdLineArr[$idx+1];
					break;
				case "-b:a":
				case "-ab":
					$this->abitrate = (int)$cmdLineArr[$idx+1];
					break;
				case "-c:a":
				case "-acodec":
					$this->acodec = $cmdLineArr[$idx+1];
					break;
				case "-vol":
					$this->volume = $cmdLineArr[$idx+1];
					break;
					
				case "-f":
					$this->format = $cmdLineArr[$idx+1];
					break;
				case "-maxrate":
				case "-bufsize":
					$val = ltrim($val,'-');
					$this->$val = (int)$cmdLineArr[$idx+1];
					break;
				case "-flags":
				case "-x264opts":
				case "-vf":
				case "-af":
				case "-filter_complex":
					$val = ltrim($val,'-');
					if(isset($this->$val))
						$arr = $this->$val;
					else
						$arr = array();
					$arr[$idx] = $cmdLineArr[$idx+1];
					$this->$val = $arr;
					break;
				case "-decryption_key":
					$val = ltrim($val,'-');
					$this->$val = $cmdLineArr[$idx+1];
					unset($cmdLineArr[$idx+1]);
					unset($cmdLineArr[$idx]);
					break;
				case "-ar":
				case "-ac":
				case "-subq":
				case "-qcomp":
				case "-qmin":
				case "-qmax":
				case "-qdiff":
				case "-coder":
				case "-bf";
				case "-aspect":
				case "-cmp":
				case "-partitions":
				case "-trellis":
				case "-refs":
				case "-me_range":
				case "-keyint_min":
				case "-i_qfactor":
				case "-sc_threshold":
				case "-bt":
				case "-rc_eq":
				case "-level":
				case "-threads":
				case "-force_key_frames":
					$val = ltrim($val,'-');
					$this->$val = $cmdLineArr[$idx+1];
					break;
				case "-bsf:v":
					$this->bsf = $cmdLineArr[$idx+1];
					break;
				case "-map":
					if(!isset($this->mappings))
						$this->mappings = array();
					$this->mappings[] = $cmdLineArr[$idx+1];
					break;
				default:
					if(strpos($val,"-metadata")!==false){
						if(!isset($this->metadata))
							$this->metadata = array();
						$this->metadata[] = array($cmdLineArr[$idx],$cmdLineArr[$idx+1]);
					}
					break;
				}
			}

			if(isset($this->filter_complex) && count($this->filter_complex)>0) {
				foreach($this->filter_complex as $idx=>$val){
					$filterStr = self::adjustFilters($cmdLineArr[$idx+1], 'video');
					if(isset($filterStr)) {
						$this->videoFilters->filterStr = $filterStr;
					}
					$filterStr = self::adjustFilters($cmdLineArr[$idx+1], 'audio');
					if(isset($filterStr))
						$this->audioFilters = $filterStr;
				}
				if(isset($this->videoFilters->filterStr)){
						/*
						 * Check and prepare for 'fade' filters
						 */
					$filterStr = trim($this->videoFilters->filterStr,'\'');
					$filterGraph = new KFFmpegFilterGraph4();
					$filterGraph->Parse($filterStr);
					$this->videoFilters->filterGraph = $filterGraph;
					$stam = new stdClass();
					$filterGraph->LoopFilters(null,'iterFuncFadeFilters', $stam);
					if(isset($stam->filters)){
						$this->videoFilters->fadeFilters = $stam->filters;
					}

						/*
						 * Check and prepare for 'subs' filter
						 */
					$stam = new stdClass();
					$filterGraph->LoopFilters(null,'iterFuncSubsFilters', $stam);
					if(isset($stam->filters)){
						$subsFilter = $stam->filters[0];
						$paramArr=explode(':',$subsFilter->_paramStr);
						list($str,$fileName)=explode('=',$paramArr[0]);
						$this->videoFilters->subsFilename =  $fileName;
					}
				}
			}
				/*
				 *
				 */
			$formatParamsArr = array();
			$formatParamsNamesArr = array("-movflags", "-min_frag_duration", "-encryption_scheme", "-encryption_key", "-encryption_kid");
			foreach($formatParamsNamesArr as $formatParamName){
				if(($key=array_search($formatParamName, $cmdLineArr))!==false) {
					$formatParamsArr[] = $cmdLineArr[$key];
					$formatParamsArr[] = $cmdLineArr[$key+1];
				}
			}
			if(count($formatParamsArr)>0){
				$this->formatParams = implode(" ",$formatParamsArr);
			}
			else
				$this->formatParams = null;
			
			if(($key=array_search("-i", $cmdLineArr))!==false) {
				$this->unResolvedSourcePath = $cmdLineArr[$key+1];
				$this->source = $this->resolveSourcePath();
				$cmdLineArr[$key+1] = "\"$this->unResolvedSourcePath\"";
			}
			$this->output = end($cmdLineArr);
		}

		/********************
		 * resolveSourcePath
		 */
		public function resolveSourcePath() 
		{
			$tm=time();
				// resolve only on 1st iteration,
				// or when the current resolved url is older 
				// than pathResolveInterval (def 6hrs)
			if(!isset($this->pathResolveTime) 
			|| $tm-$this->pathResolveTime>$this->pathResolveInterval) {
				KalturaLog::log("timestamp:$tm, old:$this->pathResolveTime, delta:".($tm-$this->pathResolveTime));
				$this->source = kFile::realPath($this->unResolvedSourcePath);	
				$this->pathResolveTime = $tm;
			}
			return $this->source;
		}
		
		/********************
		 *
		 */
		protected static function adjustFilters($filterStr, $mode) 
		{
			$filterStr = trim($filterStr, "'");
			$filterArr = explode(';',$filterStr);
			$filterArrOut = array();
			$skipArr = array();
			foreach($filterArr as $idx=>$filter){
				if(strstr($filter, "aresample")!==false) {
					$skipArr[$idx] = 1;
				}
				else if($mode=='audio' && preg_match("/\b(pan|amix|amerge|loudnorm)\b/", $filter)==1) { 
					$filterArrOut[$idx] = $filter;
				}
				else if($mode=='video' && preg_match("/\b(scale|fade|crop|overlay|rotate|yadif|subtitles)\b/", $filter)==1) { 
					$filterArrOut[$idx] = $filter;
				}
				else
					$skipArr[$idx] = 1;
			}
			if(count($filterArrOut)==0){
				return null;
			}
			foreach($skipArr as $idx=>$val) {
				if($idx>0 && key_exists($idx-1,$filterArrOut)){
					$filter = $filterArrOut[$idx-1];
					if(strpos($filter,'[')==($last=strrpos($filter,'[')))
						continue;
					$filter = substr_replace($filter, null, $last);
					$filterArrOut[$idx-1] = $filter;
				}
			}
			$filterStr = "'".implode(';',$filterArrOut)."'";
			return $filterStr;
		}
	}
	
		/********************
		 * iterFuncFadeFilters
		 *	FilterGraph iteration helper func
		 */
	function iterFuncFadeFilters($entity, $obj)
	{
		KalturaLog::log("Name:".$entity->name);
		if($entity->name=='fade')
			$obj->filters[] = $entity;
		return null;
	}
		
		/********************
		 * iterFuncSubsFilters
		 *	FilterGraph iteration helper func
		 */
	function iterFuncSubsFilters($entity, $obj)
	{
		KalturaLog::log("Name:".$entity->name);
		if($entity->name=='subtitles')
			$obj->filters[] = $entity;
		return null;
	}

 
	/********************
	 * KBaseFFmpegFilterObject
	 */
	class KFFmpegFilter4 extends KBaseFFmpegFilterObject{
		public $delim 	= null;
		
		public $_chain = null;
		
		/********************
		 * 
		 */
		public function __construct($delim = ':') 
		{
			$this->delim = $delim;
		}
		
		/********************
		 * Parse
		 */
		public function Parse($filterStr)
		{
			KalturaLog::log("Filter:$filterStr");
			$this->_string = $filterStr;

			$paramStr = $filterStr;
				/*
				 * Retrieve leading labelIn's
				 */
			while($paramStr[0]=='[' && preg_match('~\[(.*?)]~', $paramStr, $matched)==1) {
				$this->labelIn[] = $matched[1];
				$paramStr = substr($paramStr, strlen($matched[0]));
				KalturaLog::log("cleaned labelIn:$paramStr");
			}
				/*
				 * Retrieve leading labelout
				 */
			if(preg_match('~\[(.*?)]~', $paramStr, $matched)==1){
				$this->labelOut = $matched[1];
				$paramStr = str_replace($matched[0], "", $paramStr);
				KalturaLog::log("cleaned labelOut:$paramStr");
			}		
				/*
				 * Retrieve filter name
				 */
			preg_match('/^([\w]+)/i', $paramStr, $matched); // get name
			$paramStr = substr($paramStr, strlen($matched[0]));
			KalturaLog::log("cleaned name:$paramStr");
			if(!isset($this->name)){
				$this->name = $matched[1];
			}
				/*
				 * If no other data - get out
				 */
			if($paramStr[0]!='=') {
				KalturaLog::log(print_r($this,1));
				return true;
			}
				/*
				 * Handle params's fileds
				 */
			$paramStr = substr($paramStr, 1);
			KalturaLog::log("fields:$paramStr");
			$this->_paramStr = $paramStr;
				/*
				 * Scan through additional param field
				 */
			$paramObj = new stdClass();
			if(isset($this->delim) && strlen($this->delim)>0){
				$fieldArr = explode($this->delim, $paramStr);
				foreach($fieldArr as $fieldStr) {
					$auxArr = explode('=',$fieldStr);
					KalturaLog::log("Field:$fieldStr");
						/*
						 * Single filter param means that it is a 'compact'/non-named syntax,
						 * therefore no need to scan further
						 */
					if(count($auxArr)==1) {
						$field = $this->name;
						$this->$field = $paramStr;
						break;
					}
					else {
						$field = $auxArr[0];
						$this->$field = $auxArr[1];
					}
				}
			}

			return true;
		}
	

	}
	
	/********************
	 * KFFmpegFilterChain4
	 */
	class KFFmpegFilterChain4 extends KBaseFFmpegFilterObject{
		public $entities  = array();
		
		/********************
		 * Parse
		 */
		public function Parse($filterChainStr)
		{
			KalturaLog::log("FilterChain:$filterChainStr");
			$filterArr = explode(',',$filterChainStr);
			$this->_string = $filterChainStr;
			$filters = array();
			$subsId = null;
			$subsFilters = array();
			$auxFilterStr = null;
			foreach($filterArr as $filterStr) {
				if(substr( $filterStr, -1)=='\\'){
					$auxFilterStr.= $filterStr.',';
					continue;
				}
				else if(isset($auxFilterStr)){
					$filterStr = $auxFilterStr.$filterStr;
					$auxFilterStr = null;
					$filter = new KFFmpegFilter4("");
				}
				else {
					$filter = new KFFmpegFilter4();
				}
				$filter->Parse($filterStr);
				$filter->_chain = $this;
				$filter->id = count($filters);
				$filters[] = $filter;
			}
			$this->entities = $filters;
			$this->labelIn = $filters[0]->labelIn;
			$this->labelOut = $filters[count($filters)-1]->labelOut;
			return true;
		}

		/********************
		 * FindEntityByLabelIn
		 */
		public function FindEntityByLabelIn($labelIn)
		{
			KalturaLog::log("labelIn:$labelIn");
			return $this->LoopEntities($this,'iterFuncLabelIn', $labelIn);
		}
		
		/********************
		 * iterFuncLabelIn
		 */
		protected function iterFuncLabelIn($entity, $labelIn)
		{
			KalturaLog::log("labelIn:$labelIn ".$this->_string);
			foreach($entity->labelIn as $lbl) {
				if($lbl==$labelIn) {
					return $entity;
				}
			}
			return null;
		}
		
		/********************
		 * LoopEntities
		 */
		public function LoopEntities($obj, $funcName, $var)
		{
			KalturaLog::log("funcName:$funcName");
			foreach($this->entities as $entity) {
				if(isset($obj))
					$found = $obj->$funcName($entity, $var);
				else
					$found = $funcName($entity, $var);
				if($found===null)
					continue;
				return $found;
			}
			return null;
		}
	}
	
	/********************
	 * 
	 */
	class KFFmpegFilterGraph4 extends KFFmpegFilterChain4{
		
		/********************
		 * Parse
		 */
		public function Parse($filterGraphStr)
		{
			KalturaLog::log("Graph:$filterGraphStr");
			$filterChainArr = explode(';',$filterGraphStr);
			$this->_string = $filterGraphStr;
			foreach($filterChainArr as $filterChainStr) {
				$filterChain = new KFFmpegFilterChain4();
				$filterChain->Parse($filterChainStr);
				$filterChain->id = count($this->entities);
				$this->entities[] = $filterChain;
			}
			return true;
		}
		
		/********************
		 * CompoundString
		 */
		public function CompoundString(&$lastLabelOut)
		{
			$str = null;
			$chainArr = array();
			foreach($this->entities as $chain) {
				$chainStr = null;
				$filterArr = array();
				foreach($chain->entities as $filter){
					$filterStr = null;
					if(isset($filter->labelIn)){
						foreach($filter->labelIn as $labelIn) {
							$filterStr.="[$labelIn]";
						}
					}
					$filterStr.= $filter->name;
					$auxStr = $filter->name;
					if(isset($filter->$auxStr)){
						$filterStr.= ("=".$filter->$auxStr);
					}
					$fieldArr = array();
					foreach($filter as $key=>$val) {
						if(in_array($key,array("name","delim","labelIn","labelOut","_string","_paramStr","_chain","id",$filter->name)))
							continue;
						$fieldArr[] = "$key=$val";
					}
					if($filter->delim=="")
						$filterStr.= "=".$filter->_paramStr;
					else if(count($fieldArr)>0)
						$filterStr.= "=".implode($filter->delim,$fieldArr);
					
					if(isset($filter->labelOut)){
						$lastLabelOut ="[$filter->labelOut]";
						$filterStr.= $lastLabelOut;
					}
					else
						$lastLabelOut = "";
					$filterArr[] = $filterStr;
				}
				$chainStr.= implode(',',$filterArr);
				$chainArr[] = $chainStr;
			}
			$str = implode(';',$chainArr);
			KalturaLog::log("filterGraph string:$str");
			return $str;
		}
		
		/********************
		 * RemoveChain
		 */
		public function RemoveChain($chain)
		{
			$chainsToRemove = array();
			$chainsToRemove[] = $chain;
			
			unset($this->entities[$chain->id]);
			if(count($this->entities)==0)
				return true;
			
				/*
				 * If no 'labelOut' - leave
				 * Note: this usecase should be handled too
				 */
			if(!isset($chain->labelOut)){
				return true;
			}
			$labelOut = $chain->labelOut;
				/*
				 * LabelOut should be in the graph, otherwise - error
				 */
			$chain = $this->FindEntityByLabelIn($labelOut);
			if(!isset($chain)){
				return false;
			}

			$chainsToRemove[] = $chain;
			unset($this->entities[$chain->id]);
			
			$lastLabelIn = $chainsToRemove[count($chainsToRemove)-1]->labelIn;

				/*
				 * Find the external input label, in order to switchh the labels of the removed chains
				 */
			for($idx0=0; $idx0<count($chainsToRemove)-1; $idx0++) {
				foreach($lastLabelIn as $label) {
					if($label!=$chainsToRemove[$idx0]->labelOut){
						$externalLabel = $label;
						break;
					}
				}
				if(isset($externalLabel))
					break;
			}

			$chain = end($chainsToRemove);
			$toChangeLabel = $chain->labelOut;

				/*
				 * Fix chain/filter input label
				 */
			$chain = $this->FindEntityByLabelIn($toChangeLabel);
			if(!isset($chain)){
				return false;
			}
			$filter = $chain->FindEntityByLabelIn($toChangeLabel);
			if(!isset($filter)){
				return false;
			}
			$filter->labelIn = array_replace($filter->labelIn, array($toChangeLabel), array($externalLabel));
			$this->entities[$chain->id]->labelIn = $filter->labelIn;
			$this->entities[$chain->id]->entities[$filter->id]->labelIn = $filter->labelIn;
			
				/*
				 * Fix the finishing chain/filter - they should not have 'labelOut'
				 */
			$chain = end($this->entities);
			$this->entities[$chain->id]->labelOut = null;
			$filter = end($this->entities[$chain->id]->entities);
			$this->entities[$chain->id]->entities[$filter->id]->labelOut = null;
			
			return true;
		}

		/********************
		 * LoopFilters
		 */
		public function LoopFilters($obj, $funcName, $var)
		{
			KalturaLog::log("funcName:$funcName");
			foreach($this->entities as $entity) {
				$found = $entity->LoopEntities($obj, $funcName, $var);
				if($found===null)
					continue;
				return $found;
			}
			return null;
		}
		
		/********************
		 * LoopFilters
		 */
		public static function iterFuncClone($chain, $obj)
		{
			$chn = clone $chain;
			$chn->entities = array();
			foreach($chain->entities as $idx=>$entity){
				$chn->entities[$idx] = clone $entity;
			}
			$obj->entities[] = $chn;
			KalturaLog::log("Name:".$entity->name);
			return null;
		}

	}
	
