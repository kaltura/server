<?php

 /*****************************
 * Includes & Globals
 */
//ini_set("memory_limit","512M");


	/********************
	 * Chunked Encoding module
	 */
	class KChunkedEncode {
		const	MaxInaccuracyValue = 0.100;	// 100 msec, ~3 frames
		
		public $params = null;			// Additional encoding parameters, evaluated from the cmd-line
		public $setup = null;
		
		protected $chunkDataArr = array();	// Chunk design, processing and resultant data

		public $sourceFileDt = null;	// Source file mediaInfo
		public $mergedFileDt = null;	// Generated file mediaInfo

		public $maxInaccuracyValue=null;// Max allowed duration inaccuracy 

		public $cmdLine = null;
		
		public $chunkEncodeToken = "chunkenc";	// Used to identify the chunked encode jobs
		public $videoChunkPostfix = 'vid';		// File postfix's
		public $audioFilePostfix = 'aud';		//
		
		public $chunkFileFormat = "mpegts";
	
		/********************
		 * 
		 */
		public function __construct($setup)
		{
			$this->setup = $setup;
			$this->params  = new KChunkedEncodeParams();
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
			
				/*
				 * Setup work folders 
				 */
			$pInfo = pathinfo($params->output);
			$setup->output = realpath($pInfo['dirname'])."/".$pInfo['basename'];

			if(isset($setup->createFolder) && $setup->createFolder==1) {
				$setup->output.= "_".$this->chunkEncodeToken."/";
				if(!file_exists($setup->output)) {
					KalturaLog::log("Create tmp folder:".$setup->output);
					mkdir($setup->output);
				}
				$setup->output.= $pInfo['filename'];
			}
			
				/*
				 * Evaluate session duration 
				 */
			if($this->setup->duration==-1) {
				$params->duration = round($this->sourceFileDt->containerDuration/1000,4);
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
				if($params->height>360) {		// >SD
					$params->threads = 4;
				}
				else {
					$params->threads = 4;		// SD
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


			if(!in_array($vcodec, array("libx264","libx265","h264","h264b","h264m","h264h","h265"))){
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
				 */
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
				$cmdLineArr[$idx+1] = realpath($cmdLineArr[$idx+1]);
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

			$toRemove = array("-ac","-ar", "-b:a","-ab","-async","-filter_complex","-flags","-f");
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
			
			$cmdLineArr[] = "-map 0:v:0 -flags -global_header";
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
			$chunkData = $this->chunkDataArr[$chunkIdx];
			{
				$cmdLine = " -i ".$this->cmdLine." -t $chunkWithOverlap";
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
		 *
		 */
		public function BuildMergeCommandLine()
		{
			$mergedFilename= $this->getSessionName();
			
			$vidConcatStr = "concat:'";
			foreach($this->chunkDataArr as $idx=>$chunkData){
				if(isset($chunkData->toFix))
					$vidConcatStr.= $this->getChunkName($idx,"fix").'|';
				else 
					$vidConcatStr.= $this->getChunkName($idx).'|';
			}
			$vidConcatStr = rtrim($vidConcatStr, '|');
			$vidConcatStr.= "'";

			$setup = $this->setup;
			$params = $this->params;
			$audioInputParams = null;
			if(isset($params->acodec)) {
				$audioFilename = $this->getSessionName("audio");
				if($setup->duration!=-1){
					$fileDt = self::getMediaData($audioFilename);
					if(isset($fileDt) && round($fileDt->containerDuration,4)>$params->duration) {
						$audioInputParams = " -t ".$params->duration;
						KalturaLog::log("cut input audio to ".$params->duration);
					}
				}
				if($this->chunkFileFormat=="mpegts")
					$audioInputParams.= " -itsoffset -1.4";
				$audioInputParams.= " -i $audioFilename";
				$audioCopyParams = "-map 1:a -c:a copy";
				if($params->acodec=="libfdk_aac" || $params->acodec=="libfaac")
					$audioCopyParams.= " -bsf:a aac_adtstoasc";
			}
			else{
				$audioCopyParams = null;
			}

			$mergeCmd = $setup->ffmpegBin;
			if(isset($params->fps)) $mergeCmd.= " -r ".$params->fps;
			if($this->chunkFileFormat=="mpegts")
				$mergeCmd.= " -itsoffset -1.4";
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
				 */
				if($params->vcodec=="libx264")
					$mergeCmd.= ((int)$ffmpegVerStr<4)?" -nal_types_mask 0x3e":" -bsf:v filter_units=pass_types=1-5";
				else if($params->vcodec=="libx265")
					$mergeCmd.= ((int)$ffmpegVerStr<4)?" -nal_types_mask 0xffffffff":" -bsf:v filter_units=pass_types=0-31";
			}

			if(($key=array_search("-tag:v", $params->cmdLineArr))!==false) {
				$mergeCmd.= " -tag:v ".$params->cmdLineArr[$key+1];
			}

			$mergeCmd.= " -y $mergedFilename";
			KalturaLog::log("mergeCmd:\n$mergeCmd ".date("Y-m-d H:i:s"));
			return $mergeCmd;
		}

		/********************
		 *
		 */
		public function BuildFixVideoCommandLine($idx)
		{
			$chunkData = $this->chunkDataArr[$idx];
			$chunkFixName = $this->getChunkName($idx, "fix");

			$cmdLine = $this->setup->ffmpegBin;
			if($this->chunkFileFormat=="mpegts")
				$cmdLine.= " -itsoffset -1.4";
			if(isset($this->params->fps)) 
				$cmdLine.= " -r ".$this->params->fps;
			
			if(file_exists($this->getChunkName($idx, $idx+1))) {
				$cmdLine.= " -i concat:'".$this->getChunkName($idx, $idx);
				$cmdLine.= "|".$this->getChunkName($idx, $idx+1)."'";
			}
			else
				$cmdLine.= " -i ".$this->getChunkName($idx, $idx);
				
			$start = $chunkData->start; 
			$segmentTime = $chunkData->duration+(1/$this->params->fps*1.5);
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
			
			$cmdLine.= " -i $params->source";
			$cmdLine.= " -vn";
			if(isset($params->acodec)) $cmdLine.= " -c:a ".$params->acodec;
			if(isset($filterStr))
				$cmdLine.= " $filterStr";
			$cmdLine.= " -map 0:a:0 -metadata:s:a:0 language=";
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
		public function updateChunkFileStatData($idx, $stat)
		{
			$this->chunkDataArr[$idx]->stat = $stat;
		}
		
		/********************
		 * mode: base, fix, [0...n]
		 */
		public function getSessionName($mode="merged")
		{
			switch($mode){
			case "merged":
				$name = $this->setup->output."_merged";
				break;
			case "audio":
				$name = $this->setup->output."_audio";
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
			default:
				$name.= $mode;
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
			case "fix":
				$name.= "$this->videoChunkPostfix".$chunkIdx.".fix";
				break;
			case "base":
				$name.= "$this->videoChunkPostfix";
				break;
			case "srt":
				$name.= "srt";
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
			$start = $setup->startFrom;
//			$this->chunkDataIdx=round($start/$this->setup->chunkDuration);
			$finish = $setup->startFrom+$params->duration;
			$duration = $setup->chunkDuration+$this->calcChunkDrift();
			$idx = 0;
			while($finish-$start>$params->frameDuration) {
				$chunkData = new KChunkData($idx, $start, $duration);
				if($idx>0) {
					$this->chunkDataArr[$idx-1]->calcGapToNext($chunkData, $params->frameDuration);
				}
				$this->chunkDataArr[$idx++] = $chunkData;
		
				$start += $setup->chunkDuration+$this->calcChunkDrift();
				$delta = $start-$idx*$setup->chunkDuration;
				$duration = $setup->chunkDuration+$this->calcChunkDrift();
				if($params->frameDuration<$delta) {
					KalturaLog::log("idx($idx)- remove frame - frameDuration($params->frameDuration), delta($delta)");
					$start-=($params->frameDuration);
				}
				else if($delta<0 && $params->frameDuration>-$delta) {
					KalturaLog::log("idx($idx)- add frame - frameDuration($params->frameDuration), delta($delta)");
					$start+=($params->frameDuration);
				}
			}
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
	}
	
	/********************
	 *
	 */
	class KChunkedEncodeParams {
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
					$filterGraph = new KFFmpegFilterGraph();
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
				$this->source = $cmdLineArr[$key+1];
			}
			$this->output = end($cmdLineArr);
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
				else if($mode=='audio' && preg_match("/\b(pan|amix|amerge)\b/", $filter)==1) { 
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
	 * Preset and processing chunk data
	 */
	class KChunkData {
		public $index = 0;
		public $start = 0;		// Chunks start timing
		public $duration = 0;	// Chunk duration
		public $frames = 0;		// Frame count
		public $gap = 0;		// Gap to next chunk
		
		public $toFix = null;	// If set - the chunk must be fixed by increasing/decreasing the chunks frame count to 'toFix' number
		
		public $stat = null;	// Frames stat data
		
		/********************
		 * 
		 */
		public function __construct($index=null, $start=null, $duration=null, $frames=null, $gap=null) {
			$this->index = $index;
			$this->start = $start;
			$this->duration = $duration;
			$this->frames = $frames;
			$this->gap = $gap;
		}
		
		/********************
		 * calcGapToNext
		 */
		public function calcGapToNext($nextChunk, $frameDuration)
		{
			$this->gap = ($nextChunk->start-$this->start);
			$this->frames = round($this->gap/$frameDuration);
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
		
