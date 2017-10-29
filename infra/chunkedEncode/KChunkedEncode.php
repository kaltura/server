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
		public function Initialize()
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
			
			if(isset($setup->fps)){
				$params->fps = $setup->fps;
			}
			else if(!isset($params->fps)){
				KalturaLog::log($msgStr="ERROR: missing essential - fps");
				return false;
			}

			if(isset($setup->gop)){
				$params->gop = round($setup->gop*$params->fps);
			}
			else if(!isset($params->gop)){
				KalturaLog::log($msgStr="ERROR: missing essential - gop");
				return false;
			}
			
				// Get source mediaData. Required for 'supported' validation
			$this->sourceFileDt = $this->getMediaData($this->params->source);
				/*
				 * Check for supported codecs/formats
				 */
			if($this->verifySupport($msgStr)!=true){
				KalturaLog::log($msgStr);
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

			$this->buildTemplateVideoCommandLine();

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
			
			KalturaLog::log("data:".print_r($this,1));

				/*
				 * Evaluate session duration 
				 */
			$duration = $this->setup->duration;
			if($duration==-1) {
				$duration = round($this->sourceFileDt->containerDuration/1000,4);
			}
/**/		if($duration<180){
				KalturaLog::log($msgStr="ERROR: too short duration ($duration), must be at least 180sec");
				return false;
			}
			$params->duration = $duration;

				/*
				 * Evaluate various defaults
				 * - frame duration
				 * - chunk overlap
				 * - chunk duration 
				 */
			$params->frameDuration = 1/$params->fps;
			$this->setup->Update($params);
			
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
			$start = $this->setup->startFrom;
//			$this->chunkDataIdx=round($start/$this->setup->chunkDuration);
			$finish = $this->setup->startFrom+$params->duration;
			$duration = $this->setup->chunkDuration+$this->calcChunkDrift();
			$idx = 0;
				/*
				 * Handle Subs(SRT) file splitting, if it is required
				 */
			if(isset($this->params->videoFilters->subsFilename)){
				$subsFileHd = fopen($this->params->videoFilters->subsFilename,'r');
				$subsArr = array();
			}
			while($finish>$start) {
				$chunkData = new KChunkData($idx, $start, $duration);
				if($idx>0) {
					$this->chunkDataArr[$idx-1]->calcGapToNext($chunkData, $params->frameDuration);
				}
				$this->chunkDataArr[$idx++] = $chunkData;
		
					/*
					 * SRT splitting
					 */
				if(isset($subsFileHd)){
					$chunkSrtFile = $this->getChunkName($chunkData->index,"srt");
					if(file_exists($chunkSrtFile))
						unlink($chunkSrtFile);
					KSrtText::SplitSrtFile($subsFileHd, $chunkSrtFile, $start, $duration, $subsArr);
					KalturaLog::log("$chunkSrtFile, $start, $duration");

				}

				$start += $this->setup->chunkDuration+$this->calcChunkDrift();
				$delta = $start-$idx*$this->setup->chunkDuration;
				$duration = $this->setup->chunkDuration+$this->calcChunkDrift();
				if($params->frameDuration<$delta) {
					KalturaLog::log("idx($idx)- remove frame - frameDuration($params->frameDuration), delta($delta)");
					$start-=($params->frameDuration);
				}
				else if($delta<0 && $params->frameDuration>-$delta) {
					KalturaLog::log("idx($idx)- add frame - frameDuration($params->frameDuration), delta($delta)");
					$start+=($params->frameDuration);
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
		protected function verifySupport(&$msgStr)
		{
			$params = $this->params;
			if(strstr($params->vcodec, "x264")===false) {
				KalturaLog::log($msgStr="UNSUPPORTED: video codec ($params->vcodec)");
				return false;
			}

			if(strstr($params->acodec, "aac")===false) {
				KalturaLog::log($msgStr="UNSUPPORTED: audio codec ($params->acodec)");
				return false;
			}
			
			if(!in_array($params->format, array("mp4","mov","ismv"))){
				KalturaLog::log($msgStr="UNSUPPORTED: media file format ($params->format)");
				return false;
			}
/*			
			if(abs(1-($this->sourceFileDt->videoFrameRate/$params->fps))>0.1){
				KalturaLog::log($msgStr="UNSUPPORTED: incompatible frame-rates (source:".$this->sourceFileDt->videoFrameRate.",target:$params->fps). Allowed variance up to 10%.");
				return false;
			}
*/			
				/*
				 * WV session fail with chunked encoding. Detect chunks by very large GOP value
					***** Disable this check, WV related flavors can disable chunks via conv-prof 
			if($params->gop>100){
				KalturaLog::log($msgStr="UNSUPPORTED: WV conversion detected (large GOP=$params->gop). Currently chunked encoding does not work with WV.");
				return false;
			}*/

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
				if(($auxStr=strstr($cmdLineArr[$key+1],"n_forced"))!==false){
					sscanf($auxStr,"n_forced*%d",$gopInSecs);
				}
				else 
					$gopInSecs=round($params->gop/$params->fps);
				$cmdLineArr[$key+1]="'expr:gte(t,n_forced*$gopInSecs)'";
			}
			if(($key=array_search("-c:a", $cmdLineArr))!==false
			|| ($key=array_search("-acodec", $cmdLineArr))!==false) {
				unset($cmdLineArr[$key+1]);
				$cmdLineArr[$key]='-an';
			}

			$toRemove = array("-ac","-ar", "-b:a","-ab","-vsync", "-async","-filter_complex","-flags","-f");
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
				if(($key=array_search("-x264opts", $cmdLineArr))!==false) {
					if(strstr($cmdLineArr[$key+1],"zones")===false) {
						$cmdLineArr[$key+1] = $cmdLineArr[$key+1].":zones=0,10,q=12";
					}
				}
				else {
					$cmdLineArr[] = "-x264opts zones=0,10,q=12";
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
			
			$toImplode = false;

				/*
				 * Handle WM fade in's/out's
				 */
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
				$adjustedFilterStr = $filterGraph->CompoundString();
			}
			
				/*
				 * Handle Subs/SRT - change original file name to splitted 
				 */
			if(isset($this->params->videoFilters->subsFilename)){
				$adjustedSubsFilename = $this->getChunkName($chunkIdx,"srt");
				if(isset($adjustedFilterStr)){
					$adjustedFilterStr = str_replace($this->params->videoFilters->subsFilename, 
											$adjustedSubsFilename, $adjustedFilterStr);
				}
				else {
					$adjustedFilterStr = str_replace($this->params->videoFilters->subsFilename,
											$adjustedSubsFilename, $cmdLineArr[$filterIdx+1]);
				}
			}
			
			if(isset($adjustedFilterStr)) {
				KalturaLog::log($adjustedFilterStr);
				$cmdLineArr[$filterIdx+1] = '\''.$adjustedFilterStr.'\'';
				$cmdLine = implode(' ',$cmdLineArr);
			}
			
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
			$chunkWithOverlap = $setup->chunkDuration + $setup->chunkOverlap;
			
			{
				$cmdLine = $this->cmdLine." -t $chunkWithOverlap";
					/*
					 * Timing repositioning should be split into two steps 
					 * - input step to 'start-1sec'
					 * - output step to 1sec forward
					 * This is required to overcome some sources that does not reposition correctly. Better solution would be to reposition to the nearest KF, 
					 * but this will require long source query.
					 */
				if($start<1) {
					$cmdLine = " -ss $start -i ".$cmdLine;
				}
				else {
					$cmdLine = " -ss ".($start-1)." -i ".$cmdLine." -ss 1";
				}
			
				$cmdLine = " -threads ".$this->params->threadsDec.$cmdLine;
				
				$cmdLine = $this->adjustTimeRelatedFilters($cmdLine, $chunkIdx, $start, $chunkWithOverlap);
				KalturaLog::log($cmdLine);
			}
			$cmdLine = "$setup->ffmpegBin $cmdLine";

			
			if(isset($this->params->passes) && $this->params->passes==2){
				$cmdPass1 = "$cmdLine -passlogfile $chunkFilename.pass2log -pass 1 -fastfirstpass 1 -f $this->chunkFileFormat /dev/null";
				$cmdLine = "$cmdPass1 && $cmdLine -passlogfile $chunkFilename.pass2log -pass 2";
			}
			$cmdLine.= " -f segment -segment_format $this->chunkFileFormat -initial_offset $start";
			$chunkData = $this->chunkDataArr[$chunkIdx];
			if(isset($chunkData->frames)) {
				$cmdLine.= " -segment_frames ".($chunkData->frames);
			}
			else {
				$cmdLine.= " -segment_time ".$chunkData->duration;
			}
			$cmdLine.= " -segment_start_number $chunkIdx -segment_list /dev/null $chunkFilename%d ";
			return $cmdLine;
		}

		/********************
		 *
		 */
		public function BuildMergeCommandLine()
		{
			$audioFilename = $this->getSessionName("audio");
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
			if(isset($audioFilename)) {
				if($setup->duration!=-1){
					$fileDt = self::getMediaData($audioFilename);
					if(isset($fileDt) && round($fileDt->containerDuration,4)>$this->params->duration) {
						$audioInputParams = " -t ".$this->params->duration;
						KalturaLog::log("cut input audio to ".$this->params->duration);
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
			$mergeCmd.= "$audioInputParams -map 0:v:0 -c:v copy $audioCopyParams $params->formatParams -f $params->format -copyts -y $mergedFilename";
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
			$cmdLine.= " -i $params->source";
			$cmdLine.= " -vn";
			if(isset($params->acodec)) $cmdLine.= " -c:a ".$params->acodec;
			if(isset($filterStr))
				$cmdLine.= " $filterStr";
			$cmdLine.= " -map 0:a:0";
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
/* Discontinuity check by analysing chunk generation log file */
				$stat = $chunkData->stat;
				if(isset($prevObjIdx)) {
					$prevObj = $this->chunkDataArr[$prevObjIdx]->stat;
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
			if((isset($vDelta) && $vDelta>$maxMergeDelta) 
			|| (isset($aDelta) && $aDelta>$maxMergeDelta) 
//			|| (isset($cDelta) && $cDelta>$maxMergeDelta)
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
			case "merged";
				$name = $this->setup->output."_merged";
				break;
			case "audio";
				$name = $this->setup->output."_audio";
				break;
			case "qpfile";
				$name = $this->setup->output."_qpfile";
				break;
			case "log":
				$name = $this->setup->output."_session.log";
				break;
			case "session":
				$name = $this->setup->output.".ses";
				break;
			case "concat";
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
			case null;
				$name.= "$this->videoChunkPostfix".$chunkIdx;
				break;
			case "fix";
				$name.= "$this->videoChunkPostfix".$chunkIdx.".fix";
				break;
			case "base":
				$name.= "$this->videoChunkPostfix";
				break;
			case "srt";
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
		protected static function getMediaData($fileName)
		{
			if(!file_exists($fileName))
				return null;
			$medPrsr = new KMediaInfoMediaParser($fileName);
			$m=$medPrsr->getMediaInfo();
			return $m;
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
		
		public $passes = null;

		public $videoFilters =  null;
		public $audioFilters =  null;
		public $formatParams =  null;
		
		public $duration = null;
		public $frameDuration = null;

		public $cmdLineArr = array();
		
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
			return 0;
		}
		
		/********************
		 *
		 */
		protected function parseEncodingSettings($cmdLineArr) 
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
			$formatParamsNamesArr = array("-movflags", "-min_frag_duration");
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
		
		public $toFix = null;	// If set - the chunk must be fixed by increasing/decreasing the chunks frame cunt to 'toFix' number
		
		public $stat = null;	// Frames stat data
		
		/********************
		 * 
		 */
		public function __construct($index=null, $start=null, $finish=null, $frames=null, $gap=null) {
			$this->index = $index;
			$this->start = $start;
			$this->duration = $finish;
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
		
	

