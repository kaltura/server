<?php

 /*****************************
 * Includes & Globals
 */
//ini_set("memory_limit","2048M");

	/********************
	 * Base Chunked Encoding Session Manager module
	 */
	abstract class KBaseChunkedEncodeSessionManager
	{
		const 	SessionStatsJSONLogPrefix = "SessionStatsJSON";
		
		protected $name = null;	
		protected $chunker = null;

		protected $maxFailures = 5;		// Max allowed job failures (if more, get out w/out retry)
		protected $maxRetries = 10;		// Max retries per failed job
		protected $maxExecutionTime = 3000;	// In seconds. Suits FHD, represents transcoding ratio of x50, 
											// other resolutions will be adjusted accordingly (aka 360p, 4K/H265, ..)
		
		protected $videoCmdLines = array();
		protected $audioCmdLines = array();
		
		protected $createTime = null;	// Secs
		protected $finishTime = null;

		protected $chunkExecutionDataArr = array();

		protected $returnStatus = null;	// KChunkedEncodeReturnStatus
		protected $returnMessages = array();

		protected $concurrencyHistogram = array();
		protected $concurrencyAccum = 0;

		/********************
		 *
		 */
		public function __construct(KChunkedEncodeSetup $setup, $name=null)
		{
			$this->chunker = new KChunkedEncode($setup);
			KalturaLog::log(date("Y-m-d H:i:s"));
			KalturaLog::log("sessionData:".print_r($this,1));
			
			if(strlen($name)==0)
				$this->name = null;
			else
				$this->name = $name;
			
			$this->createTime = time();
		}

		/********************
		 *
		 */
		public function Generate()
		{
			if($this->GenerateContent()!=true){
				return false;
			}
			
$retries=3;			
			if($this->Analyze()>0) {
				for($idx=0;$idx<$retries;$idx++) {
					if($this->FixChunks()===true){
						break;
					}
					KalturaLog::log("FixChunks failed: retry $idx/$retries");
					sleep(rand(1,3));
				}
				if($idx==$retries){
					return false;
				}
			}
			
			if($this->Merge()!=true){
				return false;
			}
			
			$this->CleanUp();
			
			$this->returnStatus = KChunkedEncodeReturnStatus::OK;
//			if(file_exists($this->chunker->getSessionName())) {
//				copy($this->chunker->getSessionName(), $this->chunker->params->output);
//			}
			return true;
		}
		
		/* ---------------------------
		 * Initialize
		 */
		public function Initialize()
		{
			$chunker = $this->chunker;
			$rv = $chunker->Initialize($msgStr);
			if($rv!==true){
				$this->returnStatus = KChunkedEncodeReturnStatus::InitializeError;
				$this->returnMessages[] = $msgStr;
				return $rv;
			}
			if(!isset($this->name))
				$this->name = basename($chunker->params->output);

			$videoCmdLines = array();
			$sharedMode = isset($chunker->setup->sharedChunkPath)?$chunker->setup->sharedChunkPath:null;

			for($chunkIdx=0;$chunkIdx<$chunker->GetMaxChunks();$chunkIdx++) {
				$chunkData = $chunker->GetChunk($chunkIdx);
				$start = $chunkData->start;

				$outFileNames = array();
				$outFileNames[] = $chunker->getChunkName($chunkIdx);
				$outFileNames[] = $chunker->getChunkName($chunkIdx,"base").($chunkIdx+1);
				$logFilename = $chunker->getChunkName($chunkIdx,"base").".log";
				$outFileNames[] = $logFilename;
				
				$cmdLine = $chunker->BuildVideoCommandLine($start, $chunkIdx);
				$cmdLine = "time $cmdLine > $logFilename 2>&1";
				$currVideoCmdLine = array($cmdLine, $outFileNames);
				
				if($sharedMode)
				{
					$sharedOutFilenames = array();
					$sharedChunkName = $chunker->getChunkName($chunkIdx, "shared");
					if($sharedChunkName)
					{
						$sharedOutFilenames[] = $sharedChunkName;
						$sharedOutFilenames[] =$chunker->getChunkName($chunkIdx,"shared_base").($chunkIdx+1);
						$sharedOutFilenames[] =$chunker->getChunkName($chunkIdx,"shared_base").".log";
						$currVideoCmdLine[] = $sharedOutFilenames;
					}
				}
				

				$videoCmdLines[$chunkIdx] = $currVideoCmdLine;
				KalturaLog::log($cmdLine);
			}
			$this->videoCmdLines = $videoCmdLines;
			
			$cmdLine = $chunker->BuildAudioCommandLine();
			if(isset($cmdLine)){
				$outFilename = $chunker->getSessionName("audio");
				$logFilename = $outFilename.".log";
				$currAudioCmdLine = array("time $cmdLine > $logFilename 2>&1", array($outFilename,$logFilename));
				
				if($sharedMode) {
					$sharedAudioChunkName = $chunker->getSessionName("shared_audio");;
					$logFilename = $sharedAudioChunkName.".log";
					$currAudioCmdLine[] = array($sharedAudioChunkName,$logFilename);
				}
				
				$this->audioCmdLines[] = $currAudioCmdLine;
			}
			$this->SerializeSession();
			return true;
		}

		/********************
		 * Analyze 
		 */
		public function Analyze()
		{
			return $this->chunker->CheckChunksContinuity();
		}
		
		/* ---------------------------
		 * 
		 */
		public function FixChunks()
		{
			$chunker = $this->chunker;
			$processArr = array();
			$maxChunks = $chunker->GetMaxChunks();
			
			$chunkOutputFileList = array();
			if($chunker->setup->sharedChunkPath) {
				$rawChunkOutputFileList = kFile::listDir($chunker->setup->sharedChunkPath, dirname($chunker->setup->sharedChunkPath).DIRECTORY_SEPARATOR);
				foreach ($rawChunkOutputFileList as $fileItem) {
					$chunkOutputFileList[] = kFile::fixPath( "/" . $fileItem[0]);
				}
				KalturaLog::debug("Chunk dir content list: " . print_r($chunkOutputFileList, true));
			}
			
			for($idx=0; $idx<$maxChunks; $idx++) {
				$chunkData = $chunker->GetChunk($idx);
				if(!isset($chunkData->toFix) || $chunkData->toFix==0)
					continue;
				/*
				 * Check for too short generated chunks. If found - leave with error,
				 * 10 frame threshold allowed (chunker::chunkDurThreshInFrames).
				 */
				if($idx<$maxChunks-1) {
					$chunkDurThreshInSec=$chunker->chunkDurThreshInFrames*$chunker->params->frameDuration;
					$generatedChunkDur = $chunkData->stat->finish-$chunkData->stat->start;
					if($chunkData->gap-$chunkDurThreshInSec > $generatedChunkDur){
						$msgStr="Chunk id ($chunkData->index): too short chunk dur - $generatedChunkDur, should be ".round($chunkData->gap,4).", thresh:".round($chunkDurThreshInSec,4).", delta:".round($chunkData->gap-$generatedChunkDur,4);
						KalturaLog::log($msgStr);
						$this->returnMessages[] = $msgStr;
						$this->returnStatus = KChunkedEncodeReturnStatus::AnalyzeError;
						return false;
					}
					else
						KalturaLog::log("Chunk id ($chunkData->index): correct chunk dur - $generatedChunkDur, should be ".round($chunkData->gap,4).", thresh:".round($chunkDurThreshInSec,4).", delta:".round($chunkData->gap-$generatedChunkDur,4));
						
				}
				
				$toFixChunkIdx = $chunkData->index;
				
				$chunkFixName = $chunker->getChunkName($toFixChunkIdx, "fix");
				$cmdLine = $chunker->BuildFixVideoCommandLine($toFixChunkIdx, $chunkOutputFileList)." > $chunkFixName.log 2>&1";
				$process = $this->executeCmdline($cmdLine, "$chunkFixName.log");
				if($process==false){
					KalturaLog::log($msgStr="Chunk ($chunkFixName) fix FAILED !");
					$this->returnMessages[] = $msgStr;
					$this->returnStatus = KChunkedEncodeReturnStatus::AnalyzeError;
					return false;
				}
				$processArr[$toFixChunkIdx] = $process;
			}
			KalturaLog::log("waiting ...");
			foreach($processArr as $idx=>$process) {
				KProcessExecutionData::waitForCompletion($process);
				$chunkFixName = $chunker->getChunkName($idx, "fix");
				$execData = new KProcessExecutionData($process, $chunkFixName.".log");
				if($execData->exitCode!=0) {
					KalturaLog::log($msgStr="Chunk ($idx) fix FAILED, exitCode($execData->exitCode)!");
					$this->returnMessages[] = $msgStr;
					$this->returnStatus = KChunkedEncodeReturnStatus::AnalyzeError;
					return false;
				}
				if(!file_exists($chunkFixName)){
					KalturaLog::log($msgStr="Chunk ($idx) fix FAILED, missing fixed file ($chunkFixName)!");
					$this->returnMessages[] = $msgStr;
					$this->returnStatus = KChunkedEncodeReturnStatus::AnalyzeError;
					return false;
				}
			}
			
			return true;
		}
		
		/********************
		 *
		 */
		public function Merge()
		{
			$rv=$this->chunker->ConcatChunks();
			if($rv===false) {
				KalturaLog::log($msgStr="FAILED to merge - missing concat'ed chunk video file, leaving!");
				$this->returnMessages[] = $msgStr;
				$this->returnStatus = KChunkedEncodeReturnStatus::MergeError;
				
				// remove if you are not working with the fopen flow
				$this->chunker->deleteTmpMergedVideoFile();
				
				return false;
			}
			$concatFilenameLog = $this->chunker->getSessionName("concat");

			$mergeCmd = $this->chunker->BuildMergeCommandLine();
			KalturaLog::log("mergeCmd:$mergeCmd");
			$maxAttempts = 3;
			for($attempt=0; $attempt<$maxAttempts; $attempt++) {

				$process = $this->executeCmdline($mergeCmd, $concatFilenameLog);
				if($process===false) {
					KalturaLog::log("FAILED to merge (attempt:$attempt)!");
					$logTail = self::GetLogTail($concatFilenameLog);
					if(isset($logTail))
						KalturaLog::log("Log dump:\n".$logTail);
					sleep(5);
					continue;
				}
				KalturaLog::log("waiting ...");
				KProcessExecutionData::waitForCompletion($process);
				$execData = new KProcessExecutionData($process, $concatFilenameLog);
				if($execData->exitCode!=0) {
					KalturaLog::log("FAILED to merge (attempt:$attempt, exitCode:$execData->exitCode)!");
					$logTail = self::GetLogTail($concatFilenameLog);
					if(isset($logTail))
						KalturaLog::log("Log dump:\n".$logTail);
					sleep(5);
					continue;
				}
				if($this->chunker->ValidateMergedFile($msgStr)!=true){
					KalturaLog::log("FAILED to merge (attempt:$attempt, $msgStr)!");
					$logTail = self::getLogTail($concatFilenameLog);
					if(isset($logTail))
						KalturaLog::log("Log dump:\n".$logTail);
					sleep(5);
					continue;
				}
				
				break;
			}
			
			// remove if you are not working with the fopen flow
			$this->chunker->deleteTmpMergedVideoFile();
			
			if($attempt==$maxAttempts){
				KalturaLog::log($msgStr="FAILED to merge, leaving!");
				$this->returnMessages[] = $msgStr;
				$this->returnStatus = KChunkedEncodeReturnStatus::MergeAttemptsError;
				return false;
			}

			return true;
		}
		
		/********************
		 *
		 */
		public function Report()
		{
			$this->finishTime = time();
			$sessionData = $this;
			$chunker = $this->chunker;
			KalturaLog::log("sessionData:".print_r($sessionData,1));

			$msgStr = "Merged:";
			$durStr = null;
			$fileDtMrg = $chunker->mergedFileDt;
			if(isset($fileDtMrg)){
				KalturaLog::log("merged:".print_r($fileDtMrg,1));
				$msgStr.= "file dur(v:".round($fileDtMrg->videoDuration/1000,4).",a:".round($fileDtMrg->audioDuration/1000,4).")";
			}
			if(isset($sessionData->refFileDt)) {
				$fileDtRef = $sessionData->refFileDt;
				KalturaLog::log("reference:".print_r($fileDtRef,1));
			}
			$fileDtSrc = $chunker->sourceFileDt;
			if(isset($fileDtSrc)){
				KalturaLog::log("source:".print_r($fileDtSrc,1));
			}
			
			$sessionStats = new KChunkedEncodeSessionReportStats();
			$sessionStats->num = $chunker->GetMaxChunks();
			$sessionStats->lasted = $this->finishTime - $this->createTime;
			{
				KalturaLog::log("CSV,idx,startedAt,user,system,elapsed,cpu");
				foreach($this->chunkExecutionDataArr as $idx=>$execData){
					$sessionStats->userCpu +=    $execData->user;
					$sessionStats->systemCpu +=  $execData->system;
					$sessionStats->elapsedCpu += $execData->elapsed;
					
					KalturaLog::log("CSV,$idx,$execData->startedAt,$execData->user,$execData->system,$execData->elapsed,$execData->cpu");
				}
				$cnt = $chunker->GetMaxChunks();

			}
			
//			KalturaLog::log("LogFile: ".$chunker->getSessionName("log"));

			if(isset($this->concurrencyHistogram) && count($this->concurrencyHistogram)>0){
				ksort($this->concurrencyHistogram);
				$ttlStr = "Concurrency";
				$tmStr = "Concurrency";
				$concurSum = 0;
				$tmSum = 0;
				foreach($this->concurrencyHistogram as $concur=>$tm){
					$ttlStr.=",$concur";
					$tmStr.= ",$tm";
					$concurSum+= ($concur*$tm);
					$tmSum+= $tm;
				}
				KalturaLog::log($ttlStr);
				KalturaLog::log($tmStr);
				$concurrencyLevel = (round(($concurSum/$tmSum),2));
			}

			KalturaLog::log("***********************************************************");
			KalturaLog::log("* Session Summary (".date("Y-m-d H:i:s").")");
			KalturaLog::log("* ");
			if(isset($concurrencyLevel)) {
				$val = round(end($this->concurrencyHistogram)/1000,2);
				$idle = round($this->concurrencyHistogram[0]/1000,2);
				$sessionStats->concurrency = $concurrencyLevel;
				$sessionStats->concurrencyMax = key($this->concurrencyHistogram);
				$sessionStats->concurrencyMaxTime = $val;
				$sessionStats->concurrencyIdleTime = $idle;
			}

			KalturaLog::log("SessionStats:".$sessionStats->ToString());
			$sessionStatsJson = json_encode($sessionStats);
			KalturaLog::log("SessionStatsJSON $sessionStatsJson");
			
			if($sessionData->returnStatus==KChunkedEncodeReturnStatus::AnalyzeError){
				$msgStr.= ",analyze:BAD";
			}
			if($sessionData->returnStatus==KChunkedEncodeReturnStatus::OK){
				$msgStr.= ",analyze:OK";
				if(isset($fileDtMrg)) {
					$frameRateMode = stristr($fileDtMrg->rawData,"Frame rate mode                          : ");
					$frameRateMode = strtolower(substr($frameRateMode, strlen("Frame rate mode                          : ")));
					$frameRateMode = strncmp($frameRateMode,"constant",8);
					if($frameRateMode==0) {
						$msgStr.= ",frameRateMode(constant)";
					}
					else
						$msgStr.= ",frameRateMode(variable)";
				}
			}
			if(isset($chunker->sourceFileDt)
			&& (!isset($chunker->setup->duration) || $chunker->setup->duration<=0 || abs($chunker->setup->duration-round($chunker->sourceFileDt->containerDuration/1000,4))<0.1)) {
				if(isset($fileDtMrg)){
					$deltaStr = null;
					if(isset($fileDtRef)){
						$vidDelta = round(($fileDtMrg->videoDuration - $fileDtRef->videoDuration)/1000,4);
						$audDelta = round(($fileDtMrg->audioDuration - $fileDtRef->audioDuration)/1000,4);
						$deltaStr = "MergedToRef:(v:$vidDelta,a:$audDelta)";
						$videoOk = (abs($vidDelta)<$chunker->maxInaccuracyValue);
						$deltaStr.=$videoOk?",video:OK":",video:BAD";
						$audioOk = (abs($audDelta)<$chunker->maxInaccuracyValue);
						$deltaStr.=$audioOk?",audio:OK":",audio:BAD";
						$deltaStr.=($audioOk && $videoOk)?",delta:OK":",delta:BAD";
						$deltaStr.= ",dur(v:".round($fileDtRef->videoDuration/1000,4).",a:".round($fileDtRef->audioDuration/1000,4).")";
						KalturaLog::log("$deltaStr");
					}

					$deltaStr = null;
					if(isset($fileDtSrc)){
						$dur=$fileDtSrc->videoDuration = ($fileDtSrc->videoDuration>0)? $fileDtSrc->videoDuration: $dur=$fileDtSrc->containerDuration;
						$vidDelta = ($fileDtMrg->videoDuration - $dur)/1000;//round(($fileDtMrg->videoDuration - $dur)/1000,6);
						$dur=$fileDtSrc->audioDuration = ($fileDtSrc->audioDuration>0)? $fileDtSrc->audioDuration: $dur=$fileDtSrc->containerDuration;
						$audDelta = ($fileDtMrg->audioDuration - $dur)/1000;//round(($fileDtMrg->audioDuration - $dur)/1000,6);
						$deltaStr = "MergedToSrc:(v:$vidDelta,a:$audDelta)";
						$videoOk = (abs($vidDelta)<$chunker->maxInaccuracyValue);
						$deltaStr.=$videoOk?",video:OK":",video:BAD";
						$audioOk = (abs($audDelta)<$chunker->maxInaccuracyValue);
						$deltaStr.=$audioOk?",audio:OK":",audio:BAD";
						$deltaStr.=($audioOk && $videoOk)?",delta:OK":",delta:BAD";
						$deltaStr.= ",dur(v:".round($fileDtSrc->videoDuration/1000,6).",a:".round($fileDtSrc->audioDuration/1000,6).")";
						KalturaLog::log("$deltaStr");
					}
				}
			}
			
			KalturaLog::log("$msgStr");
			KalturaLog::log("OutputFile: ".realpath($chunker->params->output));
			
			$errStr = null;
			$lasted = $this->finishTime - $this->createTime;
				
			if($sessionData->returnStatus==KChunkedEncodeReturnStatus::OK) {
				$msgStr = "RESULT:Success"."  Lasted:".gmdate('H:i:s',$lasted)."/".($lasted)."s";
				if(isset($concurrencyLevel)) {
					$val = end($this->concurrencyHistogram);
					$idle = round($this->concurrencyHistogram[0]/1000,2);
					$msgStr.= ", concurrency:$concurrencyLevel(max:".key($this->concurrencyHistogram).",".round($val/1000,2)."s,idle:$idle"."s)";
				}
			}
			else {
				$msgStr = $sessionData->getErrorMessage();
				$msgStr = "RESULT:$msgStr";
			}
			KalturaLog::log($msgStr);
			KalturaLog::log("***********************************************************");

			$this->SerializeSession();
		}
		
		/********************
		 *
		 */
		public function CleanUp()
		{
			$setup = $this->chunker->setup;
			for($idx=0;$idx<$this->chunker->GetMaxChunks();$idx++){
				$chunkName_wc = $this->chunker->getChunkName($idx,"fix");
				if(file_exists($chunkName_wc)){
					$cmd = "rm -f $chunkName_wc*";
					KalturaLog::log("cleanup cmd:$cmd");
					$rv = null; $op = null;
					$output = exec($cmd, $op, $rv);
				} 
			}
			
			if(!(isset($setup->cleanUp) && $setup->cleanUp)){
				return;
			}
			for($idx=0;$idx<$this->chunker->GetMaxChunks();$idx++){
				$chunkName_wc = $this->chunker->getChunkName($idx,"*");
				$cmd = "rm -f $chunkName_wc";
				KalturaLog::log("cleanup cmd:$cmd");
				$rv = null; $op = null;
				$output = exec($cmd, $op, $rv);
			}
			$mergedFilenameAudio = $this->chunker->getSessionName("audio");
			$cmd = "rm -f $mergedFilenameAudio* ".$concatFilenameLog = $this->chunker->getSessionName("concat");
			KalturaLog::log("cleanup cmd:$cmd");
			$rv = null; $op = null;
			$output = exec($cmd, $op, $rv);
			return;
			$cmd = "rm -f $setup->output*.$this->videoChunkPostfix*";
			$cmd.= " ".$this->chunker->getSessionName("audio")."*";
			$cmd.= " ".$this->chunker->getSessionName("session");
			KalturaLog::log("cleanup cmd:$cmd");
			$rv = null; $op = null;
			$output = exec($cmd, $op, $rv);
		}
		
		/********************
		 *
		 */
		protected function executeCmdline($cmdLine, $logFile=null)
		{
			return KProcessExecutionData::executeCmdline($cmdLine);
		}

		/********************
		 * 
		 */
		public function getErrorMessage()
		{
			switch($this->returnStatus){
				case KChunkedEncodeReturnStatus::InitializeError: 	 $errStr = "InitializeError"; break;
				case KChunkedEncodeReturnStatus::GenerateVideoError: $errStr = "GenerateVideoError"; break;
				case KChunkedEncodeReturnStatus::GenerateAudioError: $errStr = "GenerateAudioError"; break;
				case KChunkedEncodeReturnStatus::FixDriftError: 	 $errStr = "FixDriftError"; break;
				case KChunkedEncodeReturnStatus::AnalyzeError: 		 $errStr = "AnalyzeError"; break;
				case KChunkedEncodeReturnStatus::MergeError: 		 $errStr = "MergeError"; break;
				case KChunkedEncodeReturnStatus::MergeAttemptsError: $errStr = "MergeAttemptsError"; break;
				case KChunkedEncodeReturnStatus::MergeThreshError:   $errStr = "MergeThreshError"; break;
			}
			$msgStr = "Failure - error($errStr/$this->returnStatus),message(".implode(',',$this->returnMessages).")";
			return $msgStr;
		}

		/********************
		 * Save the sessionData to .ses file
		 */
		public function SerializeSession()
		{
			file_put_contents($this->chunker->getSessionName("session"), serialize($this));
		}
		
		/********************
		 * 
		 */
		public static function GetLogTail($logFilename, $size=5000)
		{
			$logTail = null;
			if(file_exists($logFilename)==false) 
				return null;

			$fHd=fopen($logFilename,"r");
			if($fHd===false){
				return null;
			}
			if(fseek($fHd , -$size, SEEK_END)!==0){
				fclose($fHd);
				return null;
			}
			if(($logTail=fread($fHd, $size))===false){
				fclose($fHd);
				return null;
			}
			fclose($fHd);
			return $logTail;
		}

		/********************
		 * 
		 */
		public static function GetLogTailLines($logFilename, $size=5000)
		{
			$logTail = self::GetLogTail($logFilename, $size);
			if($logTail==null)
				return null;
			$lines=preg_split("/\r\n|\n|\r/", $logTail);
			return $lines;
		}

		/********************
		 * 
		 */
		public static function GetSessionStatsJSON($logFilename, $size=10000)
		{
			KalturaLog::log("$logFilename, $size");
			if($lines=self::GetLogTailLines($logFilename, $size)) {
               foreach($lines as $idx=>$line) {
					$prefix=self::SessionStatsJSONLogPrefix;
					if(($pos=strpos($line,$prefix))===false)
							continue;
					KalturaLog::log("$line");
					$jsonStr = substr($line, $pos+strlen($prefix));
					$obj = json_decode($jsonStr);
					KalturaLog::log("JSON:$jsonStr, object:".print_r($obj,1));
					return $obj;
                }
			}
			return null;
		}

		/********************
		 *
		 */
		abstract public function GenerateContent();

	}
	
