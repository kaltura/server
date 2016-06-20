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

	const TIMECODE_PATTERN = '#^((?:[0-9]{2}:)?[0-9]{2}:[0-9]{2}.[0-9]{3}) --> ((?:[0-9]{2}:)?[0-9]{2}:[0-9]{2}.[0-9]{3})( .*)?$#';

	/**
	 * @var array
	 */
	protected $parsing_errors;

	/* (non-PHPdoc)
	 * @see kCaptionsContentManager::parse()
	 */
	public function parse($content)
	{
		$itemsData = array();

		$this->parsing_errors = array();

		$fileContentArray = $this->getFileContentAsArray($content);
		$i = 2; //starting line after WEBVTT header

		// Parse signature.
		$signature = $this->getNextValueFromArray($fileContentArray);

		if (!$this->validateWebVttHeader($signature))
			return $this->handleErrors();

		$line = $this->getNextValueFromArray($fileContentArray);
		while ($line === '')
		{
			$line = $this->getNextValueFromArray($fileContentArray);
			++$i;
		}

		// Parse text - ignore comments and ids
		do
		{
			if (strpos($line, 'NOTE') === 0)
			{
				// Comment continues until there is a blank line. - according to WebVTT standard
				++$i;
				while (trim($line = $this->getNextValueFromArray($fileContentArray)) !== '')
					$i++;

				continue;
			}

			// Timecode.
			$matches = array();
			$timecode_match = preg_match(self::TIMECODE_PATTERN, $line, $matches);
			if ($timecode_match)
			{
				$start = $this->parseStrTTTime($matches[1]);
				$stop = $this->parseStrTTTime($matches[2]);
				$text = '';
				++$i;
				while (trim($line = $this->getNextValueFromArray($fileContentArray)) !== '')
				{
					$line = $this->handleTextLines($line);
					$text .= $line . self::UNIX_LINE_ENDING;
					++$i;
				}

				$itemsData[] = array('startTime' => $start, 'endTime' => $stop, 'content' => array(array('text' => $text)));
			} elseif ($line !== '')
			{
				// Suppose what not empty line before timeline is id.
			} else
				$parsing_errors[] = 'Malformed Format for WebVTT file detected at line ' . $i;

			++$i;
		} while (($line = $this->getNextValueFromArray($fileContentArray)) !== false);
		if (count($this->parsing_errors) > 0)
			return $this->handleErrors();

		return $itemsData;
	}

	protected function handleErrors()
	{
		KalturaLog::err("Error Parsing WebVTT file. The following errors were found while parsing the file: \n" . print_r($this->parsing_errors, true));
		return array();
	}

	/**
	 * @param content
	 * @return array
	 */
	protected function getFileContentAsArray($content)
	{
		$TextContent = str_replace( // So we change line endings to one format
			array(
				self::WINDOWS_LINE_ENDING,
				self::MAC_LINE_ENDING,
			),
			self::UNIX_LINE_ENDING,
			$content
		);
		$contentArray = explode(self::UNIX_LINE_ENDING, $TextContent); // Create array from text content
		return $contentArray;
	}

	/**
	 * @param array $array
	 * @return mixed
	 */
	protected function getNextValueFromArray(array &$array)
	{
		$element = each($array);
		if (is_array($element))
		{
			return $element['value'];
		}
		return false;
	}

	protected function parseTextLines($text)
	{
		if (trim($text) === '')
		{
			$this->parsing_errors[] = 'No text provided While parsing line for WebVTT file';
			return array();
		}
		return array_map('trim', preg_split('/$\R?^/m', $text));
	}

	/**
	 * @param $parsing_errors
	 * @return array
	 */
	protected function validateWebVttHeader($signature)
	{
		if (substr($signature, 0, 6) !== 'WEBVTT')
		{
			$this->parsing_errors[] = 'Missing "WEBVTT" at the beginning of the file';
		}
		if (strlen($signature) > 6)
		{
			if (substr($signature, 0, 7) === 'WEBVTT ')
			{
				$fileDescription = substr($signature, 7);
				if (strpos($fileDescription, '-->') !== false)
				{
					$this->parsing_errors[] = 'File description must not contain "-->"';
					return false;
				}
				return true;
			} else
			{
				$this->parsing_errors[] = 'Invalid file header (must be "WEBVTT" with optional description)';
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
	protected function handleTextLines($line)
	{
		if ($line !== '')
		{
			$lines = $this->parseTextLines($line);
			$line = implode(self::UNIX_LINE_ENDING, $lines);
			$line = strip_tags($line);
		} else
			$line = '';

		return $line;
	}

	/**
	 * @param $timeStr
	 * @return string
	 */
	private function parseStrTTTime($timeStr)
	{
		$matches = null;
		if(preg_match('/(\d+)s/', $timeStr))
			return intval($matches[1]) * 1000;

		return kXml::timeToInteger($timeStr);
	}


	/* (non-PHPdoc)
	 * @see kCaptionsContentManager::getContent()
	 */
	public function getContent($content)
	{
		$itemsData = null;
		try
		{
			$itemsData = $this->parse($content);

			$content = '';
			foreach ($itemsData as $itemData)
			{
				foreach ($itemData['content'] as $curChunk)
					$content .= $curChunk['text'] . ' ';
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

}