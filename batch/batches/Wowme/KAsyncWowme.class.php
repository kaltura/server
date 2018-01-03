<?php
/**
 * WOOOOOOWWWWWWWWW
 *
 * @package Scheduler
 * @subpackage Wowme
 */
class KAsyncWowme extends KJobHandlerWorker
{

	const DIFF_THRESHOLD = 20;
	const HIGHLIGHT_DURATION = 5;
	const HIGHLIGHT_PREV = 0;
	const HIGHLIGHT_POST = 10;
	const IS_PROD = false;

	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::WOWME;
	}


	/* (non-PHPdoc)
 	* @see KJobHandlerWorker::exec()
 	*/
	protected function exec(KalturaBatchJob $job)
	{
		$jobData = $job->data;
		/**
		 * @var KalturaWowmeJobData $jobData
		 */
		$highlightsVideoPath = null;
		switch ($jobData->highlightType)
		{
			case KalturaHighlightType::ACTION:
			case KalturaHighlightType::DRAMA:
			case KalturaHighlightType::LECTURE:
			case KalturaHighlightType::SPORTS:
				$highlightsVideoPath = $this->createActionHighlights($job->entryId, $jobData, $job);
				break;
			default:
				KalturaLog::err("Job type [" . $jobData->highlightType . "] not implemented yet");
				return $this->updateJob($job, "Failed to wow you", KalturaBatchJobStatus::FAILED, null);
		}

		$resource = new KalturaServerFileResource();
		$resource->localFilePath = $highlightsVideoPath;
		$resource->keepOriginalFile = 0;

		self::impersonate($job->partnerId);
		self::$kClient->media->addContent($jobData->outEntryId, $resource);
		self::unimpersonate();
		return $this->updateJob($job, "So long and thanks for all the fish", KalturaBatchJobStatus::FINISHED, null);
	}

	protected function createActionHighlights($entryId, KalturaWowmeJobData $jobData, $job)
	{
		$volumeMapUrl = self::$kClient->media->getVolumeMap($entryId);
		$volumeMapStr = KCurlWrapper::getContent($volumeMapUrl);
		$volumeMap = preg_split('/\n/', $volumeMapStr);
		$output = $this->findHighlightPTSs($volumeMap);
		KalturaLog::debug("@@NA volume Map [" . print_r($volumeMapStr, true). "]");
		KalturaLog::debug("@@NA highlights at [" . print_r($output, true). "]");
		$videoLocations = $this->cutVideosAtLocation($output, $jobData->fileSyncPath);

		$this->createAndUploadGifs($entryId, $videoLocations, $job->partnerId);

		$fileListPath = tempnam("/opt/kaltura/tmp/", 'filelist');
//		chmod($fileListPath,777);
		$fileList = fopen($fileListPath, "w");
		foreach ($videoLocations as $videoLocation)
		{
			KalturaLog::debug("Writing video location [$videoLocation] to filelist");
			fwrite($fileList, "file '".$videoLocation."'\n");
		}
		fclose($fileList);
		$stitchedVideoPath = $this->concatVideos($fileListPath);
		chmod('/tmp/stitchedJcw60n.mp4', 0666);
		return $stitchedVideoPath;
	}

	protected function cutVideosAtLocation($highlightTimesMs, $srcFilePath)
	{
		$videoLocations = array();
		foreach ($highlightTimesMs as $ptsMs)
		{
			$currFilePath = tempnam("/opt/kaltura/tmp/", 'pts');
			kFile::moveFile($currFilePath, $currFilePath.".mp4");
			$currFilePath = $currFilePath.".mp4";
			$pts = floor((float)$ptsMs[0] / 1000) - $ptsMs[1];
			try
			{
				$cmd = 'ffmpeg -ss ' . $pts . ' -i ' . $srcFilePath . ' -c copy -f mp4 -t ' . $ptsMs[1] . ' -y ' . $currFilePath;
				if (self::IS_PROD)
					$cmd = 'ffmpeg -ss ' . $pts . ' -i ' . $srcFilePath . ' -c:v libx264 -f mp4 -t ' . $ptsMs[1] . ' -y ' . $currFilePath;
				KalturaLog::debug("running cmd [$cmd]");
				$output = shell_exec($cmd);
			}
			catch (Exception $e)
			{
				KalturaLog::err("Caught exception [". print_r($e, true)."]");
				return array();
			}

			$videoLocations[] = $currFilePath;
		}
		KalturaLog::debug("returning video locations [".print_r($videoLocations,true)."]");
		return $videoLocations;
	}

	protected function concatVideos($fileListPath)
	{
//		ffmpeg -f concat -safe 0 -i filelist.txt -q:a 0 -c copy output.mp4
		$stitchedFilePath = tempnam('/opt/kaltura/tmp/','stitched');
		kFile::moveFile($stitchedFilePath, $stitchedFilePath.".mp4");
		$stitchedFilePath = $stitchedFilePath.".mp4";
		$cmd = 'ffmpeg -f concat -safe 0 -i ' . $fileListPath . ' -q:a 0 -c copy -y ' . $stitchedFilePath;
		KalturaLog::debug("running cmd [$cmd]");
		$output = shell_exec($cmd);
		return $stitchedFilePath;
	}

	/**
	 * @param $volumeMap
	 * @return array
	 */
	protected function findHighlightPTSs($volumeMap)
	{
		$highlightsPTSs = array();
		$previousVol = preg_split('/,/', $volumeMap[1]);
		for ($i = 2; $i < count($volumeMap); $i++)
		{
			$currVolume = preg_split('/,/', $volumeMap[$i]);
			if (count($currVolume) != 2)
				continue;
//			if (abs((float)substr($previousVol[1], 1) - (float)substr($currVolume[1], 1)) > self::DIFF_THRESHOLD)
			$prevVol = abs((float)$previousVol[1]);
			$currVol = abs((float)$currVolume[1]);
			if ((($prevVol - $currVol) >= self::DIFF_THRESHOLD && $prevVol < 50) ||
				(($prevVol - $currVol ) >= 7 && $currVol < 25 ) )
			{
				$highlightsPTSs[] = $currVolume[0];
			}
			$previousVol = $currVolume;
		}

		$output = array();
		for ($i = 0; $i < count($highlightsPTSs) - 1; $i++)
		{
			$length = self::HIGHLIGHT_DURATION;
			if (($highlightsPTSs[$i] + (1000 * $length)) >= $highlightsPTSs[$i + 1])
			{
				$length = floor(($highlightsPTSs[$i + 1] - $highlightsPTSs[$i]) / 1000);
				if ($length === 0)
					$length = 1;
			}
			$output[] = array(max(0,$highlightsPTSs[$i] - self::HIGHLIGHT_PREV), $length + self::HIGHLIGHT_POST);
		}
		$output[] = array($highlightsPTSs[count($highlightsPTSs) - 1], self::HIGHLIGHT_DURATION);
		return $output;
	}

	protected function createAndUploadGifs($entryId, $videoLocations, $partnerId)
	{
		$firstThumb = null;
		self::impersonate($partnerId);
		foreach ($videoLocations as $videoLocation)
		{
			KalturaLog::debug("creating gif for [" . $videoLocation . "]");
			$createPalletteCommand = "ffmpeg -y -i " . $videoLocation . " -vf fps=10,scale=320:-1:flags=lanczos,palettegen -y /opt/kaltura/tmp/palette.png";
			shell_exec($createPalletteCommand);
			$creatGifCommand = "ffmpeg -i " . $videoLocation ." -i /opt/kaltura/tmp/palette.png -filter_complex \"fps=10,scale=320:-1:flags=lanczos[x];[x][1:v]paletteuse\" -y /opt/kaltura/tmp/output.gif";
			shell_exec($creatGifCommand);
//			$fileData = file_get_contents('/opt/kaltura/tmp/output.gif', "b" );
			$currThumb = self::$kClient->thumbAsset->addFromImage($entryId, '/opt/kaltura/tmp/output.gif');
			if (!$firstThumb)
				$firstThumb = $currThumb;
		}
		self::$kClient->thumbAsset->setAsDefault($firstThumb->id);
		self::unimpersonate();
	}
}
