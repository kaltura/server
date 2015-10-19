<?php
/**
 * @package plugins.captionSearch
 * @subpackage Scheduler
 */
class KAsyncParseCaptionAsset extends KJobHandlerWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::PARSE_CAPTION_ASSET;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->parse($job, $job->data);
	}
	
	protected function parse(KalturaBatchJob $job, KalturaParseCaptionAssetJobData $data)
	{
		try
		{
			$this->updateJob($job, "Start parsing caption asset [$data->captionAssetId]", KalturaBatchJobStatus::QUEUED);
			
			$captionSearchPlugin = KalturaCaptionSearchClientPlugin::get(self::$kClient);
			$captionSearchPlugin->captionAssetItem->parse($data->captionAssetId);
			
			$this->closeJob($job, null, null, "Finished parsing", KalturaBatchJobStatus::FINISHED);
		}
		catch(Exception $ex)
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED, $data);
		}
		return $job;
	}
}
