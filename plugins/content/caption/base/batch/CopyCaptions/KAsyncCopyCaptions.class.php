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
	private $captionSearchClientPlugin = null;

	/*
	* @var KalturaCaptionClientPlugin
	*/
	private $captionClientPlugin = null;

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
	 */
	private function copyCaptions(KalturaBatchJob $job, KalturaCopyCaptionsJobData $data)
	{
		$errorMsg = '';
		$this->updateJob($job, "Start copying captions from [$data->sourceEntryId] to [$data->entryId]", KalturaBatchJobStatus::PROCESSING);
		self::impersonate($job->partnerId);
		$originalCaptionAssets = $this->getAllCaptionAsset($data->sourceEntryId);
		if(!$data->fullCopy)
			$originalCaptionAssets = $this->retrieveCaptionAssetsOnlyFromSupportedTypes($originalCaptionAssets);
		foreach ($originalCaptionAssets as $originalCaptionAsset)
		{
			if ($originalCaptionAsset->status != KalturaCaptionAssetStatus::READY)
				continue;
			$newCaptionAsset = $this->cloneCaption($data->entryId, $originalCaptionAsset);
			$newCaptionAssetResource = $this->createNewCaptionsFile($originalCaptionAsset->id, $data->offset, $data->duration, $newCaptionAsset->format, $data->fullCopy);
			if (!$newCaptionAssetResource)
			{
				$errorMsg = "Couldn't create new captions file for captionAssetId: [$originalCaptionAsset->id] and format: [$newCaptionAsset->format]";
				continue;
			}
			$updatedCaption = $this->loadNewCaptionAssetFile($newCaptionAsset->id, $newCaptionAssetResource);
			if (!$updatedCaption)
				$errorMsg = "Created caption asset with id: [$newCaptionAsset->id], but couldn't load the new captions file to it";
		}
		self::unimpersonate();

		if($errorMsg)
			throw new kApplicativeException(KalturaBatchJobAppErrors::MISSING_ASSETS, $errorMsg);

		$this->closeJob($job, null, null, 'Finished copying captions', KalturaBatchJobStatus::FINISHED);
		return $job;
	}


	private function getAllCaptionAsset($entryId)
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

	private function retrieveCaptionAssetsOnlyFromSupportedTypes($originalCaptionAssets)
	{
		$unsupportedFormats = $this->getUnsupportedFormats();
		$originalCaptionAssetsFiltered = array();
		foreach ($originalCaptionAssets as $originalCaptionAsset)
		{
			if (!in_array($originalCaptionAsset->format, $unsupportedFormats))
				array_push($originalCaptionAssetsFiltered, $originalCaptionAsset);
		}
		$objectsNum = count($originalCaptionAssetsFiltered);
		KalturaLog::info("[$objectsNum] caption assets left after filtering");
		return $originalCaptionAssetsFiltered;
	}


	private function getUnsupportedFormats()
	{
		$unsupportedFormats = array (CaptionType::CAP);
		return $unsupportedFormats;
	}

	private function cloneCaption($targetEntryId, $originalCaptionAsset)
	{
		KalturaLog::info("Start copying properties from caption asset: [{$originalCaptionAsset->id}] to new caption asset on entryId: [$targetEntryId]");
		$captionAsset = new KalturaCaptionAsset();
		$propertiesToCopy = array("tags", "fileExt", "language", "label", "format","isDefault");
		foreach ($propertiesToCopy as $property)
			$captionAsset->$property = $originalCaptionAsset->$property;
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

	private function loadNewCaptionAssetFile($captionAssetId, $contentResource)
	{
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


	private function createNewCaptionsFile($captionAssetId, $offset, $duration , $format, $fullCopy){
		KalturaLog::info("Create new caption file based on captionAssetId:[$captionAssetId] in format: [$format] with offset: [$offset] and duration: [$duration]");
		$captionContent = "";

		$unsupported_formats = $this->getUnsupportedFormats();

		if($fullCopy)
		{
			KalturaLog::info("fullCopy mode - copy the content of captionAssetId: [$captionAssetId] without editing");
			$captionContent = $this->getCaptionContent($captionAssetId);
		}
		else
		{
			KalturaLog::info("Copy only the relevant content of captionAssetId: [$captionAssetId]");
			$endTime = $offset + $duration;

			if (!in_array($format, $unsupported_formats))
			{
				$captionContent = $this->getCaptionContent($captionAssetId);
				$captionsContentManager = kCaptionsContentManager::getCoreContentManager($format);
				$captionContent = $captionsContentManager->buildFile($captionContent, $offset, $endTime);
			}
			else
				KalturaLog::info("copying captions for format: [$format] is not supported");
		}

		$contentResource = new KalturaStringResource();
		$contentResource->content = $captionContent;
		return $contentResource;
	}


	private function getCaptionContent($captionAssetId)
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

}