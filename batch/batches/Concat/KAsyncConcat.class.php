<?php
/**
 * This worker concatenate several files into a single file
 *
 * @package Scheduler
 * @subpackage Conversion
 */
class KAsyncConcat extends KJobHandlerWorker
{
	const LiveChunkDuration = 900000;	// msec (15*60*1000);
	const MaxChunkDelta 	= 150;		// msec
	
	/**
	 * @var string
	 */
	protected $localTempPath;
	
	/**
	 * @var string
	 */
	protected $sharedTempPath;
	
	/**
	 * (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	protected function getJobType()
	{
		return KalturaBatchJobType::CONCAT;
	}
	
	public static function getType()
	{
		return KalturaBatchJobType::CONCAT;
	}

	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::run()
	 */
	public function run($jobs = null)
	{
		// creates a temp file path
		$this->localTempPath = self::$taskConfig->params->localTempPath;
		$this->sharedTempPath = self::$taskConfig->params->sharedTempPath;
	
		$res = self::createDir( $this->localTempPath );
		if ( !$res )
		{
			KalturaLog::err( "Cannot continue conversion without temp local directory");
			return null;
		}
		$res = self::createDir( $this->sharedTempPath );
		if ( !$res )
		{
			KalturaLog::err( "Cannot continue conversion without temp shared directory");
			return null;
		}
		
		return parent::run($jobs);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->concat($job, $job->data);
	}

	protected function concat(KalturaBatchJob $job, KalturaConcatJobData $data)
	{
		$this->updateJob($job, "Files concatenation started", KalturaBatchJobStatus::PROCESSING);
		$jobData = $job->data;
		
		$ffmpegBin = KBatchBase::$taskConfig->params->ffmpegCmd;
		$ffprobeBin = isset(KBatchBase::$taskConfig->params->ffprobeCmd)? KBatchBase::$taskConfig->params->ffprobeCmd: "ffprobe";
		$mediaInfoBin = isset(KBatchBase::$taskConfig->params->mediaInfoCmd)? KBatchBase::$taskConfig->params->mediaInfoCmd: "mediainfo";
		$fileName = "{$job->entryId}_{$data->flavorAssetId}.mp4";
		$localTempFilePath = $this->localTempPath . DIRECTORY_SEPARATOR . $fileName;
		$sharedTempFilePath = $this->sharedTempPath . DIRECTORY_SEPARATOR . $fileName;
		
		$srcFiles = array();
		foreach($data->srcFiles as $srcFile)
		{
			/* @var $srcFile KalturaString */
			$srcFiles[] = $srcFile->value;
		}
		
		$result = $this->concatFiles($ffmpegBin, $ffprobeBin, $srcFiles, $localTempFilePath, $data->offset, $data->duration,$data->shouldSort);
		if(! $result)
			return $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, null, "Failed to concat files", KalturaBatchJobStatus::FAILED);

		try
		{
			// get the concatenated duration
			$mediaInfoParser = new KMediaInfoMediaParser($localTempFilePath, $mediaInfoBin);
			$data->concatenatedDuration = $mediaInfoParser->getMediaInfo()->videoDuration;
		}
		catch(Exception $ex)
		{
			KalturaLog::warning('failed to get concatenatedDuration ' . print_r($ex));
		}

