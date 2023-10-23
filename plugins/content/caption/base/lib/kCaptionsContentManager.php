<?php
/**
 * @package plugins.caption
 * @subpackage lib
 */
abstract class kCaptionsContentManager
{
	const UNIX_LINE_ENDING = "\n";
	const MAC_LINE_ENDING = "\r";
	const WINDOWS_LINE_ENDING = "\r\n";

	public function __construct()
	{
		
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

	public static function extractContent(CaptionAsset $captionAsset)
	{
		$syncKey = $captionAsset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		$content = kFileSyncUtils::file_get_contents($syncKey, true, false);
		if (!$content)
		{
			KalturaLog::crit("Failed to retrieve the content for caption ID {$captionAsset->getId()}");
			return false;
		}

		$content = str_replace(
			array(
				self::WINDOWS_LINE_ENDING,
				self::MAC_LINE_ENDING,
			),
			self::UNIX_LINE_ENDING,
			$content
		);

		return $content;
	}

	/**
	 * @param array $array
	 * @return mixed
	 */
	protected static function getNextValueFromArray(array &$array)
	{
		$element = current($array);
		next($array);
		return $element;
	}

	/**
	 * @param $line
	 * @return string
	 * @throws Exception
	 */
	public static function handleTextLines($line)
	{
		$lines = array_map('trim', preg_split('/$\R?^/m', $line));
		$line = implode(kCaptionsContentManager::UNIX_LINE_ENDING, $lines);
		return $line;
	}

	/**
	 * @param string $content
	 * @return array
	 */
	public abstract function parse($content);

	/**
	 * @param string $content
	 * @return bool
	 */
	public function validate($content)
	{
		return $this->parse($content) ? true : false;
	}

	/**
	 * @param string $content
	 * @return string
	 */
	public abstract function getContent($content);

	/**
	 * @param string $content
	 * @param int $clipStartTime
	 * @param int $clipEndTime
	 * @param int $globalOffset
	 * @return array
	 */
	public abstract function buildFile($content, $clipStartTime, $clipEndTime, $globalOffset = 0);

	/**
	 * @param array $matches
	 * @param int $clipStartTime
	 * @param int $clipEndTime
	 * @param int $globalOffset
	 * @return string
	 */
	protected abstract function createAdjustedTimeLine($matches, $clipStartTime, $clipEndTime, $globalOffset);


	/**
	 * @param string $content
	 * @param string $toAppend
	 * @return string
	 */
	public abstract function merge($content, $toAppend);



	/**
	 * @param CaptionType $type
	 * @return kCaptionsContentManager
	 */
	public static function getCoreContentManager($type)
	{
		switch($type)
		{
			case CaptionType::SRT:
				return srtCaptionsContentManager::get(); 
				
			case CaptionType::DFXP:
				return dfxpCaptionsContentManager::get();

			case CaptionType::WEBVTT:
				return webVttCaptionsContentManager::get();
				
			case CaptionType::CAP:
				return capCaptionsContentManager::get();

			default:
				return KalturaPluginManager::loadObject('kCaptionsContentManager', $type);
		}
	}
	
	/**
	 * @param KalturaCaptionType $type
	 * @return kCaptionsContentManager
	 */
	public static function getApiContentManager($type)
	{
		switch($type)
		{
			case KalturaCaptionType::SRT:
				return srtCaptionsContentManager::get(); 
				
			case KalturaCaptionType::DFXP:
				return dfxpCaptionsContentManager::get();

			case CaptionType::WEBVTT:
				return webVttCaptionsContentManager::get();

			default:
				return KalturaPluginManager::loadObject('kCaptionsContentManager', $type);
		}
	}

	/**
	 * @param $captionAsset
	 * @param $fileLocation
	 * @param null $key
	 * @return BatchJob
	 */
	public static function addParseMultiLanguageCaptionAssetJob($captionAsset, $fileLocation, $key = null)
	{
		$batchJob = new BatchJob();

		$id = $captionAsset->getId();
		$entryId = $captionAsset->getEntryId();

		$jobData = new kParseMultiLanguageCaptionAssetJobData();
		$jobData->setMultiLanaguageCaptionAssetId($id);
		$jobData->setEntryId($entryId);
		$jobData->setFileLocation($fileLocation);
		$jobData->setFileEncryptionKey($key);

		$jobType = CaptionPlugin::getBatchJobTypeCoreValue(ParseMultiLanguageCaptionAssetBatchType::PARSE_MULTI_LANGUAGE_CAPTION_ASSET);
		$batchJob->setObjectType(BatchJobObjectType::ASSET);
		$batchJob->setEntryId($entryId);
		$batchJob->setPartnerId($captionAsset->getPartnerId());
		$batchJob->setObjectId($id);

		return kJobsManager::addJob($batchJob, $jobData, $jobType);
	}

	/***
	 * @param $captionAsset
	 * @param $fromType
	 * @param $toType
	 * @return BatchJob
	 * @throws Exception
	 */
	public static function addConvertCaptionAssetJob($captionAsset, $fromType, $toType)
	{
		$batchJob = new BatchJob();

		$syncKey = $captionAsset->getSyncKey(CaptionAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		$fileSync = kFileSyncUtils::getLocalFileSyncForKey($syncKey);
		$fileLocation = $fileSync->getFullPath();

		$id = $captionAsset->getId();
		$entryId = $captionAsset->getEntryId();

		$jobData = new kConvertCaptionAssetJobData();
		$jobData->setCaptionAssetId($id);
		$jobData->setFileLocation($fileLocation);
		$jobData->setFileEncryptionKey($fileSync->getEncryptionKey());
		$jobData->setFromType($fromType);
		$jobData->setToType($toType);

		$jobType = CaptionPlugin::getBatchJobTypeCoreValue(ConvertCaptionAssetBatchType::CONVERT_CAPTION_ASSET);
		$batchJob->setObjectType(BatchJobObjectType::ASSET);
		$batchJob->setEntryId($entryId);
		$batchJob->setPartnerId($captionAsset->getPartnerId());
		$batchJob->setObjectId($id);

		return kJobsManager::addJob($batchJob, $jobData, $jobType);
	}




	/**
	 * @param $timeStr
	 * @return array
	 */
	public static function parseStrTTTime($timeStr)
	{
		$error = null;
		$tabs = explode(':', $timeStr);
		$result = count($tabs);
		$timeInMilliseconds = null;
		if ($result == 2)
			$timeInMilliseconds = self::shortTimeFormatToInteger($timeStr) ;
		elseif  ($result == 3)
			$timeInMilliseconds = kXml::timeToInteger($timeStr);
		else
			$error = 'Error parsing time to milliseconds. invalid format for '.$timeStr;

		return array ($timeInMilliseconds, $error);
	}

	/**
	 * @param $time
	 * @return string
	 */
	public function parseCaptionTime($time)
	{
		list($captionTime, $error) = kCaptionsContentManager::parseStrTTTime($time);
		return $captionTime;
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


	public function createCaptionsFile($content, $clipStartTime, $clipEndTime, $timeCode, $globalOffset)
	{
		$newFileContent = '';
		$originalFileContentArray = kCaptionsContentManager::getFileContentAsArray($content);
		while (($line = kCaptionsContentManager::getNextValueFromArray($originalFileContentArray)) !== false)
		{
			$currentBlock = '';
			$shouldAddBlockToNewFile = true;
			while (trim($line) !== '' and $line !== false)
			{
				$matches = array();
				$timecode_match = preg_match($timeCode, $line, $matches);
				if ($timecode_match)
				{
					$adjustedTimeLine = $this->createAdjustedTimeLine($matches, $clipStartTime, $clipEndTime, $globalOffset);
					if($adjustedTimeLine)
					{
						$currentBlock .= $adjustedTimeLine;
					}
					else
					{
						$shouldAddBlockToNewFile = false;
					}
				}
				else
				{
					$currentBlock .= $line . kCaptionsContentManager::UNIX_LINE_ENDING;
				}
				$line = kCaptionsContentManager::getNextValueFromArray($originalFileContentArray);
			}
			$shouldAddBlockToNewFile = $shouldAddBlockToNewFile && $currentBlock && trim($currentBlock) !== '';
			if($shouldAddBlockToNewFile)
			{
				$newFileContent = $this->addBlockToNewFile($newFileContent, $currentBlock);
			}
		}
		return $newFileContent;
	}

	protected function addBlockToNewFile($newFileContent, $currentBlock)
	{
		$newFileContent .= $currentBlock . kCaptionsContentManager::UNIX_LINE_ENDING;
		return $newFileContent;
	}

	public static function convertSrtToWebvtt($content)
	{
		$newFileContent = webVttCaptionsContentManager::WEBVTT_PATTERN . kCaptionsContentManager::UNIX_LINE_ENDING;
		$srtContentManager = new srtCaptionsContentManager();
		$originalFileContentArray = kCaptionsContentManager::getFileContentAsArray($content);
		while (($line = kCaptionsContentManager::getNextValueFromArray($originalFileContentArray)) !== false)
		{
			$currentBlock = '';
			while ($line !== false && trim($line) !== '')
			{
				$currentBlock .= kCaptionsContentManager::srtToWebvttTimeLine($srtContentManager, $line);
				$line = kCaptionsContentManager::getNextValueFromArray($originalFileContentArray);
			}
			$newFileContent .= kCaptionsContentManager::removeBlockIdentifier($currentBlock);
		}
		return $newFileContent;
	}

	protected static function removeBlockIdentifier($blockStr)
	{
		$blockArray = explode(kCaptionsContentManager::UNIX_LINE_ENDING, $blockStr);
		array_shift($blockArray);
		return implode(kCaptionsContentManager::UNIX_LINE_ENDING, $blockArray);
	}

	protected static function srtToWebvttTimeLine($srtContentManager, $line)
	{
		$matches = array();
		$timecode_match = preg_match(srtCaptionsContentManager::SRT_TIMECODE_PATTERN, $line, $matches);
		if ($timecode_match)
		{
			$startCaption = $srtContentManager->parseCaptionTime($matches[1]);
			$endCaption = $srtContentManager->parseCaptionTime($matches[2]);
			$timeLine = kWebVTTGenerator::formatWebVTTTimeStamp($startCaption) . ' --> ' . kWebVTTGenerator::formatWebVTTTimeStamp($endCaption);
			$line = kCaptionsContentManager::UNIX_LINE_ENDING . $timeLine;
		}
		return $line . kCaptionsContentManager::UNIX_LINE_ENDING;
	}
}
