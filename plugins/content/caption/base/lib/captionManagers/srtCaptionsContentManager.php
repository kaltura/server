<?php
/**
 * @package plugins.caption
 * @subpackage lib
 */
class srtCaptionsContentManager extends kCaptionsContentManager
{
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

    public function buildSrtFile($captionItems, $clipStartTime)
    {
        $result = '';
        $index = 0;

        foreach($captionItems as $captionAssetItem) {
            $result .= $this->addItemToSrt($captionAssetItem, $index, $clipStartTime);
            $index += 1;
        }

        return $result;
    }

    private function addItemToSrt($captionAssetItem, $index, $clipStartTime)
    {
        $adjustedStartTime = $captionAssetItem->startTime - $clipStartTime;
        if ($adjustedStartTime < 0)
            $adjustedStartTime = 0;
        $adjustedEndTime = $captionAssetItem->endTime - $clipStartTime;
        $content = '';
        $content .= $index. "\n";
        $content .= $this->formatTime($adjustedStartTime);
        $content .= " --> ". $this->formatTime($adjustedEndTime). "\n";
        $content .= $captionAssetItem->content. "\n\n";
        return $content;
    }


    private function formatTime($timeInMili){
        $seconds = $timeInMili / 1000;
        $remainder = round($seconds - ($seconds >> 0), 3) * 1000;
        $formatted_remainder = sprintf("%03d", $remainder);

        return gmdate('H:i:s,', $seconds).$formatted_remainder;
    }
}