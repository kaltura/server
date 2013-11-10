<?php
/**
 * @package plugins.tagSearch
 * @subpackage Scheduler
 */
class KAsyncTagIndex extends KJobHandlerWorker
{
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job) {
		
		$this->reIndexTags($job);
		
	}

	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::INDEX_TAGS;
	}
	
	protected function reIndexTags (KalturaBatchJob $job)
	{
		KalturaLog::info("Re-indexing tags according to privacy contexts");
		$tagPlugin = KalturaTagSearchClientPlugin::get(self::$kClient);
		$this->impersonate($job->partnerId);
		try 
		{
			$tagPlugin->tag->indexCategoryEntryTags($job->data->changedCategoryId, $job->data->deletedPrivacyContexts, $job->data->addedPrivacyContexts);
		}
		catch (Exception $e)
		{
			$this->unimpersonate();
			return $this->closeJob($job, KalturaBatchJobErrorTypes::KALTURA_API, $e->getCode(), $e->getMessage(), KalturaBatchJobStatus::FAILED);
		}
		$this->unimpersonate();
		return $this->closeJob($job, null, null, "Re-index complete", KalturaBatchJobStatus::FINISHED);
		
	}
}