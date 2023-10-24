<?php
/**
 * @package plugins.caption
 * @subpackage Scheduler
 */
class KAsyncCopyCaptions extends KJobHandlerWorker
{

	const START_TIME_ASC = "+startTime";

	/*
	 * @var KalturaCaptionSearchClientPlugin
	 */
	protected $captionSearchClientPlugin = null;

	/*
	* @var KalturaCaptionClientPlugin
	*/
	protected $captionClientPlugin = null;

	/**
	 * @param KSchedularTaskConfig $taskConfig
	 */
	public function __construct($taskConfig = null)
	{
		parent::__construct($taskConfig);

		$this->captionSearchClientPlugin = KalturaCaptionSearchClientPlugin::get(self::$kClient);
		$this->captionClientPlugin = KalturaCaptionClientPlugin::get(self::$kClient);
	}


	public static function getType()
	{
		return KalturaBatchJobType::COPY_CAPTIONS;
	}
	/**
	 * (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	protected function getJobType()
	{
		return KalturaBatchJobType::COPY_CAPTIONS;
	}

	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->copyCaptions($job, $job->data);
	}

	/**
	 * copy captions on specific time frame
	 * @throws kApplicativeException
	 */
	protected function copyCaptions(KalturaBatchJob $job, KalturaCopyCaptionsJobData $data)
	{
		$this->updateJob($job, "Start copying captions from source entries to [$data->entryId]", KalturaBatchJobStatus::PROCESSING);
		self::impersonate($job->partnerId);
		$this->copyCaptionsToDestination($data);
		self::unimpersonate();
		$this->closeJob($job, null, null, 'Finished copying captions', KalturaBatchJobStatus::FINISHED);
		return $job;
	}

	protected function copyCaptionsToDestination($data)
	{
		$sourceEntryIds = array_map(function ($x) {return $x->sourceEntryId;}, $data->clipsDescriptionArray);
		if (count(array_unique($sourceEntryIds)) > 1)
		{
			KalturaLog::debug("Copy captions for multi-source clips");
			$this->copyFromMultiSourceToDestination($data);
		}
		else
		{
			KalturaLog::debug("Copy captions for single source clips");
			$this->copyFromSingleSourceToDestination($data);
		}
	}

	protected function listCaptionAssets($entryId)
	{
		KalturaLog::info("Retrieve all caption assets for: [$entryId]");
		$filter = new KalturaAssetFilter();
		$filter->entryIdEqual = $entryId;
		try
		{
			$captionAssetsList = $this->captionClientPlugin->captionAsset->listAction($filter);
		}
		catch(Exception $e)
		{
			KalturaLog::info("Can't list caption assets for entry id [$entryId] " . $e->getMessage());
		}
		return $captionAssetsList->objects;
	}

	protected function retrieveCaptionAssetsOnlyFromSupportedTypes($originalCaptionAssets, $supportedFormats = array())
	{
		$unsupportedFormats = $this->getUnsupportedFormats();
		$originalCaptionAssetsFiltered = array();
		foreach ($originalCaptionAssets as $originalCaptionAsset)
		{
			if (count($supportedFormats) > 0 && !in_array($originalCaptionAsset->format, $supportedFormats))
			{
				continue;
			}
			else if (in_array($originalCaptionAsset->format, $unsupportedFormats))
			{
				// supported formats cannot be part of the defined unsupported formats
				continue;
			}
			$originalCaptionAssetsFiltered[] = $originalCaptionAsset;
		}
		$objectsNum = count($originalCaptionAssetsFiltered);
		KalturaLog::info("[$objectsNum] caption assets left after filtering");
		return $originalCaptionAssetsFiltered;
	}

	protected function getUnsupportedFormats()
	{
		$unsupportedFormats = array (CaptionType::CAP, CaptionType::SCC);
		return $unsupportedFormats;
	}

	protected function cloneCaptionAsset($targetEntryId, $originalCaptionAsset)
	{
		$captionAsset = new KalturaCaptionAsset();
		KalturaLog::info("Start copying properties from caption asset: [{$originalCaptionAsset->id}] to new caption asset on entryId: [$targetEntryId]");
		$propertiesToCopy = array("tags", "fileExt", "language", "label", "format", "isDefault", "displayOnPlayer", "accuracy");
		foreach ($propertiesToCopy as $property)
		{
			$captionAsset->$property = $originalCaptionAsset->$property;
		}
		$newCaption = $this->addCaptionAsset($targetEntryId, $captionAsset);
		return $newCaption;
	}

	protected function addCaptionAsset($targetEntryId, $newCaptionAsset = null)
	{
		$captionAsset = $newCaptionAsset;
		if (!$newCaptionAsset)
		{
			$captionAsset = new KalturaCaptionAsset();
		}
		try
		{
			$newCaption = $this->captionClientPlugin->captionAsset->add($targetEntryId , $captionAsset);
		}
		catch(Exception $e)
		{
			KalturaLog::info("Couldn't create new caption asset for entry id: [$targetEntryId]" . $e->getMessage());
		}
		return $newCaption;
	}