		return $this->moveFile($job, $data, $localTempFilePath, $sharedTempFilePath);
	}

	/**
	 * @param KalturaBatchJob $job
	 * @param KalturaConcatJobData $data
	 * @param string $localTempFilePath
	 * @param string $sharedTempFilePath
	 * @return KalturaBatchJob
	 */
	protected function moveFile(KalturaBatchJob $job, KalturaConcatJobData $data, $localTempFilePath, $sharedTempFilePath)
	{
		$this->updateJob($job, "Moving file from [$localTempFilePath] to [$sharedTempFilePath]", KalturaBatchJobStatus::MOVEFILE);
		
		kFile::moveFile($localTempFilePath, $sharedTempFilePath, true);
		clearstatcache();
		$fileSize = kFile::fileSize($sharedTempFilePath);

		$this->setFilePermissions($sharedTempFilePath);

		if(!$this->checkFileExists($sharedTempFilePath, $fileSize))
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, 'File not moved correctly', KalturaBatchJobStatus::RETRY);
			
		$data->destFilePath = $sharedTempFilePath;
		return $this->closeJob($job, null, null, 'Succesfully moved file', KalturaBatchJobStatus::FINISHED, $data);
	}

	/**
	 *
	 * @param unknown_type $ffmpegBin
	 * @param unknown_type $ffprobeBin
	 * @param array $filesArr
	 * @param unknown_type $outFilename
	 * @param unknown_type $clipStart
	 * @param unknown_type $clipDuration
	 * @param bool $shouldSort
	 * @return boolean
	 */
	protected static function concatFiles($ffmpegBin, $ffprobeBin, array $filesArr, $outFilename, $clipStart = null, $clipDuration = null, $shouldSort = true)
	{
		$fixLargeDeltaFlag = null;
		$chunkBr = null;
		$concateStr = null;
	
		/*
		 * Evaluate clipping arguments
		 */
		$clipStr = null;
		if(isset($clipStart))
			$clipStr = "-ss $clipStart";
		if(isset($clipDuration))
			$clipStr.= " -t $clipDuration";
		if ($shouldSort)
		{
			sort($filesArr);
		}
		$filesArrCnt = count($filesArr);
		$i=0;
		$mi = null;
		foreach($filesArr as $fileName) {
			$i++;
				/*
				 * Get chunk file media-info
				 */
			$ffParser = new KFFMpegMediaParser($fileName, $ffmpegBin, $ffprobeBin);
			$mi = null;
			try {
				$mi = $ffParser->getMediaInfo();
			}
			catch(Exception $ex) {
				KalturaLog::log(print_r($ex,1));
			}
				/*
				 * Calculate chunk-br for the cliping flow
				 */
			if(isset($clipStr)) {
				if(isset($mi->containerBitRate) && $mi->containerBitRate>0)
					$chunkBr+= $mi->containerBitRate;
				else if(isset($mi->videoBitRate) && $mi->videoBitRate>0)
					$chunkBr+= $mi->videoBitRate;
				else if(isset($mi->audioBitRate) && $mi->audioBitRate>0)
					$chunkBr+= $mi->audioBitRate;
			}
	
				/*
				 * On last chunk file - 
				 * - no duration delta validity tests
				 * - pack the final concat string and finish the loop
				 */
			if($i==$filesArrCnt){
				$concateStr = "concat:\"$concateStr$fileName\"";
				break;
			}
			else {
				$concateStr.= "$fileName|";
			}
			
				/* 
				 * ##############
				 * ############## DISABLE the 'small-distortion-fix' code
				 * ############## There were cases when the the 'fix' cuased another distortion
				 * ############## Hopefully WWZ fixed their chunks generation procedure in 4.1.2
				 * ############## If it does - all this code/remark should be removed,
				 * ############## otherwise (in case that the fix will still be required) - it should be enhanced.
				 * ############## 
				 * Evaluate chunk duration for drift validation
				 * - only one duration anomaly is required to set the drift fix flag, no need to check following chunk files
				 *
			if(!isset($fixLargeDeltaFlag)) {
				if(isset($mi->containerDuration) && $mi->containerDuration>0)
					$duration = $mi->containerDuration;
				else if(isset($mi->videoDuration) && $mi->videoDuration>0)
					$duration = $mi->videoDuration;
				else if(isset($mi->audioDuration) && $mi->audioDuration>0)
					$duration = $mi->audioDuration;
				else
					$duration = 0;

				if($duration>0){
					 *
					 * If the duration is too small - stop/start flow, don't fix 
					 *
					if(KAsyncConcat::LiveChunkDuration-$duration>30000){
						$fixLargeDeltaFlag = false;
					}
					else if(abs($duration-KAsyncConcat::LiveChunkDuration)>KAsyncConcat::MaxChunkDelta){
						$fixLargeDeltaFlag = true;
					}
				}
				KalturaLog::log("Chunk duration($duration), Wowza chunk setting(".KAsyncConcat::LiveChunkDuration."),max-allowed-delta(".KAsyncConcat::MaxChunkDelta."),fixLargeDeltaFlag($fixLargeDeltaFlag) ");
			}
				*/
		}
	
			/*
			 * For clip flow - set converion to x264,
			 * otherwise - just copy video
			 */
		if(isset($clipStr))	{
			$videoParamStr = "-c:v libx264";
			if(isset($chunkBr) && $chunkBr>0 && $filesArrCnt>0) {
				$chunkBr = round($chunkBr/$filesArrCnt);
				$videoParamStr.= " -b:v $chunkBr" . "k";
			}
			$videoParamStr.= " -subq 7 -qcomp 0.6 -qmin 10 -qmax 50 -qdiff 4 -bf 16 -coder 1 -refs 6 -x264opts b-pyramid:weightb:mixed-refs:8x8dct:no-fast-pskip=0 -vprofile high  -pix_fmt yuv420p -threads 4";
		}
		else
			$videoParamStr = "-c:v copy";
		
		if (isset($mi->videoFormat) || isset($mi->videoCodecId) || isset($mi->videoDuration))
			$videoParamStr.= " -map v ";

			/*
			 * If no audio - skip.
			 * For AAC source - copy audio,
			 * otherwise - convert to AAC
			 */
		$audioParamStr = null;
		if(isset($mi->audioFormat) || isset($mi->audioCodecId) || isset($mi->audioDuration)) {
			if(isset($mi->audioFormat) && $mi->audioFormat=="aac")
				$audioParamStr = "-c:a copy";
			else
				$audioParamStr = "-c:a libfdk_aac";
			$audioParamStr.= " -bsf:a aac_adtstoasc";
			$audioParamStr.= " -map a ";
		}
	
			/*
			 * ##############
			 * ############## DISABLE the 'small-distortion-fix' code, see above
			 * ##############
			 * For fix-durtion-delta flow - split the input concat to separate video and audio streams,
			 * otherwise - normal single input
			 *
		if($fixLargeDeltaFlag && $audioParamStr) {
			KalturaLog::log("Will attempt to fix the audio-video drift ");
			$cmdStr = "$ffmpegBin -probesize 15M -analyzeduration 25M -i $concateStr -probesize 15M -analyzeduration 25M -i $concateStr";
			$cmdStr.= " -map 0:v -map 1:a $videoParamStr $audioParamStr";
		}
		else */
		{
			$cmdStr = "$ffmpegBin -probesize 15M -analyzeduration 25M -i $concateStr $videoParamStr $audioParamStr";
		}
		$cmdStr .= " $clipStr -f mp4 -y $outFilename 2>&1";
	
		KalturaLog::debug("Executing [$cmdStr]");
		$output = system($cmdStr, $rv);
		return ($rv == 0) ? true : false;
	}
}
