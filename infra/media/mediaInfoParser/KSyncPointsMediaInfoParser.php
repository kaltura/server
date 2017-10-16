<?php

class KSyncPointsMediaInfoParser
{
	const MIN_DIFF_BETWEEN_SYNC_POINTS_IN_MSEC = 60000;
	const DATA_TRACK_IDENTIFIER = "data";
	const MAX_DISCONTINUANCE_ALLOWED = 1000;
	const TS_PTS_DELIMITER = ";";
	
	protected $ffprobeBin = 'ffprobeKAMFMediaInfoParser';
	protected $filePath;
	
	public function __construct($filePath, $ffprobeBin=null)
	{
		if (is_null($ffprobeBin))
		{
			if (kConf::hasParam('bin_path_ffprobeKAMFMediaInfoParser'))
			{
				$this->ffprobeBin = kConf::get('bin_path_ffprobeKAMFMediaInfoParser');
			}
		}
		else
		{
			$this->ffprobeBin = $ffprobeBin;
		}
		if (!file_exists($filePath))
			throw new kApplicativeException(KBaseMediaParser::ERROR_NFS_FILE_DOESNT_EXIST, "File not found at [$filePath]");
		
		$this->filePath = $filePath;
	}
	
	private function getLocateDataStreamIndexCommand()
	{
		return "{$this->ffprobeBin} -i {$this->filePath} -show_streams -v quiet -print_format json";
	}
	
	private function getExtrackStreamInfoCommand($streamIndex)
	{
		return "{$this->ffprobeBin} -i {$this->filePath} -select_streams $streamIndex:$streamIndex -v quiet -show_data -show_packets -print_format json";
	}
	
	public function getStreamSyncPointData()
	{
		$rawStreamSyncPointInfo = $this->getRawStreamSyncPointsInfo();
		$syncPoints = $this->parseStreamSyncPointInfo($rawStreamSyncPointInfo);
		$syncPoints = $this->addAlignmentSyncPointsInfo($syncPoints);
		return $syncPoints;
	}
	
	// get the raw output of running the command
	private function getRawStreamSyncPointsInfo()
	{
		//Find the data stream index
		$dataStreamIndex = $this->getDataStreamIndex();
		
		$cmd = $this->getExtrackStreamInfoCommand($dataStreamIndex);
		KalturaLog::debug("Executing [$cmd] for extarckting id3 tags info");
		$output = shell_exec($cmd);
		if (trim($output) === "")
			throw new kApplicativeException(KBaseMediaParser::ERROR_EXTRACT_MEDIA_FAILED, "Failed to parse media using " . get_class($this));
		
		return $output;
	}
	
	private function getDataStreamIndex()
	{
		$dataStreamIndex = null;
		$streams = $this->getStreams();
		
		foreach ($streams as $stream)
		{
			if($stream->codec_type == self::DATA_TRACK_IDENTIFIER)
			{
				$dataStreamIndex = $stream->index;
				break;
			}
		}
		
		if(!$dataStreamIndex)
			throw new kApplicativeException(KBaseMediaParser::ERROR_EXTRACT_MEDIA_FAILED, "Failed to locate data stream index " . get_class($this));
		
		return $dataStreamIndex;
	}
	
	private function getStreams()
	{
		$cmd = $this->getLocateDataStreamIndexCommand();
		KalturaLog::debug("Executing [$cmd] to locate data track index");
		$streams = shell_exec($cmd);
		
		if (trim($streams) === "")
			throw new kApplicativeException(KBaseMediaParser::ERROR_EXTRACT_MEDIA_FAILED, "Failed to locate data stream index " . get_class($this));
		
		$streams = strtolower($streams);
		return json_decode($streams)->streams;
	}
	
	// Parse the output of the command and return an array of string of the form pts;timestamp
	private function parseStreamSyncPointInfo($rawStreamSyncPointInfo)
	{
		$syncPoints = array();
		$rawStreamSyncPointInfo = strtolower($rawStreamSyncPointInfo);
		$streamSyncPointInfoJson = json_decode($rawStreamSyncPointInfo);
		
		if (!is_null($streamSyncPointInfoJson))
		{
			// Check for json decode errors caused by inproper utf8 encoding.
			if (json_last_error() != JSON_ERROR_NONE)
				$streamSyncPointInfoJson = json_decode(utf8_encode($rawStreamSyncPointInfo));
			
			$streamSyncPointInfoJson = $streamSyncPointInfoJson->packets;
			
			foreach ($streamSyncPointInfoJson as $streamSyncPointInfo)
			{
				$streamId3tagTimeStamp = $this->getTimestampFromStreamInfo($streamSyncPointInfo->data);
				$streamPtsTime = (int)($streamSyncPointInfo->pts_time * 1000); //convert for seconds to milliseconds
				
				if(!isset($streamPtsTime) || !isset($streamId3tagTimeStamp))
				{
					KalturaLog::debug("Could not locate streamPtsTime [$streamPtsTime] or streamId3tagTimeStamp [$streamId3tagTimeStamp] skipping validation");
					continue;
				}
				
				KalturaLog::debug("Testing:: time stamps to check are: streamId3tagTimeStamp [$streamId3tagTimeStamp] streamPts [$streamPtsTime]");
				if ($streamId3tagTimeStamp >= 0 && $this->shouldSaveSyncPoint(end($syncPoints), $streamId3tagTimeStamp, $streamPtsTime))
				{
					$syncPoints[] = $streamPtsTime . self::TS_PTS_DELIMITER . $streamId3tagTimeStamp;
				}
			}
			KalturaLog::debug("Returning syncPoints array: " . print_r($syncPoints, true));
		}
		else
		{
			KalturaLog::warning('Failed to json_decode. returning an empty syncPoints array');
		}
		
		return $syncPoints;
	}
	
