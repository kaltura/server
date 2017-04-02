<?php

		/********************
		 *
		 */
	class KMediaComplexityStatistics {
		public $complexityValue = 0;
		public $renditionData = null;
			
		public $startedAt = null;
		public $finishedAt = null;
			
		public $frames = null;
	}

		/********************
		 *
		 */
	class KMediaComplexityFramesData {
		public $cnt  = 0;
		public $size = 0;
	}
	
	/**
	 * 
	 */
	class KMediaFileComplexity {
		/**
		 * 
		 * @param unknown_type $ffmpegBin
		 * @param unknown_type $ffprobeBin
		 * @param unknown_type $mediaInfoBin
		 */
		public function __construct($ffmpegBin="ffmpeg", $ffprobeBin="ffprobe", $mediaInfoBin="mediainfo") {
			$this->ffmpegBin = isset($ffmpegBin)? $ffmpegBin: "ffmpeg";
			$this->ffprobeBin = isset($ffprobeBin)? $ffprobeBin: "ffprobe";
			$this->mediaInfoBin = isset($mediaInfoBin)? $mediaInfoBin: "mediaInfo";
		}

		const DEFAULT_SAMPLING_POINTS_NUM = 20;
		const MINIMAL_SAMPLING_STEP_INTERVAL = 2; 	//secs, interval between sampling points
		const DEFAULT_SINGLE_SAMPLING_POINT_DUR = 1;//secs, sampling time on each sampling point

		protected $ffmpegBin = null;
		protected $ffprobeBin = null;
		protected $mediaInfoBin = null;
		
		protected $start = null;
		protected $duration = null;
		
		protected $width = null;
		protected $height = null;
		protected $fps = null;
		protected $scanType = null;
		
		protected $samplingPointDuration = null;
		protected $samplingPoints = null;
		
		/**
		 * 
		 * @param unknown_type $start
		 * @param unknown_type $duration
		 */
		public function SetTimes($start, $duration) {
			$this->start = $start;
			$this->duration = $duration;
		}
		
		/**
		 * 
		 * @param unknown_type $width
		 * @param unknown_type $height
		 * @param unknown_type $fps
		 * @param unknown_type $scanType
		 */
		public function SetMediaParams($width, $height, $fps, $scanType){
			$this->width = $width;
			$this->height = $height;
			$this->fps = $fps;
			$this->scanType = $scanType;
		}

		/**
		 * 
		 * @param unknown_type $duration
		 * @param unknown_type $points
		 */
		public function SetSampling($duration, $points) {
			$this->samplingPointDuration = $duration;
			$this->samplingPoints = $points;
		}
		

		/**
		 * Complexity evaluation vis full/normal conversion (CRF) 
		 * 
		 * @param unknown_type $sourceFilename
		 * @param unknown_type $complexityFilename
		 * @param unknown_type $start
		 * @param unknown_type $duration
		 * @return NULL|KMediaComplexityStatistics
		 */
		public function Evaluate($sourceFilename, $complexityFilename, $start=null, $duration=null)
		{
			KalturaLog::log("sourceFilename($sourceFilename), complexityFilename($complexityFilename), start($start), duration($duration)");
			$logFilename = pathinfo($complexityFilename, PATHINFO_DIRNAME).'/'.pathinfo($complexityFilename, PATHINFO_FILENAME)."_printout.log";

			$startedAt = time();

			$cmdLine = $this->buildCmdLine($sourceFilename, $complexityFilename, $start, $duration);
			$cmdLine.= " -loglevel debug > $logFilename 2>&1 ";
			
			KalturaLog::log($cmdLine);

			$lastLine=exec($cmdLine , $outputArr, $rv);
			if($rv!=0) {
				KalturaLog::log("ERROR: failed to execute Complexity test.");
				return null;
			}
			$stat = new KMediaComplexityStatistics();
			
			$medPrsr = new KMediaInfoMediaParser($complexityFilename, $this->mediaInfoBin, $this->ffmpegBin, $this->ffprobeBin);
			$stat->renditionData = $medPrsr->getMediaInfo();

			$stat->complexityValue = $stat->renditionData->videoBitRate;

			$framesStat = self::parsePrintout($logFilename);
			if(isset($framesStat))
				$stat->frames = $framesStat;
			
			$stat->startedAt = $startedAt;
			$stat->finishedAt = time();

			KalturaLog::log(print_r($stat,1));
			KalturaLog::log("Complexity Results: bitrate($stat->complexityValue),rendition($complexityFilename),time(".($stat->finishedAt-$stat->startedAt).")");

			return $stat;
		}
		
		/**
		 * Complexity evalution in sampled mode.
		 * 
		 * @param unknown_type $sourceFilename
		 * @param unknown_type $sourceData
		 * @param unknown_type $complexityFilename
		 * @param unknown_type $start
		 * @param unknown_type $duration
		 * @return Ambigous <NULL, KMediaComplexityStatistics>
		 */
		public function EvaluateSampled($sourceFilename, $sourceData, $complexityFilename, $start=null, $duration=null)
		{
			KalturaLog::log("sourceFilename($sourceFilename), complexityFilename($complexityFilename), start($start), duration($duration)");

				/*
				 * Determine the sampling start time and duration
				 */
			 {
				if(!isset($start)){
					if(!isset($this->start) || $this->start>$sourceData->videoDuration/1000)
						$start = 0;
					else 
						$start = $this->start;
				}
				if(!isset($duration)){
					if(!isset($this->duration) || $this->duration>$sourceData->videoDuration/1000)
						$duration = $sourceData->videoDuration/1000;
					else
						$duration = $this->duration;
				}
			}
			
			if(!isset($this->scanType))
				$this->scanType = $sourceData->scanType;

				/*
				 * Limit the complexity evaluation FPS to 'reasonable' values,
				 * in order to avoid processing overloading in cases with huge/invalid FPS values.
				 */
			if(!isset($sourceData->videoFrameRate) || $sourceData->videoFrameRate>KDLSanityLimits::MaxFramerate){
				$this->fps = KDLSanityLimits::MaxFramerate;
			}
			else if($sourceData->videoFrameRate==0){
				$this->fps = KDLConstants::MaxFramerate;
			}
			
				/*
				 * Determine the sampling paramters
				 * - number of sumpling points
				 * - step interval (between the points)
				 * - test duration on each point (samplingPointDuration)
				 */
			{
				if(isset($this->samplingPoints)) 
					$samplingPoints = $this->samplingPoints;
				else 
					$samplingPoints = self::DEFAULT_SAMPLING_POINTS_NUM; // 20
				
				$stepInterval = round($duration/$samplingPoints,3);
				if($stepInterval<self::MINIMAL_SAMPLING_STEP_INTERVAL)
					$stepInterval = self::MINIMAL_SAMPLING_STEP_INTERVAL;

				if(isset($this->samplingPointDuration)) $samplingPointDuration = $this->samplingPointDuration;
				else $samplingPointDuration = self::DEFAULT_SINGLE_SAMPLING_POINT_DUR;
			}
			
			$diff = 0;
			$framesStat = null;

				/*
				 * Sampling loop
				 */
			$startedAt = time();
			for($pointsCnt=0,$sampleStart=$start;$sampleStart<$this->start+$duration; $sampleStart+=$stepInterval,$pointsCnt++) {
				$stat = $this->Evaluate($sourceFilename, $complexityFilename, $sampleStart, $samplingPointDuration);
				if(!isset($stat))
					continue;

				$psnrStat = self::measureVideoQuality($sourceFilename, $sourceData, $complexityFilename, $sampleStart, $samplingPointDuration);
				if(isset($framesStat)) { 
					$framesStat->num+= ($stat->frames->num+1);
					$framesStat->I->size+= $stat->frames->I->size;
					$framesStat->I->cnt += $stat->frames->I->cnt;
					$framesStat->P->size+= $stat->frames->P->size;
					$framesStat->P->cnt += $stat->frames->P->cnt;
					$framesStat->B->size+= $stat->frames->B->size;
					$framesStat->B->cnt += $stat->frames->B->cnt;
					if(isset($psnrStat)){
						$framesStat->y += $psnrStat->y;
						$framesStat->avg += $psnrStat->avg;
						$framesStat->cnt += $psnrStat->cnt;
					}
				}
				else {
					$framesStat = $stat->frames;
					if(isset($psnrStat)){
						$framesStat->y = $psnrStat->y;
						$framesStat->avg = $psnrStat->avg;
						$framesStat->cnt = $psnrStat->cnt;
					}
				}
				$diff+= ($stat->finishedAt - $stat->startedAt);
			}
			$finishedAt = time();

			KalturaLog::log("Frame types stat:".print_r($framesStat,1));
			$stat->complexityValue = self::estimateBitrate(60, 60*3, $framesStat, $sourceData->videoFrameRate);
			if(isset($framesStat->y)){
				$stat->y = round($framesStat->y/$pointsCnt,6);
				$stat->avg = round($framesStat->avg/$pointsCnt,6);
				$stat->cnt = round($framesStat->cnt/$pointsCnt,6);
			}
			
			$stat->startedAt = $startedAt;
			$stat->finishedAt = $finishedAt;
			
			KalturaLog::log("ComplexitySampled: source(br:".($sourceData->videoBitRate).",h:".$sourceData->videoHeight.",".$sourceFilename.")");
			$msgStr = "ComplexitySampled: Result - bitrate(".$stat->complexityValue."),dur($duration),sampling(points:$pointsCnt,stepInterval:$stepInterval,samplingDur:$samplingPointDuration)";
			if(isset($stat->y))
				$msgStr.= ",psnr(y:$stat->y,avg:$stat->avg,cnt:$stat->cnt)";
			$msgStr.= ",exec.time($diff)";
			KalturaLog::log($msgStr);
			return $stat;
		}

		/**
		 * 
		 * @param unknown_type $estimateDuration
		 * @param unknown_type $estimatedKeyFrameCount
		 * @param unknown_type $framesStat
		 * @param unknown_type $fps
		 * @return number
		 */
		protected static function estimateBitrate($estimateDuration, $estimatedKeyFrameCount, $framesStat, $fps)
		{
			/*
			 * estimateDuration
			 * estimatedKeyFrameCount - I frame count that shoudl represent both the ForcedKF's and scenecut flavors. Typically - duration_in_sec * 3
			 */
			KalturaLog::log("estimateDuration($estimateDuration), estimatedKeyFrameCount($estimatedKeyFrameCount), fps($fps), frameStat:".print_r($framesStat,1));
			
			$estimatedSize = ($framesStat->I->size/$framesStat->I->cnt)*$estimatedKeyFrameCount;
			$p2bRatio = ($framesStat->P->size + $framesStat->B->size)/($framesStat->P->cnt + $framesStat->B->cnt);
			$estimatedSize+= ($estimateDuration*$fps-$estimatedKeyFrameCount)*$p2bRatio;
			$complexityValue = round($estimatedSize*8/$estimateDuration/1024);

			KalturaLog::log("complexityValue($complexityValue), (fps:$fps,estimatedKeyFrameCount:$estimatedKeyFrameCount,p2bRatio:$p2bRatio,estimatedSize:$estimatedSize)");
			return $complexityValue;
		}
		
		/**
		 * 
		 * @param unknown_type $sourceFilename
		 * @param unknown_type $outputFilename
		 * @param unknown_type $start
		 * @param unknown_type $duration
		 * @return string
		 */
		protected function buildCmdLine($sourceFilename, $outputFilename, $start=null, $duration=null)
		{
			KalturaLog::log("sourceFilename($sourceFilename), outputFilename($outputFilename), start($start), duration($duration)");
			$ffmpegBin = $this->ffmpegBin;
			
			if(!isset($start)) 	  $start = $this->start;
			if(!isset($duration)) $duration = $this->duration;

			$width = $this->width;
			$height = $this->height;
			$fps = $this->fps;
			$scanType = $this->scanType;

			$cmdLine = $ffmpegBin;
			if(isset($start)) $cmdLine.= " -ss $start";
			$cmdLine.= " -i $sourceFilename -c:v libx264 -crf 23 -force_key_frames expr:'gte(t,n_forced*2)' -bf 0";
			$filterStr = null;
			if(isset($scanType) && $scanType>0)
				$filterStr = "yadif";
			if(isset($width))
				$scaleStr = $width;
			if(isset($height))
				$scaleStr = isset($scaleStr)? "$scaleStr:$width" : "-1:$height";
			else if(isset($scaleStr))
				$scaleStr.= ":-1";
			if(isset($scaleStr)) {
				if(isset($filterStr)) $filterStr.= ",scale=$scaleStr";
				else $filterStr.= "scale=$scaleStr";
			}
			if(isset($filterStr))
				$cmdLine.= " -filter_complex '$filterStr'";
			if(isset($fps)) $cmdLine.= " -r $fps";
			if(isset($duration)) $cmdLine.= " -t $duration";
			$cmdLine.= " -f mp4 -threads 4 -y $outputFilename";
			KalturaLog::log($cmdLine);
			return $cmdLine;
		}
		
		/**
		 * 
		 * @param unknown_type $fileName
		 * @return NULL|stdClass
		 */
		protected static function parsePrintout($fileName)
		{
			KalturaLog::log("fileName($fileName)");
			$fHd = fopen($fileName, "r");
			if(!isset($fHd))
				return null;
			
			$framesStat = new stdClass();
			$framesStat->num = 0;
			$framesStat->I = new KMediaComplexityFramesData();
			$framesStat->P = new KMediaComplexityFramesData();
			$framesStat->B = new KMediaComplexityFramesData();

			while(1){
				if(($line=fgets($fHd))==false)
					break;
				$line = strstr($line,"frame= ");
				if($line==false)
					continue;
	//		sscanf($line,"PSNR y:%s u:%s v:%s average:%s min:%s max:%s", &$yVal, &$uVal, &$vVal, &$avgVal, &$minVal, &$maxVal);
	// [libx264 @ 0x1ea33a0] frame=  44 QP=25.33 NAL=2 Slice:P Poc:88  I:501  P:881  SKIP:238  size=17499 bytes
				sscanf($line, "frame=  %d QP=%g NAL=%d Slice:%s Poc:%d  I:%d  P:%d  SKIP:%d  size=%d ", $framesStat->num, $qp, $nal, $slice, $poc, $i, $p, $skip, $size);
				KalturaLog::log($line);
				KalturaLog::log("$framesStat->num,$qp,$nal,$slice,$poc, $i, $p, $skip, $size");
				switch($slice){
				case "I":
					$framesStat->I->size+= $size;
					$framesStat->I->cnt++;
					break;
				case "P":
					$framesStat->P->size+= $size;
					$framesStat->P->cnt++;
					break;
				case "B":
					$framesStat->B->size+= $size;
					$framesStat->B->cnt++;
					break;
				}
			}
			fclose($fHd);
			return $framesStat;
		}
		
		/**
		 * 
		 * @param unknown_type $sourceFilename
		 * @param unknown_type $sourceData
		 * @param unknown_type $complexityFilename
		 * @param unknown_type $sampleStart
		 * @param unknown_type $samplingPointDuration
		 */
		protected static function measureVideoQuality($sourceFilename, $sourceData, $complexityFilename, $sampleStart, $samplingPointDuration)
		{
			// place holder till we'll be able to measure PSNR/SSIM in production 
			return null;
		}
	};

