<?php

 /*****************************
 * Includes & Globals
 */
//ini_set("memory_limit","2048M");

	/********************
	 * Chunked Encoding Session Manager module
	 */
	class KChunkedEncodeSessionManagerStandalone extends KBaseChunkedEncodeSessionManager 
	{
		public $processArr = array();	// Chunk process ids array
		public $audioProcess = null;	// Audio porcess id
		
		/********************
		 * LoadFromCmdLineArgs
		 */
		public static function LoadFromCmdLineArgs(array $argv)
		{
			$setup = new KChunkedEncodeSetup();
			self::parseArgsToSetup($argv,$setup);

				/*
				 * Otherwise - start Chunked Encode session
				 */
			$sessionManager = new KChunkedEncodeSessionManagerStandalone($setup);
			return $sessionManager;
		}
		
		/********************
		 * LoadFromSessionFile
		 */
		public static function LoadFromSessionFile($sessionFilename)
		{
			if(file_exists($sessionFilename)!=true){
				return null;
			}
			$sessionData =  unserialize(file_get_contents($sessionFilename));
			KalturaLog::log("sessionData:".print_r($sessionData,1));
			$setup = new KChunkedEncodeSetup();
			$sessionManager = new KChunkedEncodeSessionManagerStandalone($setup);
			$fldsArr = get_object_vars($sessionData);
			foreach($fldsArr as $fld=>$val){
				$sessionManager->$fld = $val;
			}
			return $sessionManager;
		}
		
		/********************
		 * Initialize the chunked encode session
		 */
		public function Initialize()
		{
			$mergedFilenameSession = $this->chunker->getSessionName("session");
			if((isset($this->chunker->setup->restore) && $this->chunker->setup->restore==1) 
			&& file_exists($mergedFilenameSession)==true){
				$sessionData =  unserialize(file_get_contents($mergedFilenameSession));
				$fldsArr = get_object_vars($sessionData);
				foreach($fldsArr as $fld=>$val){
					$this->$fld = $val;
				}

				KalturaLog::log("sessionData:".print_r($this,1));
				KalturaLog::log("duration(".$this->chunker->params->duration."), frameDuration(".$this->chunker->params->frameDuration.")\n");
				return true;
			}

			$rv = parent::Initialize();
			if($rv!==true){
//				$this->SerializeSession();
//				$this->returnStatus = KChunkedEncodeReturnStatus::InitializeError;
				return $rv;
			}
			
			if(!isset($this->chunker->setup->commandExecitionScript)) {
				$this->chunker->setup->commandExecitionScript = $this->chunker->setup->output."execitionScript.sh";
				file_put_contents($this->chunker->setup->commandExecitionScript, 'echo $@'."\n".'echo $1'."\n".'eval "$1"'."\n"."echo exit_code:".'$?');
				chmod($this->chunker->setup->commandExecitionScript,0755);
				sleep(2);
			}
			$this->SerializeSession();
			return $rv;
		}
		
		/********************
		 * GenerateContent
		 */
		public function GenerateContent()
		{
			if($this->generateAudioStart()!=true){
				return false;
			}
			
			if($this->generateVideo()!=true){
				return false;
			}

			if($this->generateAudioFinish()!=true){
				return false;
			}
			
			return true;
		}
		
		/********************
		 * generateVideo
		 */
		protected function generateVideo()
		{
			for($idx=0;$idx<$this->chunker->GetMaxChunks();$idx++) {
				if($this->generateVideoChunk($idx)!=true) {
					return false;
				}
				$rv = $this->waitForChunksCompletion();
				if($rv==false){
					KalturaLog::log($msgStr="Failed to convert chunks 1(".serialize($this->processArr).")!");
					$this->returnMessages[] = $msgStr;
					$this->returnStatus = KChunkedEncodeReturnStatus::GenerateVideoError;
					return false;
				}
			}
			$rv = $this->generateVideoFinish();
			if($rv!=true) 
				return $rv;
			$this->SerializeSession();
			return true;
		}
		
		/********************
		 * generateVideoChunk
		 */
		protected function generateVideoChunk($chunkIdx) 
		{
			$chunkData = $this->chunker->GetChunk($chunkIdx);
			$start = $chunkData->start;

			KalturaLog::log("chunk $chunkIdx:".date("Y-m-d H:i:s"));
			$rv = $this->startVideoChunkConvert($start, $chunkIdx);
			if($rv==false) {
				KalturaLog::log($msgStr="Failed to convert chunk $chunkIdx!");
				$this->returnMessages[] = $msgStr;
				$this->returnStatus = KChunkedEncodeReturnStatus::GenerateVideoError;
				return false;
			}

			$this->SerializeSession();

			return true;
		}

		/********************
		 * generateVideoFinish
		 */
		protected function generateVideoFinish() 
		{
			KalturaLog::log("Inside");
			if(count($this->processArr)!=0) {
				$rv = $this->waitForChunksCompletion();
				if($rv==false){
					KalturaLog::log($msgStr="Failed to convert chunks 2(".serialize($this->processArr).")!");
					$this->returnMessages[] = $msgStr;
					$this->returnStatus = KChunkedEncodeReturnStatus::GenerateVideoError;
					return false;
				}
			}
			$this->getChunkArrFileStatData(0);
			
			$this->SerializeSession();
			
			return true;

		}
		
		/********************
		 * generateAudioStart
		 */
		protected function generateAudioStart() 
		{
			/*
			 * Generate audio stream
			 */
			if(isset($this->audioProcess)) {
				$mergedFilenameAudio = $this->chunker->getSessionName("audio");
				if($this->audioProcess!=0 && file_exists("$mergedFilenameAudio.rv")) {
					$rv = file_get_contents("$mergedFilenameAudio.rv");
				}
			}
			else
				$rv = 1;
			if($rv) {
				$this->audioProcess = $this->generateAudioStream();
				if($this->audioProcess==false) {
					KalturaLog::log($msgStr="Audio convert: FAILED!");
					$this->returnMessages[] = $msgStr;
					$this->returnStatus = KChunkedEncodeReturnStatus::GenerateAudioError;
					return false;
				}
			}
			return true;
		}
		
		/********************
		 * generateAudioFinish
		 */
		protected function generateAudioFinish() 
		{
			if(!(isset($this->audioProcess) && $this->audioProcess!=0))
				return true;

			KalturaLog::log("Audio convert: waiting ...");
			$mergedFilenameAudio = $this->chunker->getSessionName("audio");
			KProcessExecutionData::waitForCompletion($this->audioProcess);
			$execData = new KProcessExecutionData($this->audioProcess, $mergedFilenameAudio.".log");
			if($execData->exitCode!=0) {
				KalturaLog::log($msgStr="Audio convert: FAILED, exitCode($execData->exitCode)!");
				$this->returnMessages[] = $msgStr;
				$this->returnStatus = KChunkedEncodeReturnStatus::GenerateAudioError;
				return false;
			}
			$this->audioProcess = 0;
			KalturaLog::log("Audio convert: OK!");
			
			$this->SerializeSession();
			
			return true;
		}
		
		/********************
		 * Evaluate the ad-hock concurrency level
		 * - Check whether there are active jobs (linux) that do not belong to the current session,
		 * - Evaluate how many other sessions are running.
		 * - Set the next concurrency level to match the max-concurrency and number of running sessions
		 */
		protected function evaluateConcurrency()
		{
// ps -ef | grep "ffmpeg.*chunkenc" | grep -v 'reference\|sh \|php\|grep\|audio' | '
// Previous - 
// ps -ef | grep ffmpeg | grep -v "sh \|grep\|"/web2/content/shared/tmp/qualityTest/TestBench.9/conversions/Dec24/1_snny9faa_400_1_b79i4vhn_29.97fps | grep chunkenc | awk 'NF>1{print $NF}'
			$setup = $this->chunker->setup;

				/*
				 * Filter in - ffmpeg & chunkenc
				 */
			$cmdLine = "ps -ef | grep \"$setup->ffmpegBin.*".$this->chunker->chunkEncodeToken."\"";
				/*
				 * Filter out - reference & sh & php & grep & audio & current-session-name
				 */
			$cmdLine.= "| grep -v 'reference\|sh \|php\|grep\|audio\|time'";

			$lastLine=exec($cmdLine , $outputArr, $rv);
			if($rv!=0) {
				KalturaLog::log("No other chunk sessions (rv:$rv).");
			}
			$chunkedSesssionsArr = array();
			$fallbackSesssionsArr = array();
			$thisChunks = 0;
			foreach($outputArr as $idx=>$line) {
				$line = trim($line);
				if(strlen($line)==0)
					continue;
				$lineArr = explode(' ', $line);
				if(count($lineArr)==0)
					continue;
				
				/*
				 * Retrieve the ouput filename from the ffmpeg cmd-line 
				 */
				if(($key=array_search('-pass', $lineArr))!==false && $lineArr[$key+1]==1) {
					if(($key=array_search('-passlogfile', $lineArr))!==false) {
						$outputFileName = basename($lineArr[$key+1]);
					}
				}
				else
					$outputFileName = basename(end($lineArr));
				
				/*
				 * Skip cases/cmd-lines that does not contain the 'chunkEncodeToken'
				 * Only cmd-lines with this token participate in chunked encode flow
				 */
				if(($sessionName=strstr($outputFileName,"_".$this->chunker->chunkEncodeToken, true))===false)
					continue;
				
				/*
				 * Gather into separete arrays - 
				 * - 'normal' chunked encodes (chunkedSesssionsArr)
				 * - the 'fallback' cases (fallbackSesssionsArr)
				 * - Sum up jobs that belong to that specific session (this) 
				 */
				if(strstr($outputFileName,"_fallback")===false){
					if(key_exists($sessionName,$chunkedSesssionsArr))
						$chunkedSesssionsArr[$sessionName] = $chunkedSesssionsArr[$sessionName]+1;
					else 
						$chunkedSesssionsArr[$sessionName] = 1;
					if(strstr($setup->output,$sessionName)!==false)
						$thisChunks++;
				}
				else {
					if(key_exists($sessionName,$fallbackSesssionsArr))
						$fallbackSesssionsArr[$sessionName] = $fallbackSesssionsArr[$sessionName]+1;
					else 
						$fallbackSesssionsArr[$sessionName] = 1;
				}
			}
			
				/*
				 * Sum up 
				 * - all currently processing chunks
				 * - all currenly processed sessions
				 * - all fallback sessions
				 */
			$allChunks = array_sum($chunkedSesssionsArr);
			$chunkedSesssionsCnt = count($chunkedSesssionsArr);
			if($thisChunks==0)
				$chunkedSesssionsCnt+= 1;
			$fallbackSesssionsCnt = count($fallbackSesssionsArr);
			
				/*
				 * Concurrency evaluation rules - 
				 * - Every active session should ALWAYS run at least ONE chunk
				 * - All sessions should get the same concurrency level (number of concurrently running chunks)
				 * -- An exception to the above are fallback sessions (they are not chunked)
				 * Therefore the calculation is bellow - 
				 */
			$newConcurrency = round(($setup->concurrent-$fallbackSesssionsCnt)/$chunkedSesssionsCnt);
				/*
				 * If for some reason the total sum of all concurrently executed chunks 
				 * is going to be higher than the allowed limit (setup::concurrent) => trim it to the limit val
				 */
			if($setup->concurrent<($allChunks-$thisChunks)+$newConcurrency)
				$newConcurrency = $setup->concurrent - ($allChunks-$thisChunks);
				/*
				 * If on the other hand there are remaining free execution slots - 
				 * randomly assign it to th ecurrent session
				 */
			else if($setup->concurrent>($allChunks-$thisChunks)+$newConcurrency) {
				$additional = rand(1,$chunkedSesssionsCnt);
				if($additional==1){
					$newConcurrency++;
				}
			}
			if($newConcurrency<$setup->concurrentMin)
				$newConcurrency = $setup->concurrentMin;
			$msgStr = "maxConcurrent($newConcurrency),sessions($chunkedSesssionsCnt),chunks($allChunks),this($thisChunks),fallbacks($fallbackSesssionsCnt),".serialize($chunkedSesssionsArr);
			if(isset($additional))
				$msgStr.=",added";
			KalturaLog::log($msgStr);
			return $newConcurrency;
		}

		/********************
		 * pendingChunksCount
		 */
		protected function pendingChunksCount()
		{
			return ($this->chunker->GetMaxChunks()-count($this->chunkExecutionDataArr));
		}

		/********************
		 * rv - bool
		 */
		protected function waitForChunksCompletion($sleepTime=2)
		{
			$runningArr = array();
			$processCnt = 0;

			$runningArr = $this->getRunningArray(&$processCnt);

			$this->processArr = $runningArr;
			$runningCnt = count($runningArr);
			while(1) {
				if($processCnt<$this->chunker->GetMaxChunks()) {
					$concurrent = $this->evaluateConcurrency();
					if($runningCnt<$concurrent) {
						KalturaLog::log("Available(".($concurrent-$runningCnt).") processing slots (runningCnt:$runningCnt, maxConcurrent:$concurrent)");
						break;
					}
				}
				if($this->monitorFinished($runningArr)!=true){
					return false;
				}

				$runningCnt = count($runningArr);
					// If none of the processes got finished and there are still running process - sleep for a while ...
					// otherwise - get out to run a new chunk;
				if($runningCnt==0) {
					KalturaLog::log("Finished - No running processes!!");
					break;
				}
				KalturaLog::log("Running($runningCnt=>".implode(',',$runningArr)."),Pending(".$this->pendingChunksCount().")");
				if($this->getChunkArrFileStatData()==0)
					sleep($sleepTime);
			}
			KalturaLog::log("Running($runningCnt=>".implode(',',$runningArr)."),Pending(".$this->pendingChunksCount().")");
			$this->processArr = $runningArr;
			return true;
		}

		/********************
		 * monitorFinished
		 */
		protected function monitorFinished(&$runningArr)
		{
			foreach($runningArr as $idx=>$process) {
				if(KProcessExecutionData::isProcessRunning($process)==true){
					continue;
				}
				
				$executionData = $this->chunkExecutionDataArr[$idx];
				{
					$logFileName = $this->chunker->getChunkName($idx,".log");
					$executionData->parseLogFile($logFileName);
					KalturaLog::log(print_r($executionData,1));
				}
				unset($runningArr[$idx]);
				if($executionData->exitCode!=0) {
					KalturaLog::log("Failed to convert chunk($idx),process($process)==>exitCode($executionData->exitCode)!");
					$this->processArr = $runningArr;
					return false;
				}
				KalturaLog::log("chunk($idx),process($process)==>exitCode($executionData->exitCode)");
				break;
			}
			return true;
		}
		
		/********************
		 * executeCmdline
		 */
		protected function executeCmdline($cmdLine, $logFile=null)
		{
			$executionScript=$this->chunker->setup->commandExecitionScript;
//			$cmdLine = "$executionScript \"time $cmdLine \" >> $logFile 2>&1 & echo $! ";
			$cmdLine = "$executionScript \"time $cmdLine \" >> $logFile 2>&1 ";
			$started = time();
			file_put_contents($logFile, "Started:".date('Y-m-d H:i:s', $started)."\n");
			KalturaLog::log("cmdLine:\n$cmdLine\n");
			return parent::executeCmdline($cmdLine);
		}

		/********************
		 * startVideoChunkConvert
		 */
		protected function startVideoChunkConvert($start, $chunkIdx)
		{
			$chunkFilename = $this->chunker->getChunkName($chunkIdx,"base");
			KalturaLog::log("start($start), chunkIdx($chunkIdx), chunkFilename($chunkFilename) :".date("Y-m-d H:i:s"));

			$cmdLine = $this->chunker->BuildVideoCommandLine($start, $chunkIdx);
			$cmdLine = $this->videoCmdLines[$chunkIdx];
			if(is_array($cmdLine))
				$cmdLine = $cmdLine[0];
			$cmdLine = str_replace("&& ", "&& time ", $cmdLine);
			
			$process = $this->executeCmdline($cmdLine, "$chunkFilename.log");
			$execData = new KProcessExecutionData($process);
			$this->chunkExecutionDataArr[$chunkIdx] = $execData;
			if($process===false) {
				KalturaLog::log("Failed to convert chunk $chunkIdx!");
				return false;
			}

			$this->processArr[$chunkIdx] = $process;
			
			return true;
		}

		/********************
		 * rv  - PID, -1(error), null(no audio)
		 */
		protected function generateAudioStream()
		{
			KalturaLog::log(date("Y-m-d H:i:s"));
			$cmdLine = $this->audioCmdLines[0];
			if(!isset($cmdLine))
				return null;

			$mergedFilenameAudio = $this->chunker->getSessionName("audio");
			$process = $this->executeCmdline($cmdLine, "$mergedFilenameAudio.log");
			KalturaLog::log("pid:".print_r($process,1));
			return $process;
		}

		/********************
		 * getChunkArrFileStatData
		 */
		protected function getChunkArrFileStatData($maxCnt=4)
		{
			if($maxCnt==0) $maxCnt = PHP_INT_MAX;
			$cnt = 0;
//			foreach($this->chunker->chunkDataArr as $idx=>$chunkData){
			for($idx=0; $idx<$this->chunker->GetMaxChunks(); $idx++) {
				$chunkData = $this->chunker->GetChunk($idx);
				if(key_exists($chunkData->index,$this->chunkExecutionDataArr)) {
					$execData = $this->chunkExecutionDataArr[$chunkData->index];
					if(isset($execData->exitCode) && $execData->exitCode==0 && !isset($chunkData->stat)){
						$this->updateChunkFileStatData($chunkData->index);
						if(++$cnt>=$maxCnt) break;
					}
				}
			}
			return($cnt);
		}
		
		/********************
		 * updateChunkFileStatData
		 *	
		 */
		protected function updateChunkFileStatData($idx)
		{
			$chunkFileName = $this->chunker->getChunkName($idx);
			$statFileName = "$chunkFileName.stat";

			$stat = new KChunkFramesStat($chunkFileName, $this->chunker->setup->ffprobeBin, $this->chunker->setup->ffmpegBin);
			$this->chunker->updateChunkFileStatData($idx, $stat); 
			$jsonStr = json_encode($stat);
			if(!file_exists($statFileName)){
				file_put_contents($statFileName, $jsonStr);
			}
			return $stat;
		}

		/********************
		 *
		 
		public static function quickFixCmdline($cmdLine)
		{
			$cmdLineArr = explode(" ",$cmdLine);
			
			$toFixArr = array("-force_key_frames","-filter_complex","-rc_eq");
			foreach($toFixArr as $param){
				if(($key=array_search($param, $cmdLineArr))!==false){
					if(strstr($cmdLineArr[$key+1], "'")===false) {
						$cmdLineArr[$key+1] = "'".$cmdLineArr[$key+1]."'";
					}
				}
			}
			$cmdLine = implode(" ", $cmdLineArr);
			return $cmdLine;
		}
		*/
		/********************
		 *
		 */
		protected function getRunningArray(&$processCnt)
		{
			$runningArr = array();
			foreach($this->chunkExecutionDataArr as $idx=>$execData) {
				if(isset($execData->process)){
					if(!isset($execData->exitCode))
						$runningArr[$idx] = $execData->process;
					$processCnt++;
				}
			}
			return $runningArr;
		}
		
		/*****************************
		 * parseArgsToSetup
		 */
		protected static function parseArgsToSetup($argv, $setup)
		{
			unset($argv[0]);
			if(($idx=array_search("ANDAND", $argv))!==false){
				$argv[$idx] = '&&';
			}
			$setupArr = get_object_vars($setup);
			foreach($setupArr as $fld=>$val){
				if(($idx=array_search("-$fld", $argv))!==false){
					$setup->$fld = $argv[$idx+1];
					unset($argv[$idx+1]);
					unset($argv[$idx]);
				}
			}
			KalturaLog::log($setup);

			if(!isset($setup->startFrom)) 		$setup->startFrom = 0;
			if(!isset($setup->createFolder))	$setup->createFolder = 1;
			
			$setup->cmd = implode(' ',$argv);
			
			KalturaLog::log("Setup:".print_r($setup,1));
		}

		/* ---------------------------
		 * ExecuteSession
		 */
		public static function ExecuteSession($concurrent, $concurrentMin, $sessionName, $cmdLine)
		{
//			if(($idx=array_search("ANDAND", $argv))!==false){
//				$argv[$idx] = '&&';
//			}

			KalturaLog::log("concurrent:$concurrent, concurrentMin:$concurrentMin, sessionName:$sessionName, cmdLine:$cmdLine");
			
			$setup = new KChunkedEncodeSetup;
			$setup->concurrent = $concurrent;
			$setup->cleanUp = 0;
			$setup->cmd = $cmdLine;
			
			$session = new KChunkedEncodeSessionManagerStandalone($setup, $sessionName);
			
			if(($rv=$session->Initialize())!=true) {
				$session->Report();
				return $rv;
			}
			$rv = $session->Generate();
			$session->Report();
			return $rv;
		}

	}
	
