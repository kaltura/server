<?php

 /*****************************
  * Includes & Globals
  */

	/********************
	 * Session exit statuses
	 */
	class KChunkedEncodeReturnStatus {
		const OK = 0;
		const InitializeError = 1000;
		const GenerateVideoError = 1001;
		const GenerateAudioError = 1002;
		const FixDriftError = 1003;
		const AnalyzeError = 1004;
		const MergeError = 1005;
		const MergeAttemptsError = 1006;
		const MergeThreshError = 1007;
	}
	
	/********************
	 * Session setup values
	 */
	class KChunkedEncodeSetup {

				// DefaultChunkDuration for frame height>1280 (basically 4K and QHD). 
				// Chunk dur's for smaller frames evaluated from that value.
		const	DefaultChunkDuration = 30;  // secs
		const	DefaultChunkOverlap =  0.5; // secs
		const	DefaultConcurrentChunks = 1;// secs
		
		public $ffmpegBin = "ffmpeg"; 
		public $ffprobeBin = "ffprobe";

		public $commandExecitionScript = null;
		
		public $source=null;			// Source & output files
		public $output=null;			// 

		public $cmd = null; 			// cmd-line (optional)
		public $fps = null; 			// (mandatory)
		public $gop = null; 			// (optional)
		public $chunkDuration = null;	// If not set (secs), evaluated - SD assets:2 min, HD: 1 min
		public $chunkOverlap = null;	// Overlap between the chunks (secs) . If not set - at least 10 frames
		public $concurrent = 10;		// Max concurrency
		public $concurrentMin = 1;		// Min concurrency
		public $duration = -1; 			// 
		public $startFrom = 0; 		//
		public $passes = null;			// 1-pass encoding is default
		public $threadsDec = null;
		public $threadsEnc = null;
		
		public $createFolder = 1;
		public $cleanUp = 1;
		public $sharedChunkPath = null; //Added to support FS wrappers which do not support shared NFS storage (S3)
		
		/********************
		 * C'tor
		 */
		public function __construct($source=null, $output=null, $cmd=null, $fps=null, $gop=null)
		{
			if(isset($source)) $this->source = $source;
			if(isset($output)) $this->output = $output;
			if(isset($cmd)) $this->cmd = $cmd;
			if(isset($fps)) $this->fps = $fps;
			if(isset($gop)) $this->gop = $gop;

			/*
			 * Update setup to defaults
			 */
			$this->concurrent = $this->concurrentMin = self::DefaultConcurrentChunks;
		}
		
		/********************
		 * 
		 */
		public function Update($params)
		{
			if(!isset($this->chunkOverlap)) {
				if(self::DefaultChunkOverlap/$params->frameDuration > 10)
					$this->chunkOverlap = self::DefaultChunkOverlap;
				else
					$this->chunkOverlap = round($params->frameDuration *10,2);
			}

			if(!isset($this->chunkDuration)) {
$this->chunkDuration = self::calculateChunkDuration($params->height);
			}

			if($this->concurrentMin>$this->concurrent)
				$this->concurrentMin = $this->concurrent;
		}
		
		/********************
		 * calculateChunkDuration
		 */
		public static function calculateChunkDuration($height)
		{
			if($height>1280)
				return self::DefaultChunkDuration;
			else if($height>480)
				return self::DefaultChunkDuration*2;
			else if($height>360)
				return self::DefaultChunkDuration*4;
			else
				return self::DefaultChunkDuration*6;
		}
	}

	/********************
	 * 
	 */
	class KChunkFramesStat {
		public $start = null;
		public $frame = 0;
		public $finish = null;
		public $type = null;
		
		public function __construct($chunkFileName=null, $ffprobeBin="ffprobe", $ffmpegBin="ffmpeg",$tmpPromptFolder="/tmp")
		{
			if(isset($chunkFileName)){
				$rv = $this->getData($chunkFileName, $ffprobeBin, $ffmpegBin, $tmpPromptFolder);
				if(!isset($rv))
					return null;
			}
		}
		
		/********************
		 * isEmpty
		 */
		public function isEmpty() {
			return (is_null($this->finish) || is_null($this->finish) || is_null($this->frame) 
			|| is_null($this->start) || $this->finish==0 || $this->frame==0);
		}
		
		/********************
		 * getData
		 *	Retrieve following chunk stat data - 
		 */
		public function getData($chunkFileName, $ffprobeBin="ffprobe", $ffmpegBin="ffmpeg", $tmpPromptFolder="/tmp")
		{
			return $this->getDataMpegts($chunkFileName, $ffprobeBin, $ffmpegBin, $tmpPromptFolder);
		}
		
		/********************
		 * getDataMP4
		 *	Retrieve following chunk stat data - 
		 */
		public function getDataMP4($chunkFileName, $ffprobeBin="ffprobe", $ffmpegBin="ffmpeg", $tmpPromptFolder="/tmp")
		{
			KalturaLog::log("$chunkFileName");
			$cmdLine = "$ffprobeBin -show_streams -select_streams v -v quiet -show_entries stream=duration,nb_frames -print_format csv $chunkFileName";
			
			KalturaLog::log("copy:$cmdLine");
			$lastLine=exec($cmdLine , $outputArr, $rv);
			if($rv!=0) {
				KalturaLog::log("ERROR: failed to extract frame data from chunk ($chunkFileName).");
				return null;
			}
			KalturaLog::log("copy:rv($rv), output:\n".print_r($outputArr,1));
			list($stam,$duration,$frames,$stam2) = explode(",",$outputArr[0]);
			KalturaLog::log("duration:$duration,frames:$frames");
/**/
			$outputArr = array();
			$cmdLine = "$ffmpegBin -t 1 -i $chunkFileName -c:v copy -an -copyts -vsync 1 -f matroska -y -v quiet - | $ffprobeBin -select_streams v -show_frames -show_entries frame=coded_picture_number,pkt_pts_time,pict_type,pkt_size -print_format csv -v quiet - | (head -n1)";
			KalturaLog::log("head:$cmdLine");
			$lastLine=exec($cmdLine , $outputArr, $rv);
			if($rv!=0) {
				KalturaLog::log("ERROR: failed to extract frame data from chunk ($chunkFileName).");
				return null;
			}
			KalturaLog::log("head:rv($rv), output:\n".print_r($outputArr,1));
			if($duration<10) {
				$startFrom = 0;
			}
			else {
				$startFrom = $duration-4;
			}
			$cmdLine = "$ffmpegBin -ss $startFrom -i $chunkFileName -c:v copy -an -copyts -vsync 1 -f matroska -y -v quiet - | $ffprobeBin -f matroska -select_streams v -show_frames -show_entries frame=coded_picture_number,pkt_pts_time,pict_type,pkt_size -print_format csv -v quiet - | tail -n20";
			KalturaLog::log("tail:$cmdLine");
			$lastLine=exec($cmdLine , $outputArr, $rv);
			if($rv!=0) {
				KalturaLog::log("ERROR: failed to extract frame data from chunk ($chunkFileName).");
				return null;
			}
			KalturaLog::log("tail:rv($rv), output:\n".print_r($outputArr,1));
			foreach($outputArr as $idx=>$outputLine) {
				if(strlen(trim($outputLine))==0) {
					unset($outputArr[$idx]);
				}
				else if(strncmp(trim($outputLine),"frame",5)!=0) {
					unset($outputArr[$idx]);
				}
			}
			KalturaLog::log("trimmed:output:\n".print_r($outputArr,1));
			
			$outputLine = array_shift($outputArr);
			list($stam,$pts,$size,$type,$frame) = explode(",",$outputLine);
			$this->start = $pts;
			$this->size = $size;
			$outputLine = end($outputArr);
			list($stam,$pts,$size,$type,$frame) = explode(",",$outputLine);
			$this->finish = $pts;
			$this->type = $type;
			$this->frame = $frames;

			$jsonStr = json_encode($this);
			KalturaLog::log("$jsonStr");
		}
		
		/********************
		 * getDataMpegts
		 *	Retrieve following chunk stat data - 
		 */
		public function getDataMpegts($chunkFileName, $ffprobeBin="ffprobe", $ffmpegBin="ffmpeg", $tmpPromptFolder="/tmp")
		{
			KalturaLog::log("$chunkFileName");
				/*
				 * In order to save AWS egress traffic, 
				 * store the tmp MP4 file in th local /tmp folder
				 */
//			$mp4TmpFile = "$chunkFileName.mp4";
			$mp4TmpFile = "$tmpPromptFolder/".basename($chunkFileName).".mp4";
			$cmdLine = "$ffmpegBin -i $chunkFileName -c copy -f mp4 -v quiet -y $mp4TmpFile;$ffprobeBin -show_streams -select_streams v -v quiet -show_entries stream=duration,nb_frames -print_format csv $mp4TmpFile; unlink $mp4TmpFile";
			KalturaLog::log("copy:$cmdLine");
			$lastLine=exec($cmdLine , $outputArr, $rv);
			if($rv!=0) {
				KalturaLog::log("ERROR: failed to extract frame data from chunk ($chunkFileName).");
				return null;
			}
			KalturaLog::log("copy:rv($rv), output:\n".print_r($outputArr,1));
			list($stam,$duration,$frames,$stam2) = explode(",",$outputArr[0]);
			KalturaLog::log("duration:$duration,frames:$frames");
/**/
			$outputArr = array();
			$cmdLine = "$ffmpegBin -t 1 -i $chunkFileName -c:v copy -an -copyts -mpegts_copyts 1 -vsync 1 -f mpegts -y -v quiet - | $ffprobeBin -select_streams v -show_frames -show_entries frame=coded_picture_number,pkt_pts_time,pict_type,pkt_size -print_format csv -v quiet - | (head -n1)";
			KalturaLog::log("head:$cmdLine");
			$lastLine=exec($cmdLine , $outputArr, $rv);
			if($rv!=0) {
				KalturaLog::log("ERROR: failed to extract frame data from chunk ($chunkFileName).");
				return null;
			}
			KalturaLog::log("head:rv($rv), output:\n".print_r($outputArr,1));
			if($duration<10) {
				$startFrom = 0;
			}
			else {
				$startFrom = $duration-4;
			}
			$cmdLine = "$ffmpegBin -ss $startFrom -i $chunkFileName -c:v copy -an -copyts -mpegts_copyts 1 -vsync 1 -f mpegts -y -v quiet - | $ffprobeBin -f mpegts -select_streams v -show_frames -show_entries frame=coded_picture_number,pkt_pts_time,pict_type,pkt_size -print_format csv -v quiet - | tail -n20";
			KalturaLog::log("tail:$cmdLine");
			$lastLine=exec($cmdLine , $outputArr, $rv);
			if($rv!=0) {
				KalturaLog::log("ERROR: failed to extract frame data from chunk ($chunkFileName).");
				return null;
			}
			KalturaLog::log("tail:rv($rv), output:\n".print_r($outputArr,1));
			foreach($outputArr as $idx=>$outputLine) {
				if(strlen(trim($outputLine))==0) {
					unset($outputArr[$idx]);
				}
				else if(strncmp(trim($outputLine),"frame",5)!=0) {
					unset($outputArr[$idx]);
				}
			}
			KalturaLog::log("trimmed:output:\n".print_r($outputArr,1));
			
			$outputLine = array_shift($outputArr);
			list($stam,$pts,$size,$type,$frame) = explode(",",$outputLine);
			$this->start = $pts;
			$this->size = $size;
			$outputLine = end($outputArr);
			list($stam,$pts,$size,$type,$frame) = explode(",",$outputLine);
			$this->finish = $pts;
			$this->type = $type;
			$this->frame = $frames;
			
			$jsonStr = json_encode($this);
			KalturaLog::log("$jsonStr");

		}
		
		/********************
		 * getFrameData
		 *	Retrieve following chunk stat data - 
		 */
		public static function getFrameData($fileName, $startFrom, $duration, $ffprobeBin="ffprobe", $ffmpegBin="ffmpeg")
		{
			$outputArr = array();
			$cmdLine = "$ffmpegBin -ss $startFrom -t $duration -i $fileName -c:v copy -an -copyts -mpegts_copyts 1 -vsync 1 -f mpegts -y -v quiet - | $ffprobeBin -select_streams v -show_frames -show_entries frame=coded_picture_number,pkt_pts_time,pict_type -print_format csv -v quiet - ";
			KalturaLog::log("head:$cmdLine");
			$lastLine=exec($cmdLine , $outputArr, $rv);
			if($rv!=0) {
				KalturaLog::log("ERROR: failed to extract frame data from chunk ($fileName).");
				return null;
			}
			$statsArr = array();
			foreach($outputArr as $idx=>$line) {
				if(strlen($line)>10){
					list($stam,$pts,$type,$frame) = explode(",",$line);
					$framesStat = new KChunkFramesStat();
					$framesStat->start = $pts;
					$framesStat->type = $type;
					$framesStat->frame = (int)($frame);
					$statsArr[] = $framesStat;
				}
			}
			return $statsArr;
		}
	
	}
	
	/********************
	 * Process stat data
	 */
	class KProcessExecutionData {
		public $process = null;	// ...
		public $exitCode = null;// ...
		public $startedAt = null;
		
		public $user = null;
		public $system = null;
		public $elapsed = null;
		public $cpu = null;
		
		/********************
		 *
		 */
		public function __construct($process = null, $logFileName = null)
		{
			if(isset($process)){
				$this->process = $process;
			}
			if(isset($logFileName)){
				$this->parseLogFile($logFileName);
			}
		}
		
		/********************
		 *
		 */
		public static function executeCmdline($cmdLine)
		{
			$cmdLine = "$cmdLine & echo $! ";
			$started = date_create();
			KalturaLog::log("cmdLine:\n$cmdLine\n");

			$rv = 0;
			$op = null;
			
			exec($cmdLine,$op,$rv);
			if($rv!=0) {
				return false;
			}
			$pid = implode("\n",$op);
			KalturaLog::log("pid($pid), rv($rv)");
			return $pid;
		}
		
		/********************
		 *
		 */
		public static function waitForCompletion($process, $sleepTime=2)
		{
			KalturaLog::log("process($process), sleepTime($sleepTime)");
			while(1) {
				if(self::isProcessRunning($process)==false){
					break;
				}
				sleep($sleepTime);
			}
			KalturaLog::log("process($process)==>finished");
		}

		/********************
		 *
		 */
		public static function isProcessRunning($process)
		{
			if(file_exists( "/proc/$process" )){
				return true;
			}
			return false;
		}
		
		/********************
		 *
		 */
		public function parseLogFile($logFileName)
		{
			KalturaLog::log("logFileName:$logFileName");
			if(!kFile::checkFileExists($logFileName)) {
				KalturaLog::log("NOT FOUND $logFileName !!!");
				return;
			}

			$line = kFile::getFileContent($logFileName, 0,80);
			$flSz = kFile::fileSize($logFileName);
			$startFrom = $flSz<300? 0: $flSz-300;
			$logLines = kFile::getFileContent($logFileName, $startFrom, $flSz);
KalturaLog::log("logLinesBuffer - fileSize($flSz), startFrom($startFrom), loaded(".strlen($logLines)."), text - \n $logLines");
			$logLines.= (PHP_EOL.$line);
			$logLines = explode(PHP_EOL, $logLines);

			foreach($logLines as $line){
				if(strstr($line, "elapsed")!==false) {
					$tmpArr = explode(" ",$line);
					foreach($tmpArr as $tmpStr){
						if(($pos=strpos($tmpStr, "user"))!==false) {
							$this->user = substr($tmpStr,0,$pos);
						}
						else if(($pos=strpos($tmpStr, "system"))!==false) {
							$this->system = substr($tmpStr,0,$pos);
						}
						else if(($pos=strpos($tmpStr, "elapsed"))!==false) {
							$elapsed = substr($tmpStr,0,$pos);
							$tmpTmArr = array_reverse(explode(":",$elapsed));
							$this->elapsed = 0;
							foreach($tmpTmArr as $i=>$tm){
								$this->elapsed+= $tm*pow(60,$i);
							}
						}
						else if(($pos=strpos($tmpStr, "%CPU"))!==false) {
							$this->cpu = substr($tmpStr,0,$pos);
						}
					}
				}
				else if(strstr($line, "Started:")!==false){
					$this->startedAt = trim(substr($line,strlen("started:2017-03-15 ")));
				}
				else if(strstr($line, "exit_code:")!==false){
					$this->exitCode = trim(substr($line,strlen("exit_code:")));
				}
			}
		}
		
		/********************
		 *
		 */
		private static function readLastLines($fp, $length)
		{
			fseek($fp, -$length, SEEK_END);
			$lines=array();
			while(!feof($fp)){
				$line = fgets($fp);
				$lines[] = $line;
			}
			return $lines;
		}
	}

	/********************
	 * Session Report stat data
	 */
	class KChunkedEncodeSessionReportStats {
		public $num = 0;
		public $lasted = 0;
		public $elapsedCpu = 0;
		public $userCpu = 0;
		public $systemCpu = 0;
		public $concurrency = 0;
		public $concurrencyMax = 0;
		public $concurrencyMaxTime = 0;
		public $concurrencyIdleTime = 0;
		
		public function ToString()
		{
			return ("chunks($this->num),lasted:$this->lasted"."s,accum(elapsed:$this->elapsedCpu,user:$this->userCpu,system:$this->systemCpu),concurrency:$this->concurrency(max:$this->concurrencyMax,".$this->concurrencyMaxTime."s,idle:$this->concurrencyIdleTime"."s)");
		}
	}

