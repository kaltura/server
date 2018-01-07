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
	const HIGHLIGHT_PREV = 2000;
	const HIGHLIGHT_POST = 10;
	const IS_PROD = false;

	const DRAGON_PREV = 5;

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
			case KalturaHighlightType::DRAGON:
				$highlightsVideoPath = $this->createDragonHighlights($job);
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

		$this->createAndUploadGifs($jobData->outEntryId, $videoLocations, $job->partnerId);

		$fileListPath = tempnam("/opt/kaltura/tmp/", 'filelist');
		$fileList = fopen($fileListPath, "w");
		foreach ($videoLocations as $videoLocation)
		{
			KalturaLog::debug("Writing video location [$videoLocation] to filelist");
			fwrite($fileList, "file '".$videoLocation."'\n");
		}
		fclose($fileList);
		$stitchedVideoPath = $this->concatVideos($fileListPath);
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

	protected function createDragonHighlights(KalturaBatchJob $job)
	{
		$jobData = $job->data;
		$output = $this->findDragonHighlightsTimes($jobData->fileSyncPath);

		$videoLocations = $this->cutVideosAtLocation($output, $jobData->fileSyncPath);

//		$this->createAndUploadGifs($jobData->outEntryId, $videoLocations, $job->partnerId);

		$fileListPath = tempnam("/opt/kaltura/tmp/", 'filelist');
		$fileList = fopen($fileListPath, "w");
		foreach ($videoLocations as $videoLocation)
		{
			KalturaLog::debug("Writing video location [$videoLocation] to filelist");
			fwrite($fileList, "file '".$videoLocation."'\n");
		}
		fclose($fileList);
		$stitchedVideoPath = $this->concatVideos($fileListPath);
		return $stitchedVideoPath;
	}


	/**
	 * @param $fileSync
	 * return array(array(time in ms, duration is s))
	 */
	protected function findDragonHighlightsTimes($fileSync)
	{
		$output = array();
		$dragonImgsDir = '/opt/kaltura/tmp/dragonImgs';
//		$files = array_diff(scandir($dragonImgsDir), array('.','..'));
//		foreach ($files as $file) {
//			@unlink("$dragonImgsDir/$file");
//		}
//		$createImgsCommands = "ffmpeg -i " . $fileSync . " -vf fps=2 -f image2 -r 0.5 -y " . $dragonImgsDir ."/%d.jpg";
//		KalturaLog::debug("Running command to create dragon images [".$createImgsCommands."]");
//		shell_exec($createImgsCommands);



//		$dragonFiles = array_diff(scandir($dragonImgsDir), array('.','..'));
//		$dragonImages = array();
//		foreach ($dragonFiles as $imageFile)
//		{
//			if ($this->isDragonImage("$dragonImgsDir/$imageFile", $imageFile))
//			{
//				$dragonImages[] = $imageFile;
//				KalturaLog::debug("File [".$imageFile."] is dragon!");
//			}
//		}
//
//		KalturaLog::debug("Dragon images [".print_r($dragonImages,true)."]");
		$dragonImages = $this->getPreComputedDragonResults();
		foreach ($dragonImages as $dragonImg)
		{
			$startTime = (int)substr($dragonImg, 0, strpos($dragonImg, ".jpg"));
			$startTime = ($startTime - self::DRAGON_PREV) * 1000;
			$output[] = array($startTime, 15);

		}
		return $output;
	}


	protected function isDragonImage($imagePath, $filename){
		$ch = curl_init();

//		curl_setopt($ch, CURLOPT_URL, "https://api.clarifai.com/v2/workflows/kaltura/results");
		curl_setopt($ch, CURLOPT_URL, "https://api.clarifai.com/v2/models/game_of_thrones/outputs");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$img64 = base64_encode( file_get_contents($imagePath));
		curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"inputs\": [{\"data\": {\"image\": {\"base64\": \"$img64\" }}}]}");
		curl_setopt($ch, CURLOPT_POST, 1);

		$headers = array();
		$headers[] = "Authorization: Key a747a3c4c5464742b5a96f32ffbe3d4f";
		$headers[] = "Content-Type: application/json";
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		file_put_contents("/opt/kaltura/tmp/dragonResults.txt", "{\"filename\":\"$filename\",\"data\":".$result."}\n", FILE_APPEND);
		$res =  json_decode($result,true);
		curl_close ($ch);
		return $this->is_action_image($res);
	}

	protected function is_action_image($res)
	{
//		$count = 0;
		$certainty = 0;
		try
		{
//			foreach ($res['results'][0]['outputs'][0]['data']['concepts'] as $concept)
			foreach ($res['outputs'][0]['data']['concepts'] as $concept)
			{
				if ($concept['id'] == 'dragon' && $concept['value'] == 1)
					return true;
				if (in_array($concept['id'], array("dragon", "flames", "fire")))
				{
					$certainty = $certainty + $concept['value'];
				}
			}
		}
		catch (Exception $e)
		{
			KalturaLog::debug("Caught exception in is_action_image [" . print_r($e->getMessage(),true). "]");
		}
		return $certainty > 2.1;
//		return false;
	}


	protected function getPreComputedDragonResults()
	{
		$dragonResults = file('/opt/kaltura/tmp/dragonResults.txt');
		$dragonFiles = array();
		foreach ($dragonResults as $currResult)
		{
			$res =  json_decode($currResult,true);
			$isActionImg = $this->is_action_image($res['data']);
			if ($isActionImg)
			{
				KalturaLog::debug("File [".$res['filename']."] is dragon!");
				$dragonFiles[] = $res['filename'];
			}
		}
		return $dragonFiles;
	}
}
