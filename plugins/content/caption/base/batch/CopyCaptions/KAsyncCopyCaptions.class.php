<?php
/**
 * @package Scheduler
 * @subpackage Copy
 */

/**
 * Will copy objects and add them
 * according to the suppolied engine type and filter
 *
 * @package Scheduler
 * @subpackage Copy
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
		$this->updateJob($job, "Start copying captions from [$data->sourceEntryId] to [$data->entryId]", KalturaBatchJobStatus::QUEUED);
		$this->captionSearchClientPlugin = KalturaCaptionSearchClientPlugin::get(self::$kClient);
		$this->captionClientPlugin = KalturaCaptionClientPlugin::get(self::$kClient);

		self::impersonate($job->partnerId);
		$originalCaptionAssets = $this->getAllCaptionAsset($data->sourceEntryId);
		$unsupportedFormats = array (CaptionType::CAP, CaptionType::WEBVTT);

		foreach ($originalCaptionAssets->objects as $originalCaptionAsset){
			if(in_array($originalCaptionAsset->format, $unsupportedFormats)) {
				KalturaLog::info("Original caption asset [$originalCaptionAsset->id] from an unsupported format [$originalCaptionAsset->format], skipping to next item");
				continue;
			}

			$newCaptionAsset = $this->cloneCaption($data->entryId, $originalCaptionAsset);

			$newCaptionAssetResource = $this->createNewCaptionsFile($originalCaptionAsset->id, $data->offset, $data->duration, $newCaptionAsset->format);
			if (!$newCaptionAssetResource)
				throw new kApplicativeException(KalturaBatchJobAppErrors::MISSING_ASSETS, "Error while trying to copy captions - empty resource");

			$updatedCaption = $this->loadNewCaptionAssetFile($newCaptionAsset->id, $newCaptionAssetResource);
			if (!$updatedCaption)
				throw new kApplicativeException(KalturaBatchJobAppErrors::MISSING_ASSETS ,"Error while trying to copy captions");
		}
		self::unimpersonate();

		$this->closeJob($job, null, null, "Finished copying captions", KalturaBatchJobStatus::FINISHED);
		return $job;
	}


	private function getAllCaptionAsset($entryId)
	{
		KalturaLog::info("Retrieve all caption assets for: [$entryId]");
		$filter = new KalturaAssetFilter();
		$filter->entryIdEqual = $entryId;
		try {
			$captionAssetsList = $this->captionClientPlugin->captionAsset->listAction($filter);
		}
		catch(Exception $e) {
			KalturaLog::info("Can't list caption assets for entry id [$entryId] " . $e->getMessage());
		}
		return $captionAssetsList;
	}


	private function cloneCaption($targetEntryId, $originalCaptionAsset)
	{
		KalturaLog::info("Start copying properties from caption asset: [{$originalCaptionAsset->id}]");
		$captionAsset = new KalturaCaptionAsset();
		$propertiesToCopy = array("tags", "fileExt", "language", "label", "format");
		foreach ($propertiesToCopy as $property)
			$captionAsset->$property = $originalCaptionAsset->$property;
		try {
			$newCaption = $this->captionClientPlugin->captionAsset->add($targetEntryId , $captionAsset);
		}
		catch(Exception $e) {
			KalturaLog::info("Couldn't create new caption asset for entry id: [$targetEntryId]" . $e->getMessage());
		}
		return $newCaption;
	}

	private function getCaptionAssetItems($captionAssetId, $offset, $duration){
		$endTime = $offset + $duration;
		$captionItemsStartedBefore = $this->getCaptionAssetStartedBeforeClip($captionAssetId, $offset, $endTime);
		$captionItemsStartedOnRange = $this->getCaptionAssetStartedOnClipRange($captionAssetId, $offset, $endTime);
		$allCaptions = array_merge($captionItemsStartedBefore->objects, $captionItemsStartedOnRange->objects);
		return $allCaptions;
	}


	private function getCaptionAssetStartedBeforeClip($captionAssetId, $offset, $endTime)
	{
		KalturaLog::info("Retrieve caption asset items associated with captionAssetId: [$captionAssetId] starting before [$offset] and ending before[$endTime]");
		$filter = new KalturaCaptionAssetItemFilter();
		$filter->endTimeGreaterThanOrEqual = $offset;
		$filter->endTimeLessThanOrEqual = $endTime;
		$filter->startTimeLessThanOrEqual = $offset;
		$filter->orderBy = self::START_TIME_ASC;
		try {
			$captionItemsStartedBefore = $this->captionSearchClientPlugin->captionAssetItem->listAction($captionAssetId, $filter);
		}
		catch(Exception $e) {
			KalturaLog::info("Can't list caption assets items for caption asset id: [$captionAssetId]" . $e->getMessage());
		}
		return $captionItemsStartedBefore;
	}

	private function getCaptionAssetStartedOnClipRange($captionAssetId, $offset, $endTime){
		KalturaLog::info("Retrieve caption asset items associated with captionAssetId: [$captionAssetId] starting in the range [$offset - $endTime]");
		$filter = new KalturaCaptionAssetItemFilter();
		$filter->startTimeGreaterThanOrEqual = $offset;
		$filter->startTimeLessThanOrEqual = $endTime;
		$filter->orderBy = self::START_TIME_ASC;
		try {
			$captionItemsStartedOnClipRange = $this->captionSearchClientPlugin->captionAssetItem->listAction($captionAssetId, $filter);
		}
		catch(Exception $e) {
			KalturaLog::info("Can't list caption assets items for caption asset id: [$captionAssetId]" . $e->getMessage());
		}
		return $captionItemsStartedOnClipRange;
	}

	private function loadNewCaptionAssetFile($captionAssetId, $contentResource){
		try {
			$updatedCaption = $this->captionClientPlugin->captionAsset->setContent($captionAssetId, $contentResource);
		}
		catch(Exception $e) {
			KalturaLog::info("Can't set content to caption asset id: [$captionAssetId]" . $e->getMessage());
		}
		return $updatedCaption;
	}


	private function createNewCaptionsFile($captionAssetId, $offset, $duration , $format){
		KalturaLog::info("Create new caption file in format: [$format] with offset: [$offset]");
		$srtContent = "";
		$endTime = $offset + $duration;
		switch($format){
			case CaptionType::SRT:
				$originalCaptionAssetItems = $this->getCaptionAssetItems($captionAssetId, $offset, $duration);
				$srtContentManager = new srtCaptionsContentManager();
				$srtContent = $srtContentManager->buildSrtFile($originalCaptionAssetItems, $offset);
				break;

			case CaptionType::DFXP:
				$captionContent = $this->getCaptionContent($captionAssetId);
				$dfxpContentManager = new dfxpCaptionsContentManager();
				$srtContent = $dfxpContentManager->buildDfxpFile($captionContent, $offset, $endTime);
				break;

			default:
				KalturaLog::info("copying captions for format: [$format] is not supported");
		}

		$contentResource = new KalturaStringResource();
		$contentResource->content = $srtContent;
		return $contentResource;
	}


	private function getCaptionContent($captionAssetId)
	{
		KalturaLog::info("Retrieve caption assets content for: [$captionAssetId]");

		try {
			$captionAssetContentUrl= $this->captionClientPlugin->captionAsset->serve($captionAssetId);
			$captionAssetContent = KCurlWrapper::getContent($captionAssetContentUrl);
		}
		catch(Exception $e) {
			KalturaLog::info("Can't serve caption asset id [$captionAssetId] " . $e->getMessage());
		}
		return $captionAssetContent;
	}

}