<?php
/**
 * @package plugins.caption
 * @subpackage lib
 */
class webVttCaptionsContentManager extends kCaptionsContentManager
{

	const WEBVTT_TIMECODE_PATTERN = '#^((?:[0-9]{2}:)?[0-9]{2}:[0-9]{2}\.[0-9]{3}) --> ((?:[0-9]{2}:)?[0-9]{2}:[0-9]{2}\.[0-9]{3})( .*)?$#';

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
	 * @param $timeStr
	 * @return string
	 */
	public function parseWebvttStrTTTime($timeStr)
	{
		list ($timeInMilliseconds, $error) = kCaptionsContentManager::parseStrTTTime($timeStr);
		if($error)
			$this->parsingErrors[] = $error;
		return $timeInMilliseconds;
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
		$result = self::retrieveWebvttParsedCaptionsAndContent($content);
		if (empty($result))
			return array();
		else
			{
			list($itemsData, $editedContent) = $result;
			return $itemsData;
		}
	}


	public function retrieveWebvttParsedCaptionsAndContent($content, $clipStartTime = null, $clipEndTime = null){
		$this->headerInfo = array();
		$foundFirstTimeCode = false;
		$itemsData = array();

		$fillContent = false;
		if($clipStartTime && $clipEndTime)
			$fillContent = true;

		$fileContentArray = kCaptionsContentManager::getFileContentAsArray($content);

		// Parse signature.
		$header = kCaptionsContentManager::getNextValueFromArray($fileContentArray);
		if (!$this->validateWebVttHeader($header))
		{
			KalturaLog::err("Error Parsing WebVTT file. The following errors were found while parsing the file: \n" . print_r($this->parsingErrors, true));
			return array();
		}
		$this->headerInfo[] = $header. kCaptionsContentManager::UNIX_LINE_ENDING;
		$editedContent = $header. kCaptionsContentManager::UNIX_LINE_ENDING;
		$currentBlock = '';

		// parse text. ignore comments, ids, styles, notes, etc. for parsed captions only
		while (($line = kCaptionsContentManager::getNextValueFromArray($fileContentArray)) !== false)
		{
			// Timecode.
			$matches = array();
			$timecode_match = preg_match(self::WEBVTT_TIMECODE_PATTERN, $line, $matches);
			if ($timecode_match)
			{
				$foundFirstTimeCode = true;
				$startCaption = $this->parseWebvttStrTTTime($matches[1]);
				$endCaption = $this->parseWebvttStrTTTime($matches[2]);

				if($fillContent)
				{
					if (!kCaptionsContentManager::onTimeRange($startCaption, $endCaption, $clipStartTime, $clipEndTime))
					{
						$currentBlock = '';
						while (trim($line = kCaptionsContentManager::getNextValueFromArray($fileContentArray)) !== '')
							$line = kCaptionsContentManager::handleTextLines($line);
						continue;
					}

					$adjustedStartTime = kCaptionsContentManager::getAdjustedStartTime($startCaption, $clipStartTime);
					$adjustedEndTime = kCaptionsContentManager::getAdjustedEndTime($clipStartTime, $clipEndTime, $endCaption);

					$currentBlock .= kWebVTTGenerator::formatWebVTTTimeStamp($adjustedStartTime) . ' --> ' . kWebVTTGenerator::formatWebVTTTimeStamp($adjustedEndTime);

					$settings = isset($matches[3]) ? trim($matches[3]): '';
					$currentBlock .= ' ' . $settings . kCaptionsContentManager::UNIX_LINE_ENDING;
				}

				$text = '';
				while (trim($line = kCaptionsContentManager::getNextValueFromArray($fileContentArray)) !== '')
				{
					$line = kCaptionsContentManager::handleTextLines($line);
					$text .= $line . kCaptionsContentManager::UNIX_LINE_ENDING;
				}

				$currentBlock .= $text . kCaptionsContentManager::UNIX_LINE_ENDING;
				$editedContent .= $currentBlock;
				$currentBlock = '';

				$itemsData[] = array('startTime' => $startCaption, 'endTime' => $endCaption, 'content' => array(array('text' => $text)));
			}
			else
			{
				if ($foundFirstTimeCode == false)
					$this->headerInfo[] = $line . kCaptionsContentManager::UNIX_LINE_ENDING;
				$currentBlock .= $line . kCaptionsContentManager::UNIX_LINE_ENDING;
				if($line === '')
				{
					$editedContent .= $currentBlock;
					$currentBlock = '';
				}
			}
		};
		if (count($this->parsingErrors) > 0)
		{
			KalturaLog::err("Error Parsing WebVTT file. The following errors were found while parsing the file: \n" . print_r($this->parsingErrors, true));
			return array();
		}

		return array ($itemsData, $editedContent);
	}


	public function buildFile($content, $clipStartTime, $clipEndTime)
	{
		$result = self::retrieveWebvttParsedCaptionsAndContent($content, $clipStartTime, $clipEndTime);
		if (empty($result))
			return '';
		else
			{
			list($itemsData, $editedContent) = $result;
			return $editedContent;
		}
	}


}