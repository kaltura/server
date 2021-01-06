<?php
/**
 * @package plugins.reachInternal
 * @subpackage Scheduler
 */

class KAsyncReachQueueHandler extends KPeriodicWorker
{

    const CATALOG_ITEM_INDEX = 'reach_vendor_catalog_item';

    /**
     * @var KalturaReachClientPlugin
     */
    protected $reachPlugin;

    /* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
    public static function getType()
    {
        return KalturaBatchJobType::REACH_INTERNAL_QUEUE_HANDLER;
    }

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
        KBatchBase::impersonate(KBatchBase::$taskConfig->params->reachInternalVendorPartner);

        //init filter
        $filter = new KalturaEntryVendorTaskFilter();
        $pager = new KalturaFilterPager();
        $pager->pageSize = 100;

        //Set response profile in order to add the vendor catalog item and/or reach profile to the entry vendor task response.
        $responseProfile = $this->constructResponseProfile();
        KBatchBase::$kClient->setResponseProfile($responseProfile);

        $response = $this->reachPlugin->entryVendorTask->getJobs($filter, $pager);

        $handledTasksCounter = 0;
        while ($handledTasksCounter < KBatchBase::$taskConfig->params->taskHandleLimit) {
            foreach ($response->objects as $entryVendorTask) {
                /* @var $entryVendorTask KalturaEntryVendorTask */

                //retrieve associated catalog item and retrieve appropriate engine based on the engineType property
                $catalogItem = $entryVendorTask->relatedObjects[self::CATALOG_ITEM_INDEX][0];
                /* @var $catalogItem KalturaVendorCatalogItem */
                $engine = KReachVendorTaskProcessorEngine::getInstance($catalogItem->engineType);
                if (!$engine)
                {
                    KalturaLog::info('No engine type found to process entry vendor task ID: ' . $entryVendorTask->id);
                    continue;
                }

                try {
                    $engine->moveTaskToStatus($entryVendorTask, KalturaEntryVendorTaskStatus::PROCESSING);
                    $engine->handleTask($entryVendorTask);
                    KBatchBase::unimpersonate();
                } catch (Exception $e) {
                    KalturaLog::err('Error occurred processing vendor task with ID ' . $entryVendorTask->id . '. Message: ' . $e->getMessage());
                    KBatchBase::unimpersonate();
                }

                $handledTasksCounter++;
                if ($handledTasksCounter >= KBatchBase::$taskConfig->params->taskHandleLimit)
                {
                    KalturaLog::info('Entry vendor task processing limit reached for current run. Exiting.');
                    break;
                }
            }

            KBatchBase::impersonate(self::REACH_INTERNAL_VENDOR_PARTNER);
            $response = $this->reachPlugin->entryVendorTask->getJobs($filter, $pager);
        }

        KBatchBase::unimpersonate();
    }

    /**
     * @return KalturaDetachedResponseProfile
     */
    protected function constructResponseProfile ()
    {
        $responseProfile = new KalturaDetachedResponseProfile();
        $responseProfile->fields = 'id,partnerId,vendorPartnerId,createdAt,entryId,status,reachProfileId,catalogItemId,accessKey,notes,dictionary';
        $responseProfile->relatedProfiles = array();

        $catalogItemProfile = new KalturaDetachedResponseProfile();
        $catalogItemProfile->fields = 'id,vendorPartnerId,name,systemName,createdAt,updatedAt,status,pricing,fixedPriceAddons,engineType';
        $catalogItemProfile->filter = new KalturaVendorCatalogItemFilter();
        $catalogItemProfile->mappings = array();
        $mapping = new KalturaResponseProfileMapping();
        $mapping->parentProperty = 'catalogItemId';
        $mapping->filterProperty = 'idEqual';
        $catalogItemProfile->mappings[] = $mapping;
        $responseProfile->relatedProfiles[] = $catalogItemProfile;

        $reachResponseProfile = new KalturaDetachedResponseProfile();
        $reachResponseProfile->fields = 'id,partnerId,createdAt,updatedAt,status,profileType,rules,credit,usedCredit,dictionaries,autoDisplayMachineCaptionsOnPlayer,autoDisplayHumanCaptionsOnPlayer,enableMachineModeration,enableHumanModeration';
        $reachResponseProfile->filter = new KalturaReachProfileFilter();
        $reachResponseProfile->mappings = array();
        $mapping = new KalturaResponseProfileMapping();
        $mapping->parentProperty = 'reachProfileId';
        $mapping->filterProperty = 'idEqual';
        $reachResponseProfile->mappings[] = $mapping;
        $responseProfile->relatedProfiles[] = $reachResponseProfile;

        return $responseProfile;
    }

}