<?php
/**
 * @package plugins.caption
 * @subpackage lib
 */
class srtCaptionsContentManager extends kCaptionsContentManager
{

	const SRT_TIMECODE_PATTERN = '#^((?:[0-9]{2}:)?[0-9]{2}:[0-9]{2}\,[0-9]{3}) --> ((?:[0-9]{2}:)?[0-9]{2}:[0-9]{2}\,[0-9]{3})( .*)?$#';

	/* (non-PHPdoc)
	 * @see kCaptionsContentManager::parse()
	 */
	public function parse($content)
	{
		if (kString::beginsWith($content, "\xff\xfe"))
		{
			$content = iconv('utf-16', 'utf-8', substr($content, 2));
		}
		
		$matches = null;
		$regex = '/(?P<index>\d+)\s*\r?\n\s*(?P<startHours>\d{1,2}):(?P<startMinutes>\d{1,2}):(?P<startSeconds>\d{1,2})[,\.](?P<startMilliseconds>\d{1,3})\s*-->\s*(?P<endHours>\d{1,2}):(?P<endMinutes>\d{1,2}):(?P<endSeconds>\d{1,2})[,\.](?P<endMilliseconds>\d{1,3})\s*\r?\n((?P<content>.+)\r?(\n|$))?\s*\r?(\n|$)/sU';
		if(!preg_match_all($regex, $content, $matches) || !count($matches) || !count($matches[0]))
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

	public function buildFile($content, $clipStartTime, $clipEndTime)
	{
		$fileContentArray = kCaptionsContentManager::getFileContentAsArray($content);
		$editedContent = '';
		$currentBlock = '';
		while (($line = kCaptionsContentManager::getNextValueFromArray($fileContentArray)) !== false)
		{
			$matches = array();
			$timecode_match = preg_match(self::SRT_TIMECODE_PATTERN, $line, $matches);
			if ($timecode_match)
			{
				$startCaption = self::parseCaptionTime($matches[1]);
				$endCaption = self::parseCaptionTime($matches[2]);
				if (!kCaptionsContentManager::onTimeRange($startCaption, $endCaption, $clipStartTime, $clipEndTime))
				{
					$currentBlock = '';
					while (trim($line = kCaptionsContentManager::getNextValueFromArray($fileContentArray)) !== '')
					{
					}
					continue;
				}

				$adjustedStartTime = kCaptionsContentManager::getAdjustedStartTime($startCaption, $clipStartTime);
				$adjustedEndTime = kCaptionsContentManager::getAdjustedEndTime($clipStartTime, $clipEndTime, $endCaption);
				$currentBlock .= $this->formatSrtTimeStamp($adjustedStartTime) . ' --> ' . $this->formatSrtTimeStamp($adjustedEndTime). kCaptionsContentManager::UNIX_LINE_ENDING;

				$text = '';
				while (trim($line = kCaptionsContentManager::getNextValueFromArray($fileContentArray)) !== '')
				{
					$line = kCaptionsContentManager::handleTextLines($line);
					$text .= $line . kCaptionsContentManager::UNIX_LINE_ENDING;
				}

				$currentBlock .= $text . kCaptionsContentManager::UNIX_LINE_ENDING;
				$editedContent .= $currentBlock;
				$currentBlock = '';
			}
			else
			{
				$currentBlock .= $line . kCaptionsContentManager::UNIX_LINE_ENDING;
				if ($line === '')
				{
					$editedContent .= $currentBlock;
					$currentBlock = '';
				}
			}
		}
		return $editedContent;
	}


	/**
	 * @param int $timeStamp
	 * @return string
	 */
	private function formatSrtTimeStamp($timeInMili){
		$seconds = $timeInMili / 1000;
		$remainder = round($seconds - ($seconds >> 0), 3) * 1000;
		$formatted_remainder = sprintf("%03d", $remainder);
		return gmdate('H:i:s,', $seconds).$formatted_remainder;
	}


	private function parseCaptionTime($time){
		$modifiedTime = str_replace(',','.',$time);
		list($CaptionTime, $errorStart) = kCaptionsContentManager::parseStrTTTime($modifiedTime);
		return $CaptionTime;
	}

}