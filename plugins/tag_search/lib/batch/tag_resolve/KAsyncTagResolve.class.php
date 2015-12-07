<?php
/**
 * @package Scheduler
 * @subpackage TagResolver
 */
class KAsyncTagResolve extends KPeriodicWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	 */
	public function run($jobs = null) 
	{
		$tagPlugin = KalturaTagSearchClientPlugin::get(self::$kClient);
		$deletedTags = $tagPlugin->tag->deletePending();
		
		KalturaLog::info("Finished resolving tags: $deletedTags tags removed from DB");
	}
	
	/**
	 * @return int
	 * @throws Exception
	 */
	public static function getType()
	{
		return KalturaBatchJobType::TAG_RESOLVE;
	}

	
}