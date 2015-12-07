<?php
/**
 * This worker converts recorded live media files to MPEG-TS
 *
 * @package Scheduler
 * @subpackage Conversion
 */
class KAsyncConvertLiveSegment extends KJobHandlerWorker
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
		return KalturaBatchJobType::CONVERT_LIVE_SEGMENT;
	}
	
	public static function getType()
	{
		return KalturaBatchJobType::CONVERT_LIVE_SEGMENT;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::run()
	 */
	public function run($jobs = null)
	{
		// creates a temp file path
		$this->localTempPath = self::$taskConfig->params->localTempPath;
		$this->sharedTempPath = self::$taskConfig->params->sharedTempPath;
		
		$res = self::createDir($this->localTempPath);
		if(! $res)
		{
			KalturaLog::err("Cannot continue conversion without temp local directory");
			return null;
		}
		$res = self::createDir($this->sharedTempPath);
		if(! $res)
		{
			KalturaLog::err("Cannot continue conversion without temp shared directory");
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
		return $this->convert($job, $job->data);
	}
	
	protected function convert(KalturaBatchJob $job, KalturaConvertLiveSegmentJobData $data)
	{
		$this->updateJob($job, "File conversion started", KalturaBatchJobStatus::PROCESSING);
		$jobData = $job->data;
		
		$ffmpegBin = KBatchBase::$taskConfig->params->ffmpegCmd;
		$ffprobeBin = isset(KBatchBase::$taskConfig->params->ffprobeCmd)? KBatchBase::$taskConfig->params->ffprobeCmd: "ffprobe";
		$fileName = "{$job->entryId}_{$jobData->assetId}_{$data->mediaServerIndex}.{$job->id}.ts";
		$localTempFilePath = $this->localTempPath . DIRECTORY_SEPARATOR . $fileName;
		$sharedTempFilePath = $this->sharedTempPath . DIRECTORY_SEPARATOR . $fileName;
		
		$result = $this->convertRecordedToMPEGTS($ffmpegBin, $ffprobeBin, $data->srcFilePath, $localTempFilePath);
		if(! $result)
			return $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, null, "Failed to convert file", KalturaBatchJobStatus::FAILED);
		
		return $this->moveFile($job, $data, $localTempFilePath, $sharedTempFilePath);
	}
	
	/**
	 * @param KalturaBatchJob $job
	 * @param KalturaConcatJobData $data
	 * @param string $localTempFilePath
	 * @param string $sharedTempFilePath
	 * @return KalturaBatchJob
	 */
	protected function moveFile(KalturaBatchJob $job, KalturaConvertLiveSegmentJobData $data, $localTempFilePath, $sharedTempFilePath)
	{
		$this->updateJob($job, "Moving file from [$localTempFilePath] to [$sharedTempFilePath]", KalturaBatchJobStatus::MOVEFILE);
		
		kFile::moveFile($localTempFilePath, $sharedTempFilePath, true);
		clearstatcache();
		$fileSize = kFile::fileSize($sharedTempFilePath);
		
		$this->setFilePermissions($sharedTempFilePath);
		
		if(! $this->checkFileExists($sharedTempFilePath, $fileSize))
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, 'File not moved correctly', KalturaBatchJobStatus::RETRY);
		
		$data->destFilePath = $sharedTempFilePath;
		return $this->closeJob($job, null, null, 'Succesfully moved file', KalturaBatchJobStatus::FINISHED, $data);
	}
	
	protected function convertRecordedToMPEGTS($ffmpegBin, $ffprobeBin, $inFilename, $outFilename)
	{
		$cmdStr = "$ffmpegBin -i $inFilename -c copy -bsf:v h264_mp4toannexb -f mpegts -y $outFilename 2>&1";
		
		KalturaLog::debug("Executing [$cmdStr]");
		$output = system($cmdStr, $rv);

		/*
		 * Anomaly detection -
		*	Look for the time of the first KF in the source file.
		*	Should be less than 200 msec
		*	Currnetly - just logging
		*/
		$detectInterval = 10;		// sec
		$maxKeyFrameTime = 0.200;	// sec
		$kfArr=KFFMpegMediaParser::retrieveKeyFrames($ffprobeBin, $inFilename,0,$detectInterval);
		KalturaLog::log("KeyFrames:".print_r($kfArr,1));
		if(count($kfArr)==0){
			KalturaLog::log("Anomaly detection: NO Keyframes in the detection interval ($detectInterval sec)");
		}
		else if($kfArr[0]>$maxKeyFrameTime){
			KalturaLog::log("Anomaly detection: ERROR, first KF at ($kfArr[0] sec), max allowed ($maxKeyFrameTime sec)");
		}
		else {
			KalturaLog::log("Anomaly detection: OK, first KF at ($kfArr[0] sec), max allowed ($maxKeyFrameTime sec)");
		}
		
		return ($rv == 0) ? true : false;
	}
}