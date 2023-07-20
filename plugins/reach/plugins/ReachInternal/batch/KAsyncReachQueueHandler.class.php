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
        //init filter
        $filter = new KalturaEntryVendorTaskFilter();
        $pager = new KalturaFilterPager();
        $pager->pageSize = 100;

        //Set response profile in order to add the vendor catalog item and/or reach profile to the entry vendor task response.
        $responseProfile = $this->constructResponseProfile();

	    $handledTasksCounter = 0;
	    do
	    {
		    //retrieve all entry vendor tasks using the response profile.
		    KBatchBase::impersonate(KBatchBase::$taskConfig->params->reachInternalVendorPartner);
		    KBatchBase::$kClient->setResponseProfile($responseProfile);
		    $response = $this->reachPlugin->entryVendorTask->getJobs($filter, $pager);

            if ($response->totalCount == 0)
            {
                KalturaLog::info('No jobs found to handle at this time. Exiting');
                KBatchBase::unimpersonate();
                return;
            }

            foreach ($response->objects as $entryVendorTask) {
                /* @var $entryVendorTask KalturaEntryVendorTask */

				if ($entryVendorTask->status != KalturaEntryVendorTaskStatus::PENDING)
				{
					KalturaLog::info('Entry vendor task id ' . $entryVendorTask->id . ' is in an invalid status: ' . $entryVendorTask->status . '. Skipping.');
					$handledTasksCounter++;
					continue;
				}

                //retrieve associated catalog item and retrieve appropriate engine based on the engineType property
                $catalogItem = $entryVendorTask->relatedObjects[0]->objects[0];
                /* @var $catalogItem KalturaVendorCatalogItem */
                $engine = kReachVendorTaskProcessorEngine::getInstance($catalogItem->engineType);
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

        } while ($handledTasksCounter < KBatchBase::$taskConfig->params->taskHandleLimit && !parent::checkStopFile());

        KBatchBase::unimpersonate();
    }

    /**
     * @return KalturaDetachedResponseProfile
     */
    protected function constructResponseProfile ()
    {
        $responseProfile = new KalturaDetachedResponseProfile();
        $responseProfile->type = KalturaResponseProfileType::INCLUDE_FIELDS;
        $responseProfile->fields = 'id,partnerId,vendorPartnerId,createdAt,entryId,status,reachProfileId,catalogItemId,taskJobData,accessKey,notes,dictionary';
        $responseProfile->relatedProfiles = array();

        $catalogItemProfile = new KalturaDetachedResponseProfile();
        $catalogItemProfile->type = KalturaResponseProfileType::INCLUDE_FIELDS;
        $catalogItemProfile->fields = 'id,vendorPartnerId,name,systemName,createdAt,updatedAt,status,pricing,fixedPriceAddons,engineType';
        $catalogItemProfile->filter = new KalturaVendorCatalogItemFilter();
        $catalogItemProfile->mappings = array();
        $mapping = new KalturaResponseProfileMapping();
        $mapping->parentProperty = 'catalogItemId';
        $mapping->filterProperty = 'idEqual';
        $catalogItemProfile->mappings[] = $mapping;
        $responseProfile->relatedProfiles[] = $catalogItemProfile;

        $reachResponseProfile = new KalturaDetachedResponseProfile();
        $reachResponseProfile->type = KalturaResponseProfileType::INCLUDE_FIELDS;
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