	protected function loadNewCaptionAssetFile($captionAssetId, $contentResource)
	{
		if (!kXml::isXMLValidContent($contentResource->content))
		{
			$contentResource->content = kXml::stripXMLInvalidChars($contentResource->content);
		}
		try
		{
			$updatedCaption = $this->captionClientPlugin->captionAsset->setContent($captionAssetId, $contentResource);
		}
		catch(Exception $e)
		{
			KalturaLog::info("Can't set content to caption asset id: [$captionAssetId]" . $e->getMessage());
			return null;
		}
		return $updatedCaption;
	}

	protected function createNewCaptionsFile($captionContent, $offset, $duration , $format, $fullCopy, $globalOffset)
	{
		KalturaLog::info("Format: [$format] offset: [$offset] and duration: [$duration]");
		$unsupported_formats = $this->getUnsupportedFormats();
		if($fullCopy)
		{
			KalturaLog::info("fullCopy mode - copy the content without editing");
		}
		else
		{
			KalturaLog::info("Copy only the relevant content");
			$endTime = $offset + $duration;
			if (!in_array($format, $unsupported_formats))
			{
				$captionsContentManager = kCaptionsContentManager::getCoreContentManager($format);
				$captionContent = $captionsContentManager->buildFile($captionContent, $offset, $endTime, $globalOffset);
			}
			else
			{
				KalturaLog::info("copying captions for format: [$format] is not supported");
			}
		}
		return $captionContent;
	}

	protected function getCaptionContent($captionAssetId)
	{
		KalturaLog::info("Retrieve caption assets content for captionAssetId: [$captionAssetId]");
		try
		{
			$captionAssetContentUrl= $this->captionClientPlugin->captionAsset->serve($captionAssetId);
			$captionAssetContent = KCurlWrapper::getContent($captionAssetContentUrl);
		}
		catch(Exception $e)
		{
			KalturaLog::info("Can't serve caption asset id [$captionAssetId] " . $e->getMessage());
		}
		return $captionAssetContent;
	}

	protected function getWebvttCaptionContent($captionAssetId)
	{
		KalturaLog::info("Retrieve caption assets content in WebVTT format for captionAssetId: [$captionAssetId]");
		try
		{
			$captionAssetContentUrl= $this->captionClientPlugin->captionAsset->serveWebVTT($captionAssetId, 0, -1);
			$captionAssetContent = KCurlWrapper::getContent($captionAssetContentUrl);
		}
		catch(Exception $e)
		{
			KalturaLog::info("Can't serve WebVTT content for caption asset id [$captionAssetId] " . $e->getMessage());
		}
		return $captionAssetContent;
	}

	/**
	 * @param KalturaCopyCaptionsJobData $data
	 * @throws kApplicativeException
	 */
	protected function copyFromSingleSourceToDestination(KalturaCopyCaptionsJobData $data)
	{
		$errorMsg = '';
		$clipDescriptionArray = $data->clipsDescriptionArray;
		$originalCaptionAssets = $this->listCaptionAssets($clipDescriptionArray[0]->sourceEntryId);
		if (!$data->fullCopy)
		{
			$originalCaptionAssets = $this->retrieveCaptionAssetsOnlyFromSupportedTypes($originalCaptionAssets);
		}
		foreach ($originalCaptionAssets as $originalCaptionAsset)
		{
			if ($originalCaptionAsset->status != KalturaCaptionAssetStatus::READY)
			{
				continue;
			}
			$newCaptionAsset = $this->cloneCaptionAsset($data->entryId, $originalCaptionAsset);
			$newCaptionAssetResource = new KalturaStringResource();
			$this->clipAndConcatSub($data, $clipDescriptionArray, $originalCaptionAsset, $newCaptionAsset, $newCaptionAssetResource, $errorMsg);
			$updatedCaption = $this->loadNewCaptionAssetFile($newCaptionAsset->id, $newCaptionAssetResource);
			if (!$updatedCaption)
			{
				throw new kApplicativeException(KalturaBatchJobAppErrors::MISSING_ASSETS, "Created caption asset with id: [$newCaptionAsset->id], but couldn't load the new captions file to it");
			}
		}
		if ($errorMsg)
		{
			throw new kApplicativeException(KalturaBatchJobAppErrors::MISSING_ASSETS, $errorMsg);
		}
	}

