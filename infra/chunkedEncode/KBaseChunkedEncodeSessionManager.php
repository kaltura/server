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
		protected $name = null;	
		protected $chunker = null;

		protected $maxFailures = 3;		// Max allowed job failures (if more, get out w/out retry)
		protected $maxRetries = 10;		// Max retries per failed job
		protected $maxExecutionTime = 3600;	// In seconds 
		
		protected $videoCmdLines = array();
		protected $audioCmdLines = array();
		
		protected $createTime = null;	// Secs
		protected $finishTime = null;

		protected $chunkExecutionDataArr = array();

		protected $returnStatus = null;	// KChunkedEncodeReturnStatus
		protected $returnMessages = array();
		
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
			
			if($this->Analyze()>0 && $this->FixChunks()!==true){
				return false;
			}

			if($this->Merge()!=true){
				return false;
			}
			
			if(isset($this->chunker->setup->cleanUp) && $this->chunker->setup->cleanUp){
				$this->CleanUp();
			}
			
			$this->returnStatus = KChunkedEncodeReturnStatus::OK;
			copy($this->chunker->getSessionName(), $this->chunker->params->output);
						
			return true;
		}
		
		/* ---------------------------
		 * Initialize
		 */
		public function Initialize()
		{
			$chunker = $this->chunker;
			$rv = $chunker->Initialize();
			if($rv!==true){
				$this->returnStatus = KChunkedEncodeReturnStatus::InitializeError;
				return $rv;
			}
			if(!isset($this->name))
				$this->name = basename($chunker->params->output);

			$videoCmdLines = array();
			for($chunkIdx=0;$chunkIdx<$chunker->GetMaxChunks();$chunkIdx++) {
				$chunkData = $chunker->GetChunk($chunkIdx);
				$start = $chunkData->start;
				$cmdLine = $chunker->BuildVideoCommandLine($start, $chunkIdx);
				$logFilename = $chunker->getChunkName($chunkIdx,".log");
				$cmdLine = "time $cmdLine > $logFilename 2>&1";
				$outFilename = $chunker->getChunkName($chunkIdx);
				$videoCmdLines[$chunkIdx] = array($cmdLine, $outFilename);
				KalturaLog::log($cmdLine);
			}
			$this->videoCmdLines = $videoCmdLines;
			
			$audioCmdLine = $chunker->BuildAudioCommandLine();
			$this->audioCmdLines = array($audioCmdLine);
			
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
			for($idx=0; $idx<$chunker->GetMaxChunks(); $idx++) {
				$chunkData = $chunker->GetChunk($idx);
				if(!isset($chunkData->toFix) || $chunkData->toFix==0)
					continue;

				$toFixChunkIdx = $chunkData->index;
				
				$chunkFixName = $chunker->getChunkName($toFixChunkIdx, "fix");
				$cmdLine = $chunker->BuildFixVideoCommandLine($toFixChunkIdx)." > $chunkFixName.log 2>&1";
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
			}
			
			return true;
		}
		
		/********************
		 *
		 */
		public function Merge()
		{
			$concatFilenameLog = $this->chunker->getSessionName("concat");

			$mergeCmd = $this->chunker->BuildMergeCommandLine();
			KalturaLog::log("mergeCmd:$mergeCmd");
			$maxAttempts = 3;
			for($attempt=0; $attempt<$maxAttempts; $attempt++) {

				$process = $this->executeCmdline($mergeCmd, $concatFilenameLog);
				if($process==false) {
					KalturaLog::log("FAILED to merge (attempt:$attempt)!");
					sleep(5);
					continue;
				}
				KalturaLog::log("waiting ...");
				KProcessExecutionData::waitForCompletion($process);
				$execData = new KProcessExecutionData($process, $concatFilenameLog);
				if($execData->exitCode!=0) {
					KalturaLog::log("FAILED to merge (attempt:$attempt, exitCode:$execData->exitCode)!");
					sleep(5);
					continue;
				}
				break;
			}
			if($attempt==$maxAttempts){
				KalturaLog::log($msgStr="FAILED to merge, leaving!");
				$this->returnMessages[] = $msgStr;
				$this->returnStatus = KChunkedEncodeReturnStatus::MergeAttemptsError;
				return false;
			}

			if($this->chunker->ValidateMergedFile($msgStr)!=true){
				KalturaLog::log($msgStr);
				$this->returnMessages[] = $msgStr;
				$this->returnStatus = KChunkedEncodeReturnStatus::MergeThreshError;
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
				$msgStr.= ",file dur(v:".round($fileDtMrg->videoDuration/1000,4).",a:".round($fileDtMrg->audioDuration/1000,4).")";
			}
			if(isset($sessionData->refFileDt)) {
				$fileDtRef = $sessionData->refFileDt;
				KalturaLog::log("reference:".print_r($fileDtRef,1));
			}
			$fileDtSrc = $chunker->sourceFileDt;
			if(isset($fileDtSrc)){
				KalturaLog::log("source:".print_r($fileDtSrc,1));
			}
			
			{
				KalturaLog::log("CSV,idx,startedAt,user,system,elapsed,cpu");
				$userAcc = $systemAcc = $elapsedAcc = $cpuAcc = 0;
				foreach($this->chunkExecutionDataArr as $idx=>$execData){
					$userAcc+= $execData->user;
					$systemAcc+= $execData->system;
					$elapsedAcc+= $execData->elapsed;
					$cpuAcc+= $execData->cpu;
					
					KalturaLog::log("CSV,$idx,$execData->startedAt,$execData->user,$execData->system,$execData->elapsed,$execData->cpu");
				}
				$cnt = $chunker->GetMaxChunks();
				$userAvg 	= round($userAcc/$cnt,3);
				$systemAvg 	= round($systemAcc/$cnt,3);
				$elapsedAvg = round($elapsedAcc/$cnt,3);
				$cpuAvg 	= round($cpuAcc/$cnt,3);
			}
			
//			KalturaLog::log("LogFile: ".$chunker->getSessionName("log"));
			KalturaLog::log("***********************************************************");
			KalturaLog::log("* Session Summary (".date("Y-m-d H:i:s").")");
			KalturaLog::log("* ");
			KalturaLog::log("ExecutionStats:chunks($cnt),accum(elapsed:$elapsedAcc,user:$userAcc,system:$systemAcc),average(elapsed:$elapsedAvg,user:$userAvg,system:$systemAvg,cpu:$cpuAvg)");
			if($sessionData->returnStatus==KChunkedEncodeReturnStatus::AnalyzeError){
				$msgStr.= ",analyze:BAD";
			}
			if($sessionData->returnStatus==KChunkedEncodeReturnStatus::OK){
				$msgStr.= ",analyze:OK";
				$frameRateMode = stristr($fileDtMrg->rawData,"Frame rate mode                          : ");
				$frameRateMode = strtolower(substr($frameRateMode, strlen("Frame rate mode                          : ")));
				$frameRateMode = strncmp($frameRateMode,"constant",8);
				if($frameRateMode==0) {
					$msgStr.= ",frameRateMode(constant)";
				}
				else
					$msgStr.= ",frameRateMode(variable)";
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
			KalturaLog::log("OutputFile: ".realpath($chunker->getSessionName()));
			
			$errStr = null;
			$lasted = $this->finishTime - $this->createTime;
				
			if($sessionData->returnStatus==KChunkedEncodeReturnStatus::OK) {
				$msgStr = "RESULT:Success"."  Lasted:".gmdate('H:i:s',$lasted)."/".($lasted)."secs";
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
		abstract public function GenerateContent();

	}
	
