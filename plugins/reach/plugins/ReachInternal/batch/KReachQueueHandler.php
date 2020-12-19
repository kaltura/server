<?php
/**
 * @package plugins.eventNotification
 * @subpackage Scheduler
 */

class KReachQueueHandler extends KPeriodicWorker
{

    const CATALOG_ITEM_INDEX = 'reach_vendor_catalog_item';

    /**
     * @var KalturaReachClientPlugin
     */
    protected $reachPlugin;

    function __construct($taskConfig = null)
    {
        parent::__construct($taskConfig);

        $this->reachPlugin = KalturaReachClientPlugin::get(KBatchBase::$kClient);
    }

    /**
     * @inheritDoc
     */
    public function run($jobs = null)
    {
        //retrieve all entry vendor tasks using the response profile.
        KBatchBase::impersonate(self::REACH_INTERNAL_VENDOR_PARTNER);

        //init filter
        $filter = new KalturaEntryVendorTaskFilter();
        $pager = new KalturaFilterPager();
        $pager->pageSize = 100;

        //Set response profile in order to add the vendor catalog item and/or reach profile to the entry vendor task response.
        KBatchBase::$kClient->setResponseProfile(KBatchBase::$taskConfig->params->responseProfileId);

        $response = $this->reachPlugin->entryVendorTask->getJobs($filter, $pager);

        $handledTasksCounter = 0;
        while ($handledTasksCounter < KBatchBase::$taskConfig->params->taskHandleLimit) {
            foreach ($response->objects as $entryVendorTask) {
                /* @var $entryVendorTask KalturaEntryVendorTask */

                //retrieve associated catalog item and retrieve appropriate engine based on the engineType property
                $catalogItem = $entryVendorTask->relatedObjects[self::CATALOG_ITEM_INDEX][0];
                /* @var $catalogItem KalturaVendorCatalogItem */
                $engine = KReachVendorTaskProcessorEngine::getInstance($catalogItem->engineType);
                if (!$engine) {
                    KalturaLog::info('No engine type found to process entry vendor task ID: ' . $entryVendorTask->id);
                    continue;
                }

                try {
                    $engine->handleTask($entryVendorTask);
                } catch (Exception $e) {
                    KalturaLog::err('Error occurred processing vendor task with ID ' . $entryVendorTask->id . '. Message: ' . $e->getMessage());
                }

                $handledTasksCounter++;
                if ($handledTasksCounter >= KBatchBase::$taskConfig->params->taskHandleLimit)
                {
                    KalturaLog::info('Entry vendor task processing limit reached for current run. Exiting.');
                    break;
                }
            }

            $response = $this->reachPlugin->entryVendorTask->getJobs($filter, $pager);
        }

        KBatchBase::unimpersonate();
    }

}