	/**
	 * @param KalturaCopyCaptionsJobData $data
	 * @throws kApplicativeException
	 */
	protected function copyFromMultiSourceToDestination(KalturaCopyCaptionsJobData $data)
	{
		$errorMsg = '';
		$clipDescriptionArray = $data->clipsDescriptionArray;
		$languageEntryCaptionAsset = $this->buildLanguageEntryCaption($clipDescriptionArray);
		foreach ($languageEntryCaptionAsset as $language => $entryCaptionAsset)
		{
			KalturaLog::debug("Copy captions for language [$language]");
			$captionAsset = $this->getNewCaptionAsset($entryCaptionAsset, $language);
			$newCaptionAsset = $this->addCaptionAsset($data->entryId, $captionAsset);
			$newCaptionAssetResource = new KalturaStringResource();
			foreach ($clipDescriptionArray as $clipDescription)
			{
				$originalCaptionAsset = null;
				$sourceEntryId = $clipDescription->sourceEntryId;
				if(isset($entryCaptionAsset[$sourceEntryId]))
				{
					$originalCaptionAsset = $entryCaptionAsset[$sourceEntryId];
				}
				if(!$originalCaptionAsset)
				{
					continue;
				}
				$this->clipAndConcatSub($data, array($clipDescription), $originalCaptionAsset, $newCaptionAsset, $newCaptionAssetResource, $errorMsg);
			}
			$updatedCaption = $this->loadNewCaptionAssetFile($newCaptionAsset->id, $newCaptionAssetResource);
			if (!$updatedCaption)
			{
				throw new kApplicativeException(KalturaBatchJobAppErrors::MISSING_ASSETS, "Created caption asset with id: [$newCaptionAsset->id], but couldn't load the new captions file to it");
			}
		}
	}

	protected function getNewCaptionAsset($entryCaptionAssetArray, $language)
	{
		$captionAsset = new KalturaCaptionAsset();
		foreach ($entryCaptionAssetArray as $entryCaptionAsset)
		{
			if(!$captionAsset->format || $captionAsset->format != CaptionType::WEBVTT && $entryCaptionAsset->format == CaptionType::WEBVTT)
			{
				$captionAsset->format = $entryCaptionAsset->format;
				$captionAsset->fileExt = $entryCaptionAsset->fileExt;
			}
		}
		$captionAsset->language = $language;
		$captionAsset->label = $language;
		return $captionAsset;
	}

	protected function buildLanguageEntryCaption($clipDescriptionArray)
	{
		$languageEntryCaptionAsset = array();
		$entryCaptionAssets = array();
		foreach ($clipDescriptionArray as $clipDescription)
		{
			$sourceEntryId = $clipDescription->sourceEntryId;
			if (!isset($entryCaptionAssets[$sourceEntryId]))
			{
				$captionAssets = $this->listCaptionAssets($sourceEntryId);
				$supportedCaptionTypes = array(CaptionType::WEBVTT, CaptionType::SRT);
				$entryCaptionAssets[$sourceEntryId] = $this->retrieveCaptionAssetsOnlyFromSupportedTypes($captionAssets, $supportedCaptionTypes);
				foreach ($entryCaptionAssets[$sourceEntryId] as $captionAsset)
				{
					$language = $captionAsset->language;
					if (!isset($languageEntryCaptionAsset[$language]))
					{
						$languageEntryCaptionAsset[$language] = array();
					}
					if (!isset($languageEntryCaptionAsset[$language][$sourceEntryId]))
					{
						$languageEntryCaptionAsset[$language][$sourceEntryId] = array();
					}
					$currentCaptionAsset = $languageEntryCaptionAsset[$language][$sourceEntryId];
					if ($currentCaptionAsset && $currentCaptionAsset->updatedAt > $captionAsset->updatedAt)
					{
						continue;
					}
					$languageEntryCaptionAsset[$language][$sourceEntryId] = $captionAsset;
				}
			}
		}
		return $languageEntryCaptionAsset;
	}

	/**
	 * @param KalturaCopyCaptionsJobData $data
	 * @param $clipDescriptionArray
	 * @param $originalCaptionAsset
	 * @param $newCaptionAsset
	 * @param $newCaptionAssetResource
	 * @param string $errorMsg
	 */
	protected function clipAndConcatSub(KalturaCopyCaptionsJobData $data, $clipDescriptionArray, $originalCaptionAsset, $newCaptionAsset, $newCaptionAssetResource, &$errorMsg)
	{
		$captionAssetId = $originalCaptionAsset->id;
		if($originalCaptionAsset->format != CaptionType::WEBVTT && $newCaptionAsset->format == CaptionType::WEBVTT)
		{
			$captionContent = $this->getWebvttCaptionContent($captionAssetId);
		}
		else
		{
			$captionContent = $this->getCaptionContent($captionAssetId);
		}

		KalturaLog::info("Create new caption file based on captionAssetId:[$captionAssetId]");
		foreach ($clipDescriptionArray as $clipDescription)
		{
			$toAppend = $this->createNewCaptionsFile($captionContent, $clipDescription->startTime,
				$clipDescription->duration, $newCaptionAsset->format, $data->fullCopy, $clipDescription->offsetInDestination);
			if ($toAppend && $newCaptionAssetResource->content)
			{
				$captionsContentManager = kCaptionsContentManager::getCoreContentManager($newCaptionAsset->format);
				$newCaptionAssetResource->content = $captionsContentManager->merge($newCaptionAssetResource->content, $toAppend);
			}
			else if(!$newCaptionAssetResource->content)
			{
				$newCaptionAssetResource->content = $toAppend;
			}
			if (is_null($toAppend))
			{
				$errorMsg = "Couldn't create new captions file for captionAssetId: [$originalCaptionAsset->id] and format: [$newCaptionAsset->format]";
			}
		}
	}

}