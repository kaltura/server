<?php
/**
 * @package Scheduler
 * @subpackage SimuLiveClearScheduleEvents
 */

/**
 * clear old Simu-Live scheduled Events
 *
 * @package Scheduler
 * @subpackage SimuLiveClearScheduleEvents
 */
class KAsyncSimuLiveClearScheduleEvents extends KPeriodicWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::CLEANUP;
	}

	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	*/
	public function run($jobs = null)
	{
		$filter = new KalturaSimulatedLiveEntryScheduleEventFilter();
		$dvrWindow = KBatchBase::$taskConfig->params->dvrWindow;
		$filter->endDateLessThanOrEqual = time() - $dvrWindow;
		$filter->statusIn = KalturaScheduleEventStatus::ACTIVE;

		$pager = new KalturaFilterPager();
		$pager->pageSize = 100;
		$pager->pageIndex = 1;

		$schedulePlugin = KalturaScheduleClientPlugin::get(KBatchBase::$kClient);
		$events = $schedulePlugin->scheduleEvent->listAction($filter, $pager);
		
		while(count($events->objects))
		{
			self::$kClient->startMultiRequest();
			foreach ($events->objects as $event)
			{
				$schedulePlugin->scheduleEvent->delete($event->id);
			}
			self::$kClient->doMultiRequest();
			$pager->pageIndex++;
			$events = $schedulePlugin->scheduleEvent->listAction($filter, $pager);
		}
	}
}
