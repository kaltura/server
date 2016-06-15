<?php
/**
 * @package plugins.caption
 * @subpackage lib
 */
class webVttCaptionsContentManager extends kCaptionsContentManager
{
	const UNIX_LINE_ENDING = "\n";
	const MAC_LINE_ENDING = "\r";
	const WINDOWS_LINE_ENDING = "\r\n";

	const TIMECODE_PATTERN = '#^((?:[0-9]{2}:)?[0-9]{2}:[0-9]{2}\.[0-9]{3}) --> ((?:[0-9]{2}:)?[0-9]{2}:[0-9]{2}\.[0-9]{3})( .*)?$#';

	const BOM_CODE =  "\xEF\xBB\xBF";
	/**
	 * @var array
	 */
	protected $parsingErrors = array();

	/**
	 * @var array
	 */
	public $headerInfo = array();

	/* (non-PHPdoc)
	 * @see kCaptionsContentManager::parse()
	 */
	public function parse($content)
	{
		$itemsData =  $this->parseWebVTT($content);

		foreach ($itemsData as &$itemData )
		{
			foreach ($itemData['content'] as &$curChunk)
			{
				$val = strip_tags($curChunk['text']);
				$curChunk['text'] = $val;
			}
		}
		return $itemsData;
	}

	/**
	 * @param content
	 * @return array
	 */
	public static function getFileContentAsArray($content)
	{
		$textContent = str_replace( // So we change line endings to one format
			array(
				self::WINDOWS_LINE_ENDING,
				self::MAC_LINE_ENDING,
			),
			self::UNIX_LINE_ENDING,
			$content
		);
		$contentArray = explode(self::UNIX_LINE_ENDING, $textContent); // Create array from text content
		return $contentArray;
	}

	/**
	 * @param array $array
	 * @return mixed
	 */
	public static function getNextValueFromArray(array &$array)
	{
		$element = each($array);
		if (is_array($element))
		{
			return $element['value'];
		}
		return false;
	}

	/**
	 * @param $parsing_errors
	 * @return array
	 */
	public function validateWebVttHeader($signature)
	{
		if (substr($signature, 0, 6) !== 'WEBVTT' && substr($signature, 0, 9) !== self::BOM_CODE.'WEBVTT')
		{
			$this->parsingErrors[] = 'Missing "WEBVTT" at the beginning of the file';
			return false;
		}

		if (strlen($signature) > 6 && substr($signature, 0, 6) === 'WEBVTT')
		{
			if (substr($signature, 0, 7) === 'WEBVTT ')
			{
				$fileDescription = substr($signature, 7);
				if (strpos($fileDescription, '-->') !== false)
				{
					$this->parsingErrors[] = 'File description must not contain "-->"';
					return false;
				}
				return true;
			}
			else
			{
				$this->parsingErrors[] = 'Invalid file header (must be "WEBVTT" with optional description)';
				return false;
			}
		} elseif (strlen($signature) > 9 && substr($signature, 0, 9) === self::BOM_CODE.'WEBVTT')
		{
			if (substr($signature, 0, 10) === self::BOM_CODE.'WEBVTT ')
			{
				$fileDescription = substr($signature, 10);
				if (strpos($fileDescription, '-->') !== false)
				{
					$this->parsingErrors[] = 'File description must not contain "-->"';
					return false;
				}
				return true;
			}
			else
			{
				$this->parsingErrors[] = 'Invalid file header (must be "WEBVTT" with optional description)';
				return false;
			}
		}
		return true;
	}

	/**
	 * @param $line
	 * @return string
	 * @throws Exception
	 */
	public static function handleTextLines($line)
	{
		$lines = array_map('trim', preg_split('/$\R?^/m', $line));
		$line = implode(self::UNIX_LINE_ENDING, $lines);
		return $line;
	}

	/**
	 * @param $timeStr
	 * @return string
	 */
	public function parseStrTTTime($timeStr)
	{
		$tabs = explode(':', $timeStr);
		$result = count($tabs);
		$timeInMilliseconds = null;
		if ($result == 2){
			$timeInMilliseconds = self::shortTimeFormatToInteger($timeStr) ;
		} elseif  ($result == 3){
			$timeInMilliseconds = kXml::timeToInteger($timeStr);
		} else {
			$this->parsingErrors[] = 'Error parsing time to milliseconds. invalid format for '.$timeStr;
		}
		return $timeInMilliseconds;
	}

	private static function shortTimeFormatToInteger($time)
	{
		$parts = explode(':', $time);
		if(!isset($parts[0]) || !is_numeric($parts[0]))
			return null;

		$ret = intval($parts[0]) * (60 * 1000);  // hours im milliseconds

		if(!isset($parts[1]))
			return $ret;
		if(!is_numeric($parts[1]))
			return null;

		if(!isset($parts[1]))
			return $ret;
		if(!is_numeric($parts[1]))
			return null;

		$ret += floatval($parts[1]) * 1000;  // seconds im milliseconds
		return round($ret);
	}

	/* (non-PHPdoc)
	 * @see kCaptionsContentManager::getContent()
	 */
	public function getContent($content)
	{
		$itemsData = null;
		try
		{
			$itemsData = $this->parseWebVTT($content);

			$content = '';
			foreach ($itemsData as $itemData)
			{
				foreach ($itemData['content'] as $curChunk)
				{
					$text = strip_tags($curChunk['text']);
					$content .= $text. ' ';
				}
			}
		} catch (Exception $e)
		{
			KalturaLog::err($e->getMessage());
			return null;
		}
		return trim(preg_replace('/\s+/', ' ', $content));
	}

	/**
	 * @return webVttCaptionsContentManager
	 */
	public static function get()
	{
		return new webVttCaptionsContentManager();
	}

	/**
	 * @param $content
	 * @return array
	 */
	public function parseWebVTT($content)
	{
		$this->headerInfo = array();
		$foundFirstTimeCode = false;

		$itemsData = array();

		$fileContentArray = self::getFileContentAsArray($content);

		// Parse signature.
		$header = self::getNextValueFromArray($fileContentArray);

		if (!$this->validateWebVttHeader($header))
		{
			KalturaLog::err("Error Parsing WebVTT file. The following errors were found while parsing the file: \n" . print_r($this->parsingErrors, true));
			return array();
		}

		$this->headerInfo[] = $header.self::UNIX_LINE_ENDING;
		// Parse text - ignore comments, ids, styles, notes, etc
		while (($line = self::getNextValueFromArray($fileContentArray)) !== false)
		{
			// Timecode.
			$matches = array();
			$timecode_match = preg_match(self::TIMECODE_PATTERN, $line, $matches);
			if ($timecode_match)
			{
				$foundFirstTimeCode = true;
				$start = $this->parseStrTTTime($matches[1]);
				$stop = $this->parseStrTTTime($matches[2]);
				$text = '';
				while (trim($line = self::getNextValueFromArray($fileContentArray)) !== '')
				{
					$line = $this->handleTextLines($line);
					$text .= $line . self::UNIX_LINE_ENDING;
				}

				$itemsData[] = array('startTime' => $start, 'endTime' => $stop, 'content' => array(array('text' => $text)));
			}elseif ($foundFirstTimeCode == false)
					$this->headerInfo[] = $line . self::UNIX_LINE_ENDING;

		};
		if (count($this->parsingErrors) > 0)
		{
			KalturaLog::err("Error Parsing WebVTT file. The following errors were found while parsing the file: \n" . print_r($this->parsingErrors, true));
			return array();
		}
		return $itemsData;
	}

}