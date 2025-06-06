<?php
/**
 * @package plugins.caption
 * @subpackage lib
 */
class srtCaptionsContentManager extends kCaptionsContentManager
{

	const SRT_TIMECODE_PATTERN = '#^((?:[0-9]{2}:)?[0-9]{2}:[0-9]{2}\,[0-9]{3}) --> ((?:[0-9]{2}:)?[0-9]{2}:[0-9]{2}\,[0-9]{3})( .*)?$#';

	protected function customPregMatchAll($pattern, $subject, &$matches)
	{
		$matches = [];
		$offset = 0;
		$allMatches = [];

		while (preg_match($pattern, $subject, $match, PREG_OFFSET_CAPTURE, $offset))
		{
			$allMatches[] = $match;
			$offset = $match[0][1] + strlen($match[0][0]);
		}

		 if (!empty($allMatches))
		 {
			 foreach ($allMatches as $match)
			 {
				 foreach ($match as $key => $value)
				 {
					 $matches[$key][] = $value[0];
				 }
			 }
		 }

		 return count($allMatches);
	}

	/* (non-PHPdoc)
	 * @see kCaptionsContentManager::parse()
	 */
	public function parse($content)
	{
		if (kString::beginsWith($content, "\xff\xfe"))
		{
			$content = @iconv('utf-16', 'utf-8//IGNORE', substr($content, 2));
		}
		else
		{
			$content = @iconv('utf-8', 'utf-8//IGNORE', $content);
		}
		
		$matches = null;
		$regex = '/(?P<index>\d+)\s*\r?\n\s*(?P<startHours>\d{1,2}):(?P<startMinutes>\d{1,2}):(?P<startSeconds>\d{1,2})[,\.](?P<startMilliseconds>\d{1,3})\s*-->\s*(?P<endHours>\d{1,2}):(?P<endMinutes>\d{1,2}):(?P<endSeconds>\d{1,2})[,\.](?P<endMilliseconds>\d{1,3})\s*\r?\n((?P<content>.+)\r?(\n|$))?\s*\r?(\n|$)/sU';

		$pregMatchAll = $this->customPregMatchAll($regex, $content, $matches);
		if(!$pregMatchAll || !count($matches) || !count($matches[0]))
		{
			KalturaLog::err("Content regex not found");
			return array();
		}
		
		$itemsData = array();
		foreach($matches[0] as $index => $match)
		{
			$startHours = intval($matches['startHours'][$index]);
			$startMinutes = intval($matches['startMinutes'][$index]);
			$startSeconds = intval($matches['startSeconds'][$index]);
			$startMilliseconds = intval($matches['startMilliseconds'][$index]);
			$endHours = intval($matches['endHours'][$index]);
			$endMinutes = intval($matches['endMinutes'][$index]);
			$endSeconds = intval($matches['endSeconds'][$index]);
			$endMilliseconds = intval($matches['endMilliseconds'][$index]);
			$content = $matches['content'][$index];
			
			$itemsData[] = array(
				'startTime' => $this->makeTime($startHours, $startMinutes, $startSeconds, $startMilliseconds),
				'endTime' => $this->makeTime($endHours, $endMinutes, $endSeconds, $endMilliseconds), 
				'content' => array(array('text' => $content)),
			);
		}
		return $itemsData;
	}
	
	private function makeTime($hours, $minutes, $seconds, $milliseconds)
	{
		$ret = $hours * 60;
		$ret += $minutes;
		$ret *= 60;
		$ret += $seconds;
		$ret *= 1000;
		$ret += $milliseconds;
		return $ret;
	}
	
	/* (non-PHPdoc)
	 * @see kCaptionsContentManager::getContent()
	 */
	public function getContent($content)
	{
		if (kString::beginsWith($content, "\xff\xfe"))
		{
			$content = iconv('utf-16', 'utf-8', substr($content, 2));
		}
		
		$replace = array(
			'/^[\d]+\s*[\r\n]+/' => '',
			'/[\r\n]+\s*[\d]+\s*[\r\n]+/' => '',
			'/[\d]{2}:[\d]{2}:[\d]{2},[\d]{3}/' => '',
			'/\s+-->\s+/' => ' ',
			'/\s+/' => ' ',
		);
		return preg_replace(array_keys($replace), $replace, $content);
	}
	
	/**
	 * @return srtCaptionsContentManager
	 */
	public static function get()
	{
		return new srtCaptionsContentManager();
	}

	public function buildFile($content, $clipStartTime, $clipEndTime, $globalOffset = 0)
	{
		$newFileContent = $this->createCaptionsFile($content, $clipStartTime, $clipEndTime, self::SRT_TIMECODE_PATTERN, $globalOffset);
		return $newFileContent;
	}


	protected function createAdjustedTimeLine($matches, $clipStartTime, $clipEndTime, $globalOffset)
	{
		$startCaption = self::parseCaptionTime($matches[1]);
		$endCaption = self::parseCaptionTime($matches[2]);
		if (!TimeOffsetUtils::onTimeRange($startCaption, $endCaption, $clipStartTime, $clipEndTime))
			return null;
		$adjustedStartTime = TimeOffsetUtils::getAdjustedStartTime($startCaption, $clipStartTime, $globalOffset);
		$adjustedEndTime = TimeOffsetUtils::getAdjustedEndTime($endCaption, $clipStartTime, $clipEndTime, $globalOffset);
		$timeLine = $this->formatSrtTimeStamp($adjustedStartTime) . ' --> ' . $this->formatSrtTimeStamp($adjustedEndTime). kCaptionsContentManager::UNIX_LINE_ENDING;
		return $timeLine;
	}


	/**
	 * @param int $timeStamp
	 * @return string
	 */
	private function formatSrtTimeStamp($timeInMili)
	{
		$seconds = $timeInMili / 1000;
		$remainder = round($seconds - ($seconds >> 0), 3) * 1000;
		$formatted_remainder = sprintf("%03d", $remainder);
		return gmdate('H:i:s,', $seconds).$formatted_remainder;
	}

	/**
	 * @param $time
	 * @return string
	 */
	public function parseCaptionTime($time)
	{
		$time = str_replace(',','.',$time);
		$captionTime = parent::parseCaptionTime($time);
		return $captionTime;
	}

	/**
	 * @param string $content
	 * @param string $toAppend
	 * @return string
	 */
	public function merge($content, $toAppend)
	{
		return $content . $toAppend;
	}

	protected function addBlockToNewFile($newFileContent, $currentBlock)
	{
		static $newLineIndex = 1;
		$blockArray = explode("\n", $currentBlock, 2);
		$blockArray[0] = $newLineIndex++;
		$currentBlock = implode("\n", $blockArray);
		$newFileContent .= $currentBlock . kCaptionsContentManager::UNIX_LINE_ENDING;
		return $newFileContent;
	}
}
