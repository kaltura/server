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

		const	DefaultChunkDuration = 60;  // secs
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
				if($params->height>480)
					$this->chunkDuration = self::DefaultChunkDuration;
				else if($params->height>360)
					$this->chunkDuration = 2*self::DefaultChunkDuration;
				else
					$this->chunkDuration = 3*self::DefaultChunkDuration;
			}

			if($this->concurrentMin>$this->concurrent)
				$this->concurrentMin = $this->concurrent;
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
		
		public function __construct($chunkFileName=null, $ffprobeBin="ffprobe", $ffmpegBin="ffmpeg")
		{
			if(isset($chunkFileName)){
				$rv = $this->getData($chunkFileName, $ffprobeBin, $ffmpegBin);
				if(!isset($rv))
					return null;
			}
		}
		
		/********************
		 * getData
		 *	Retrieve following chunk stat data - 
		 */
		public function getData($chunkFileName, $ffprobeBin="ffprobe", $ffmpegBin="ffmpeg")
		{
			KChunkFramesStat::getData2($chunkFileName, $this, $ffprobeBin, $ffmpegBin);
		}
		
		/********************
		 * getData
		 *	Retrieve following chunk stat data - 
		 */
		protected static function getData1($chunkFileName, KChunkFramesStat $framesStat, $ffprobeBin="ffprobe", $ffmpegBin="ffmpeg")
		{
				/*
				 * Retrieve data for first frame and for last 10
				 */
			$cmdLine = "$ffprobeBin -select_streams v -show_frames -show_entries frame=coded_picture_number,pkt_pts_time,pict_type -print_format csv -v quiet $chunkFileName | (head -n1 && tail -n10)";
			KalturaLog::log($cmdLine);
			$lastLine=exec($cmdLine , $outputArr, $rv);
			if($rv!=0) {
				KalturaLog::log("ERROR: failed to extract frame data from chunk ($chunkFileName).");
				return null;
			}
			KalturaLog::log("rv($rv), output:\n".print_r($outputArr,1));
			$outputLine = array_shift($outputArr);
			list($stam,$pts,$type,$frame) = explode(",",$outputLine);
			$framesStat->start = $pts;
			
			$framesStat->frame = 0;
			$frame = 0;
			foreach($outputArr as $outputLine){
				list($stam,$pts,$type,$frame) = explode(",",$outputLine);
				if($framesStat->frame<$frame) {
					$framesStat->frame = $frame;
				}
			}
			$framesStat->frame++;
			$framesStat->finish = $pts;
			$framesStat->type = $type;
			
			$jsonStr = json_encode($framesStat);
			KalturaLog::log("$jsonStr");
			
			return $framesStat;
		}
		
		/********************
		 * getData
		 *	Retrieve following chunk stat data - 
		 */
		protected static function getData2($chunkFileName, KChunkFramesStat $framesStat, $ffprobeBin="ffprobe", $ffmpegBin="ffmpeg")
		{
			KalturaLog::log("$chunkFileName");
			$cmdLine = "$ffmpegBin -i $chunkFileName -c copy -f mp4 -v quiet -y $chunkFileName.mp4;$ffprobeBin -show_streams -select_streams v -v quiet -show_entries stream=duration,nb_frames -print_format csv $chunkFileName.mp4";
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
			$cmdLine = "$ffmpegBin -t 1 -i $chunkFileName -c:v copy -an -copyts -mpegts_copyts 1 -vsync 1 -f mpegts -y -v quiet - | $ffprobeBin -select_streams v -show_frames -show_entries frame=coded_picture_number,pkt_pts_time,pict_type -print_format csv -v quiet - | (head -n1)";
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
			$cmdLine = "$ffmpegBin -ss $startFrom -i $chunkFileName -c:v copy -an -copyts -mpegts_copyts 1 -vsync 1 -f mpegts -y -v quiet - | $ffprobeBin -f mpegts -select_streams v -show_frames -show_entries frame=coded_picture_number,pkt_pts_time,pict_type -print_format csv -v quiet - | tail";
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
			}
			KalturaLog::log("trimmed:output:\n".print_r($outputArr,1));

			$outputLine = array_shift($outputArr);
			list($stam,$pts,$type,$frame) = explode(",",$outputLine);
			$framesStat->start = $pts;

			$outputLine = end($outputArr);
			list($stam,$pts,$type,$frame) = explode(",",$outputLine);
			$framesStat->finish = $pts;
			$framesStat->type = $type;
			$framesStat->frame = $frames;
			
			$jsonStr = json_encode($framesStat);
			KalturaLog::log("$jsonStr");
			return $framesStat;
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
			if(!file_exists($logFileName))
				return;
			$fp = fopen($logFileName, 'r');
			if($fp==null)
				return;
			$line = fgets($fp);
			$logLines = null;
			$logLines = self::readLastLines($fp, 300);							
			$logLines[] = $line;
			fclose($fp);
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


