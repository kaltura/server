<?php

class capCaptionsContentManager extends kCaptionsContentManager
{
	const DATA_START_OFFSET = 0x80;
	const START_TIME_OFFSET = 2;
	const END_TIME_OFFSET = 6;
	const HEADER_SIZE_NO_END_TIME = 12;
	const HEADER_SIZE_END_TIME = 16;
	const FLAG_HAS_END_TIME = 0x20;
	const TIMESTAMP_SIZE = 4;
	const NO_END_TIME = 0xffffffff;
	const FRAME_RATE = 30;
	const LAST_FRAME_DURATION = 2000;

	static protected $SPECIAL_CHARS = array(
		0x81 => "\xe2\x99\xaa",
		0x82 => "\xc3\xa1",
		0x83 => "\xc3\xa9",
		0x84 => "\xc3\xad",
		0x85 => "\xc3\xb3",
		0x86 => "\xc3\xba",
		0x87 => "\xc3\xa2",
		0x88 => "\xc3\xaa",
		0x89 => "\xc3\xae",
		0x8A => "\xc3\xb4",
		0x8B => "\xc3\xbb",
		0x8C => "\xc3\xa0",
		0x8D => "\xc3\xa8",
		0x8E => "\xc3\x91",
		0x8F => "\xc3\xb1",
		0x90 => "\xc3\xa7",
		0x91 => "\xc2\xa2",
		0x92 => "\xc2\xa3",
		0x93 => "\xc2\xbf",
		0x94 => "\xc2\xbd",
		0x95 => "\xc2\xae",
	);
	
	protected function framesToMS($frames)
	{
		return floor(min(1000 * $frames / self::FRAME_RATE, 999));
	}
	
	protected function decodeTimestamp($ts, $hoursBase)
	{
		$unpacked = unpack('Chour/Cmin/Csec/Cframe', $ts);
		return (($unpacked['hour'] - $hoursBase) * 3600 + $unpacked['min'] * 60 + $unpacked['sec']) * 1000 + $this->framesToMS($unpacked['frame']);
	}	
	
	public function parse($content)
	{
		$itemsData = array();
		$last = null;
		$pos = self::DATA_START_OFFSET;
		$contentLen = strlen($content);
		while ($pos < $contentLen) 
		{
			// get the block len
			$length = ord($content[$pos]);
			if ($length == 0)
			{
				$pos += 1;
				continue;
			}
			else if ($length > $contentLen - $pos)
			{
				break;
			}
				
			// find start pos
			$flags = ord($content[$pos + 1]);
			if (($flags & self::FLAG_HAS_END_TIME) != 0)	// has end time
			{
				$start = self::HEADER_SIZE_END_TIME;
			}
			else
			{
				$start = self::HEADER_SIZE_NO_END_TIME;
			}
			
			if ($length <= $start + 1)
			{
				$pos += $length;
				continue;
			}
			
			if (is_null($last))
			{
				$hoursBase = ord($content[$pos + self::START_TIME_OFFSET]);
			}

			// get timestamps
			$startTime = $this->decodeTimestamp(substr($content, $pos + self::START_TIME_OFFSET, self::TIMESTAMP_SIZE), $hoursBase);
			if (!is_null($last) && $last['endTime'] > $startTime)
			{
				$itemsData[count($itemsData) - 1]['endTime'] = $startTime;
			}
			if (($flags & self::FLAG_HAS_END_TIME) != 0)	// has end time
			{
				$endTime = $this->decodeTimestamp(substr($content, $pos + self::END_TIME_OFFSET, self::TIMESTAMP_SIZE), $hoursBase);
			}
			else
			{
				$endTime = self::NO_END_TIME;
			}

			// process text
			$text = '';
			for ($index = $pos + $start; $index < $pos + $length - 1; $index++)
			{
				$curCh = $content[$index];
				$curChOrd = ord($curCh);
				if ($curChOrd == 0)
				{
					if (strlen($text) == 0 || $text[strlen($text) - 1] != "\n")
					{
						$text .= "\n";
					}
				}
				else if (isset(self::$SPECIAL_CHARS[$curChOrd]))
				{
					$text .= self::$SPECIAL_CHARS[$curChOrd];
				}
				else if ($curChOrd < 0x20 || $curChOrd >= 0xc0)	// styles
				{
					continue;
				}
				else
				{
					$text .= $curCh;
				}
			}
			
			// add record
			$last = array(
				'startTime' => $startTime,
				'endTime' => $endTime, 
				'content' => array(array('text' => trim($text))),
			);
			$itemsData[] = $last;

			// move to the next block
			$pos += $length;
		}
		
		if (!is_null($last) && $last['endTime'] == self::NO_END_TIME)
		{
			$itemsData[count($itemsData) - 1]['endTime'] = $last['startTime'] + self::LAST_FRAME_DURATION;
		}
		
		return $itemsData;
	}
	
	public function getContent($content)
	{
		$result = '';
		$parsed = $this->parse($content);
		foreach ($parsed as $curItem)
		{
			$result .= $curItem['content'][0]['text'] . ' ';
		}
		return preg_replace('/\s+/', ' ', $result);
	}	

	/**
	 * @return srtCaptionsContentManager
	 */
	public static function get()
	{
		return new capCaptionsContentManager();
	}

	public function buildFile($content, $clipStartTime, $clipEndTime, $globalOffset = 0)
	{
	}

	protected function createAdjustedTimeLine($matches, $clipStartTime, $clipEndTime, $globalOffset)
	{
	}

}
