<?php
/**
 * This worker concatenate several files into a single file
 *
 * @package Scheduler
 * @subpackage Conversion
 */
class KAsyncConcat extends KJobHandlerWorker
{
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
		$fileName = $job->entryId . ".mp4";
		$localTempFilePath = $this->localTempPath . DIRECTORY_SEPARATOR . $fileName;
		$sharedTempFilePath = $this->sharedTempPath . DIRECTORY_SEPARATOR . $fileName;
		
		$srcFiles = array();
		foreach($data->srcFiles as $srcFile)
		{
			/* @var $srcFile KalturaString */
			$srcFiles[] = $srcFile->value;
		}
		
		$result = $this->concatFiles($ffmpegBin, $srcFiles, $localTempFilePath, $data->offset, $data->duration);
		if(! $result)
			return $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, null, "Failed to concat files", KalturaBatchJobStatus::FAILED);
		
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
		KalturaLog::debug("Moving file from [$localTempFilePath] to [$sharedTempFilePath]");
	
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
	 * @param string $ffmpegBin
	 * @param array $filesArr
	 * @param string $outFilename
	 * @param float $clipStart
	 * @param float $clipDuration
	 * @return boolean
	 */
	protected function concatFiles($ffmpegBin, array $filesArr, $outFilename, $clipStart = null, $clipDuration = null)
	{
		sort($filesArr);
		
		/*
		 * Check whether the input audio is AAC. If not - convert audio to AAC 
		 */
		$cmdStr = "$ffmpegBin -i $filesArr[0] 2>&1";
		$output = shell_exec($cmdStr);
		if(! isset($output))
			return false;
		$isAac = false;
		$str = stristr($output, "audio:");
		if($str != false)
		{
			$str = stristr($str, "aac");
			if($str != false)
				$isAac = true;
		}
		
		/*
		 * Calculate file bitrate. 
		 * It will be used for clipping in order to support precise clip start 
		 * and to preserve the source video quality
		 */
		$srcBr = null;
		$str = stristr($output, "duration:");
		if($str != false)
		{
			$str = substr($str, strlen("duration:"));
			$str = trim(substr($str, 0, strpos($str, ',')));
			$hours = $minutes = $seconds = null;
			sscanf($str, "%d:%d:%f", $hours, $minutes, $seconds);
			$dur = $hours * 3600 + $minutes * 60 + $seconds;
			$sz = filesize($filesArr[0]);
			$srcBr = round($sz * 8 / ($dur * 1024) * 1.2); // Increase the target br by 20% to compensate the expected conversion quality reduction 
		}
		
		/*
		 * Set ffmpeg clipping arguments
		 */
		$clipStr = null;
		if(isset($clipStart))
			$clipStr = "-ss $clipStart";
		if(isset($clipDuration))
			$clipStr .= " -t $clipDuration";
			
		if(isset($clipStr))
		{
			$videoParams = "libx264";
			if(isset($srcBr))
				$videoParams .= " -b:v $srcBr" . "k";
				
			$videoParams .= " -subq 7 -qcomp 0.6 -qmin 10 -qmax 50 -qdiff 4 -bf 16 -coder 1 -refs 6 -x264opts b-pyramid:weightb:mixed-refs:8x8dct:no-fast-pskip=0 -vprofile high  -pix_fmt yuv420p -threads 4";
		}
		else
			$videoParams = "copy";
		
		$concateStr = implode("|", $filesArr);
		$cmdStr = "$ffmpegBin -i concat:\"$concateStr\" -c:v $videoParams -bsf:a aac_adtstoasc";
		if($isAac)
			$cmdStr .= " -c:a copy";
		else
			$cmdStr .= " -c:a libfdk_aac";
		
		$cmdStr .= " $clipStr -f mp4 -y $outFilename 2>&1";
		
		KalturaLog::debug("Executing [$cmdStr]");
		$output = system($cmdStr, $rv);
		return ($rv == 0) ? true : false;
	}

}