	//For each syncPoint add another before it to aligns the info without the drift
	private function addAlignmentSyncPointsInfo($syncPoints)
	{
		$ret = array();
		
		foreach ($syncPoints as $key => $syncPoint)
		{
			$ret[] = $syncPoint;
			if(!isset($syncPoints[$key+1]))
				break;
			
			$currPoint = explode(self::TS_PTS_DELIMITER, $syncPoint);
			$nextSyncPoint = explode(self::TS_PTS_DELIMITER, $syncPoints[$key+1]);
			
			$drift = ($nextSyncPoint[1] - $currPoint[1]) - ($nextSyncPoint[0] - $currPoint[0]);
			$alignerSyncPointTs = $nextSyncPoint[1] - $drift;
			$alignerSyncPointPts = $alignerSyncPointTs - $currPoint[1] + $currPoint[0];
			
			$ret[] = $alignerSyncPointPts . self::TS_PTS_DELIMITER . $alignerSyncPointTs;
		}
		
		return $ret;
	}
	
	/*
	 	parse the output of ffprobe that looks like:
	 		00000000: 0000 0000 2754 4558 5400 0000 1d00 0003  ....'text.......
			00000010: 7b22 7469 6d65 7374 616d 7022 3a31 3437  {"timestamp":147
			00000020: 3537 3533 3730 3233 3538 7d00            5753702358}.
	
		and return the timestamp value from the json object
		
	 */
	private function getTimestampFromStreamInfo($rawStreamSyncPointInfo)
	{
		$ret = '';
		$timeStamp = null;
		
		$rawStreamSyncPointInfoLines = explode("\n", $rawStreamSyncPointInfo);
		for ($i = 0; $i < count($rawStreamSyncPointInfoLines); $i++) {
			$ret .= str_replace(' ', '', substr($rawStreamSyncPointInfoLines[$i], 49, 20));
		}
		
		//locate id3tag json object start position
		$jsonObjStartPosition = strpos($ret, "{");
		$ret = substr($ret, $jsonObjStartPosition);
		
		//locate id3tag json object end position
		$jsonObjEndPosition = strpos($ret, "}") + 1;
		$ret = substr($ret, 0, $jsonObjEndPosition);
		
		$ret = json_decode($ret);
		if(!is_null($ret))
			$timeStamp = $ret->timestamp;
		
		return $timeStamp;
	}
	
	private function shouldSaveSyncPoint($lastSyncPoint, $streamId3tagTimeStamp, $streamPts)
	{
		if (!$lastSyncPoint)
		{
			KalturaLog::debug("First syncPoint, ts = [$streamId3tagTimeStamp] pts = [$streamPts]");
			return true;
		}
		
		$syncPointParts = explode(self::TS_PTS_DELIMITER, $lastSyncPoint);
		$lastStreamPts = $syncPointParts[0];
		$lastId3TagTimeStamp = $syncPointParts[1];
		$tsDelta = $streamId3tagTimeStamp - $lastId3TagTimeStamp;
		$ptsDelta = $streamPts - $lastStreamPts;
		
		if (abs($tsDelta - $ptsDelta) >=  self::MAX_DISCONTINUANCE_ALLOWED)
		{
			if ($tsDelta > self::MIN_DIFF_BETWEEN_SYNC_POINTS_IN_MSEC)
			{
				KalturaLog::debug("Discontinuance found, adding syncPoint tsDelta = [$tsDelta] ptsDelta = [$ptsDelta]");
				return true;
			}
			else
			{
				KalturaLog::debug("Discontinuance found, but not adding syncPoint since time from last syncPoint is less than ["
					. self::MIN_DIFF_BETWEEN_SYNC_POINTS_IN_MSEC . "] tsDelta = [$tsDelta] ptsDelta [$ptsDelta]");
			}
		}
		else
		{
			KalturaLog::debug("NOT adding syncPoint, tsDelta = [$tsDelta] ptsDelta = [$ptsDelta]");
		}
		
		return false;
	}
}