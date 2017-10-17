<?php
/**
 * @package plugins.caption
 * @subpackage lib
 */
class kWebVTTGenerator
{
	/**
	 * @param int $timeStamp
	 * @return string
	 */
	public static function formatWebVTTTimeStamp($timeStamp)
	{
		$millis = $timeStamp % 1000;
		$timeStamp = (int)($timeStamp / 1000);
		$seconds = $timeStamp % 60;
		$timeStamp = (int)($timeStamp / 60);
		$minutes = $timeStamp % 60;
		$timeStamp = (int)($timeStamp / 60);
		$hours = $timeStamp;
		return sprintf('%02d:%02d:%02d.%03d', $hours, $minutes, $seconds, $millis);
	}

	/**
	 * @param int $segmentDuration
	 * @param int $entryDuration
	 * @return string
	 */
	public static function buildWebVTTM3U8File($segmentDuration, $entryDuration)
	{
		$result = "#EXTM3U\r\n";
		$result .= "#EXT-X-TARGETDURATION:{$segmentDuration}\r\n";
		$result .= "#EXT-X-VERSION:3\r\n";
		$result .= "#EXT-X-MEDIA-SEQUENCE:1\r\n";
		$result .= "#EXT-X-PLAYLIST-TYPE:VOD\r\n";
		$segmentCount = ceil($entryDuration / $segmentDuration);
		$lastSegmentDuration = $entryDuration - ($segmentCount - 1) * $segmentDuration;
		for ($curIndex = 1; $curIndex <= $segmentCount; $curIndex++)
		{
			if ($curIndex == $segmentCount)
			{
				$result .= "#EXTINF:{$lastSegmentDuration}.0,\r\n";
			}
			else
			{
				$result .= "#EXTINF:{$segmentDuration}.0,\r\n";
			}
			$result .= "segmentIndex/{$curIndex}.vtt\r\n";
		}
		$result .= "#EXT-X-ENDLIST\r\n";
		return $result;
	}

	/**
	 * @param array $parsedCaption
	 * @param int $segmentIndex
	 * @param int $segmentDuration
	 * @param int $localTimestamp
	 * @return string
	 */
	public static function buildWebVTTSegment(array $parsedCaption, $segmentIndex, $segmentDuration, $localTimestamp)
	{
		$segmentStartTime = ($segmentIndex - 1) * $segmentDuration * 1000;
		$segmentEndTime = $segmentIndex * $segmentDuration * 1000;

		$result = "WEBVTT\n";
		if ($localTimestamp != 10000)
		{
			$result .= "X-TIMESTAMP-MAP=MPEGTS:900000,LOCAL:" . self::formatWebVTTTimeStamp($localTimestamp) . "\n";
		}
		$result .= "\n";

		foreach ($parsedCaption as $curCaption)
		{
			if ($segmentIndex != -1 && ($curCaption["startTime"] < $segmentStartTime || $curCaption["startTime"] >= $segmentEndTime) &&
				($curCaption["endTime"] < $segmentStartTime || $curCaption["endTime"] >= $segmentEndTime)
			)
				continue;

			// calculate line-level styling
			$styling = '';
			$firstChunk = reset($curCaption['content']);
			if ($firstChunk && isset($firstChunk['style']))
			{
				$style = $firstChunk['style'];

				$aligmentMapping = array('left' => 'start', 'right' => 'end', 'center' => 'middle', 'full' => 'middle');
				if (isset($style['textAlign']))
				{
					$styling .= ' align:';
					if (isset($aligmentMapping[$style['textAlign']]))
						$styling .= $aligmentMapping[$style['textAlign']];
					else
						$styling .= $style['align'];
				}

				$aligmentMapping = array('before' => '0%', 'center' => '50%', 'after' => '100%');
				if (isset($style['displayAlign']) && isset($aligmentMapping[$style['displayAlign']]))
					$styling .= ' line:' . $aligmentMapping[$style['displayAlign']];
			}

			// calculate the line content
			$content = '';
			foreach ($curCaption['content'] as $curChunk)
			{
				$curChunkText = $curChunk['text'];
				if (isset($curChunk['style']))
				{
					$style = $curChunk['style'];
					if (isset($style['bold']))
						$curChunkText = "<b>$curChunkText</b>";
					if (isset($style['italic']))
						$curChunkText = "<i>$curChunkText</i>";
				}
				$content .= $curChunkText;
			}

			// make sure the content does not contain 2 consecutive newlines
			$content = preg_replace('/\n+/', "\n", str_replace("\r", '', $content));

			$result .= self::formatWebVTTTimeStamp($curCaption["startTime"]) . ' --> ' .
				self::formatWebVTTTimeStamp($curCaption["endTime"]) .
				$styling . "\n";
			$result .= trim($content) . "\n\n";
		}
		return $result;
	}

	/**
	 * @param webVttCaptionsContentManager $captionsContentManager
	 * @param int $segmentIndex
	 * @param int $segmentDuration
	 * @param int $localTimestamp
	 * @return string
	 */
	public static function getSegmentFromWebVTT($captionsContentManager, $webVTTcontent, $segmentIndex, $segmentDuration, $localTimestamp)
	{
		$parsedCaption = $captionsContentManager->parseWebVTT($webVTTcontent);
		$headerInfo = $captionsContentManager->headerInfo;

		$segmentStartTime = ($segmentIndex - 1) * $segmentDuration * 1000;
		$segmentEndTime = $segmentIndex * $segmentDuration * 1000;

		$result = implode('', $headerInfo);
		if ($localTimestamp != 10000)
		{
			$result .= "X-TIMESTAMP-MAP=MPEGTS:900000,LOCAL:" . self::formatWebVTTTimeStamp($localTimestamp) . "\n";
		}
		$result .= "\n";

		foreach ($parsedCaption as $curCaption)
		{
			if ($segmentIndex != -1 && ($curCaption["startTime"] < $segmentStartTime || $curCaption["startTime"] >= $segmentEndTime) &&
			($curCaption["endTime"] < $segmentStartTime || $curCaption["endTime"] >= $segmentEndTime))
			continue;
			// calculate the line content
			$content = '';
			foreach ($curCaption['content'] as $curChunk)
			{
				$curChunkText = $curChunk['text'];
				$content .= $curChunkText;
			}

			// make sure the content does not contain 2 consecutive newlines
			$content = preg_replace('/\n+/', "\n", str_replace("\r", '', $content));

			$result .= self::formatWebVTTTimeStamp($curCaption["startTime"]) . ' --> ' .
				self::formatWebVTTTimeStamp($curCaption["endTime"]) ."\n";
			$result .= trim($content) . "\n\n";
		}
		return $result;
	}
}