<?php
/**
 * @package plugins.caption
 * @subpackage lib
 */
class kWebVTTGenerator
{
	const HTML_ALLOWED_UNDECODED_CHARS = '/&lt;(i|b|u)&gt;/';
	const WEBVTT_CUE_PAYLOAD_ENCODED_CHARS = '/(&amp;)|(&gt;)|(&lt;)|(&lrm;)|(&rlm;)|(&nbsp;)/';
	const WEBVTT_CUE_PAYLOAD_DECODED_CHARS = '/[&<>]/';

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
	public static function buildWebVTTM3U8File($segmentDuration, $entryDuration, $clipFrom, $clipTo)
	{
		$result = self::getManifestHeaderForWebVTT($segmentDuration);

		$entryDurationMs = $entryDuration * 1000;
		$linkParamsToConcat = '';
		if ($clipTo && $entryDurationMs > $clipTo)
		{
			$entryDurationMs = $clipTo;
			$linkParamsToConcat .= "clipTo/$clipTo/";
		}
		if ($clipFrom)
		{
			$entryDurationMs -= $clipFrom;
			$linkParamsToConcat .= "clipFrom/$clipFrom/";
		}

		$segmentCount = ceil(($entryDurationMs / 1000) / $segmentDuration);
		$lastSegmentDuration = ceil($entryDurationMs / 1000) - ($segmentCount - 1) * $segmentDuration;
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

			$result .= $linkParamsToConcat;
			$result .= "segmentIndex/{$curIndex}.vtt\r\n";
		}
		$result .= "#EXT-X-ENDLIST\r\n";
		return $result;
	}

	protected static function getManifestHeaderForWebVTT($segmentDuration)
	{
		$result = "#EXTM3U\r\n";
		$result .= "#EXT-X-TARGETDURATION:{$segmentDuration}\r\n";
		$result .= "#EXT-X-VERSION:3\r\n";
		$result .= "#EXT-X-MEDIA-SEQUENCE:1\r\n";
		$result .= "#EXT-X-PLAYLIST-TYPE:VOD\r\n";
		return $result;
	}

	/**
	 * @param array $parsedCaption
	 * @param int $segmentIndex
	 * @param int $segmentDuration
	 * @param int $localTimestamp
	 * @return string
	 */
	public static function buildWebVTTSegment(array $parsedCaption, $segmentIndex, $segmentDuration, $localTimestamp, $clipFrom, $clipTo)
	{
		$result = self::getSegmentHeaderForWebVTT($localTimestamp);

		[$segmentStartTime, $segmentEndTime] = self::calculateSegmentTime($segmentIndex, $segmentDuration, $clipFrom, $clipTo);

		foreach ($parsedCaption as $curCaption)
		{
			if (self::validateCaptionTimeFrame($curCaption, $segmentStartTime, $segmentEndTime))
			{
				continue;
			}

			if ($curCaption['startTime'] >= $curCaption['endTime'])
			{
				continue;
			}

			// calculate line-level styling
			$styling = array();
			$firstChunk = reset($curCaption['content']);
			if ($firstChunk && isset($firstChunk['style']))
			{
				$style = $firstChunk['style'];

				$alignmentMapping = array('left' => 'start', 'right' => 'end', 'center' => 'middle', 'full' => 'middle');
				if (isset($style['textAlign']))
				{
					if (isset($alignmentMapping[$style['textAlign']]))
						$styling['align'] = $alignmentMapping[$style['textAlign']];
					else
						$styling['align'] = $style['align'];
				}

				$alignmentMapping = array('before' => '0%', 'center' => '50%', 'after' => '100%');
				if (isset($style['displayAlign']) && isset($alignmentMapping[$style['displayAlign']]))
					$styling['line'] = $alignmentMapping[$style['displayAlign']];

				if (isset($style['origin']))
				{
					$origin = explode(' ', $style['origin']);
					$styling['position'] = $origin[0];
					$styling['line'] = $origin[1];
					$styling['align'] = 'start';
				}
				if (isset($style['extent']))
				{
					$extent = explode(' ', $style['extent']);
					$styling['size'] = $extent[0];
				}
			}

			$stylingStr = '';
			foreach($styling as $key => $value)
			{
				$stylingStr .= " $key:$value";
			}

			// calculate the line content
			$content = '';
			foreach ($curCaption['content'] as $curChunk)
			{
				$curChunkText = self::escapeSpecialChars($curChunk['text']);
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
				$stylingStr . "\n";
			$result .= trim($content) . "\n\n";
		}
		$result .="\n\n\n";
		return $result;
	}

	/**
	 * @param webVttCaptionsContentManager $captionsContentManager
	 * @param int $segmentIndex
	 * @param int $segmentDuration
	 * @param int $localTimestamp
	 * @return string
	 */
	public static function getSegmentFromWebVTT($captionsContentManager, $webVTTcontent, $segmentIndex, $segmentDuration, $localTimestamp, $clipFrom, $clipTo)
	{
		$result = self::getSegmentHeaderForWebVTT($localTimestamp, $captionsContentManager->headerInfo);

		[$segmentStartTime, $segmentEndTime] = self::calculateSegmentTime($segmentIndex, $segmentDuration, $clipFrom, $clipTo);
		
		$parsedCaption = $captionsContentManager->parseWebVTT($webVTTcontent);
		foreach ($parsedCaption as $curCaption)
		{
			if (self::validateCaptionTimeFrame($curCaption, $segmentStartTime, $segmentEndTime))
			{
				continue;
			}

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

		$result .="\n\n\n";

		return $result;
	}

	protected static function getSegmentHeaderForWebVTT($localTimestamp, $headerInfo = null)
	{
		$result = isset($headerInfo) ? implode('', $headerInfo) : "WEBVTT\n";
		if ($localTimestamp != 10000)
		{
			$result .= "X-TIMESTAMP-MAP=MPEGTS:900000,LOCAL:" . self::formatWebVTTTimeStamp($localTimestamp) . "\n";
		}
		$result .= "\n";
		return $result;
	}

	protected static function calculateSegmentTime($segmentIndex, $segmentDuration, $clipFrom, $clipTo)
	{
		$segmentStartTime = $clipFrom ?: 0;

		if ($segmentIndex == -1)
		{
			if (!$clipFrom && !$clipTo)
			{
				return array(-1, -1);
			}
		}
		else
		{
			$segmentStartTime += ($segmentIndex - 1) * $segmentDuration * 1000;
		}

		$segmentEndTime = $segmentStartTime + $segmentDuration * 1000;

		if ($clipTo && $clipTo < $segmentEndTime)
		{
			$segmentEndTime = $clipTo;
		}

		return array($segmentStartTime, $segmentEndTime);
	}

	protected static function validateCaptionTimeFrame($curCaption, $segmentStartTime, $segmentEndTime)
	{
		return $segmentStartTime > -1 && $segmentEndTime > -1
			&& ($curCaption['startTime'] < $segmentStartTime || $curCaption['startTime'] >= $segmentEndTime)
			&& ($curCaption['endTime'] < $segmentStartTime || $curCaption['endTime'] >= $segmentEndTime);
	}

	/**
	 * @param string $textLine
	 * @return string
	 */
	public static function escapeSpecialChars($textLine)
	{
		$isEncoded = preg_match(self::WEBVTT_CUE_PAYLOAD_ENCODED_CHARS, $textLine);
		if (!$isEncoded)
		{
			$shouldEncode = preg_match(self::WEBVTT_CUE_PAYLOAD_DECODED_CHARS, $textLine);
			if ($shouldEncode)
			{
				$textLine = htmlspecialchars($textLine, ENT_NOQUOTES);

				$shouldDecodeStyleTags = preg_match(self::HTML_ALLOWED_UNDECODED_CHARS, $textLine);
				if($shouldDecodeStyleTags)
				{
					$find = array('&lt;i&gt;', '&lt;/i&gt;', '&lt;b&gt;', '&lt;/b&gt;', '&lt;u&gt;', '&lt;/u&gt;');
					$replace = array('<i>', '</i>', '<b>', '</b>', '<u>', '</u>');
					$textLine = str_replace($find, $replace, $textLine);
				}
			}
		}
		return $textLine;
	}
}