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
		$this->updateJob($job, "Start copying captions from [$data->sourceEntryId] to [$data->entryId]", KalturaBatchJobStatus::PROCESSING);
		self::impersonate($job->partnerId);
		$originalCaptionAssets = $this->getAllCaptionAsset($data->sourceEntryId);
		if(!$data->fullCopy)
			$originalCaptionAssets = $this->retrieveCaptionAssetsOnlyFromSupportedTypes($originalCaptionAssets);
		foreach ($originalCaptionAssets as $originalCaptionAsset)
		{
			$newCaptionAsset = $this->cloneCaption($data->entryId, $originalCaptionAsset);
			$newCaptionAssetResource = $this->createNewCaptionsFile($originalCaptionAsset->id, $data->offset, $data->duration, $newCaptionAsset->format, $data->fullCopy);
			if (!$newCaptionAssetResource)
				throw new kApplicativeException(KalturaBatchJobAppErrors::MISSING_ASSETS, "Couldn't create new captions file. Empty resource returned for captionAssetId: [$originalCaptionAsset->id] and format: [$newCaptionAsset->format]");
			$updatedCaption = $this->loadNewCaptionAssetFile($newCaptionAsset->id, $newCaptionAssetResource);
			if (!$updatedCaption)
				throw new kApplicativeException(KalturaBatchJobAppErrors::MISSING_ASSETS ,"New caption file was not created for asset: [$newCaptionAsset->id]");
		}
		self::unimpersonate();

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
		$unsupportedFormats = array (CaptionType::CAP, CaptionType::WEBVTT);
		$originalCaptionAssetsFiltered = array();
		foreach ($originalCaptionAssets as $originalCaptionAsset) {
			if (!in_array($originalCaptionAsset->format, $unsupportedFormats))
				array_push($originalCaptionAssetsFiltered, $originalCaptionAsset);
		}
		$objectsNum = count($originalCaptionAssetsFiltered);
		KalturaLog::info("[$objectsNum] caption assets left after filtering");
		return $originalCaptionAssetsFiltered;
	}

	private function cloneCaption($targetEntryId, $originalCaptionAsset)
	{
		KalturaLog::info("Start copying properties from caption asset: [{$originalCaptionAsset->id}] to new caption asset on entryId: [$targetEntryId]");
		$captionAsset = new KalturaCaptionAsset();
		$propertiesToCopy = array("tags", "fileExt", "language", "label", "format");
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

	private function getCaptionAssetItems($captionAssetId, $offset, $duration){
		$endTime = $offset + $duration;
		$captionItemsStartedBefore = $this->getCaptionAssetItemsStartedBeforeClipStartTime($captionAssetId, $offset, $endTime);
		$captionItemsStartedOnRange = $this->getCaptionAssetItemsInClipRange($captionAssetId, $offset, $endTime);
		$allCaptions = array_merge($captionItemsStartedBefore->objects, $captionItemsStartedOnRange->objects);
		return $allCaptions;
	}


	private function getCaptionAssetItemsStartedBeforeClipStartTime($captionAssetId, $offset, $endTime)
	{
		KalturaLog::info("Retrieve caption asset items associated with captionAssetId: [$captionAssetId] starting before [$offset] and ending after[$offset]");
		$filter = new KalturaCaptionAssetItemFilter();
		$filter->endTimeGreaterThanOrEqual = $offset;
		$filter->startTimeLessThanOrEqual = $offset;
		$filter->orderBy = self::START_TIME_ASC;
		try
		{
			$captionItemsStartedBefore = $this->captionSearchClientPlugin->captionAssetItem->listAction($captionAssetId, $filter);
		}
		catch(Exception $e)
		{
			KalturaLog::info("Can't list caption assets items for caption asset id: [$captionAssetId]" . $e->getMessage());
		}
		return $captionItemsStartedBefore;
	}

	private function getCaptionAssetItemsInClipRange($captionAssetId, $offset, $endTime){
		KalturaLog::info("Retrieve caption asset items associated with captionAssetId: [$captionAssetId] starting in the range [$offset - $endTime]");
		$filter = new KalturaCaptionAssetItemFilter();
		$filter->startTimeGreaterThanOrEqual = $offset;
		$filter->startTimeLessThanOrEqual = $endTime;
		$filter->orderBy = self::START_TIME_ASC;
		try
		{
			$captionItemsStartedOnClipRange = $this->captionSearchClientPlugin->captionAssetItem->listAction($captionAssetId, $filter);
		}
		catch(Exception $e)
		{
			KalturaLog::info("Can't list caption assets items for caption asset id: [$captionAssetId]" . $e->getMessage());
		}
		return $captionItemsStartedOnClipRange;
	}

	private function loadNewCaptionAssetFile($captionAssetId, $contentResource){
		try
		{
			$updatedCaption = $this->captionClientPlugin->captionAsset->setContent($captionAssetId, $contentResource);
		}
		catch(Exception $e)
		{
			KalturaLog::info("Can't set content to caption asset id: [$captionAssetId]" . $e->getMessage());
		}
		return $updatedCaption;
	}


	private function createNewCaptionsFile($captionAssetId, $offset, $duration , $format, $fullCopy){
		KalturaLog::info("Create new caption file based on captionAssetId:[$captionAssetId] in format: [$format] with offset: [$offset] and duration: [$duration]");
		$captionContent = "";

		if($fullCopy)
		{
			KalturaLog::info("fullCopy mode - copy the content of captionAssetId: [$captionAssetId] without editing");
			$captionContent = $this->getCaptionContent($captionAssetId);
		}
		else {
			KalturaLog::info("Copy only the relevant content of captionAssetId: [$captionAssetId]");
			$endTime = $offset + $duration;
			switch ($format)
			{
				case CaptionType::SRT:
					$originalCaptionAssetItems = $this->getCaptionAssetItems($captionAssetId, $offset, $duration);
					$srtContentManager = new srtCaptionsContentManager();
					$captionContent = $srtContentManager->buildSrtFile($originalCaptionAssetItems, $offset);
					break;

				case CaptionType::DFXP:
					$captionContent = $this->getCaptionContent($captionAssetId);
					$dfxpContentManager = new dfxpCaptionsContentManager();
					$captionContent = $dfxpContentManager->buildDfxpFile($captionContent, $offset, $endTime);
					break;

				default:
					KalturaLog::info("copying captions for format: [$format] is not supported");
			}
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