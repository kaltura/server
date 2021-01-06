<?php


class KReachVendorTaskHelloWorldProcessorEngine extends KReachVendorTaskProcessorEngine
{

    /**
     * @inheritDoc
     */
    function handleTask(KalturaEntryVendorTask $vendorTask)
    {
        KalturaLog::info('HelloWorld engine handling vendor task ' . $vendorTask->id . ' for client partner ' . $vendorTask->partnerId);

        //Impersonate the target partner
        KBatchBase::impersonate($vendorTask->partnerId);

        $targetMetadataProfileSystemName = KBatchBase::$taskConfig->params->reachHelloWorldEngine->metadataSystemName;

        $metadataProfileFilter = new KalturaMetadataProfileFilter();
        $metadataProfileFilter->systemNameEqual = $targetMetadataProfileSystemName;
        $metadataProfileFilter->statusEqual = KalturaMetadataProfileStatus::ACTIVE;

        $metadataPlugin = KalturaMetadataClientPlugin::get(KBatchBase::$kClient);
        $metadataProfileListResponse = $metadataPlugin->metadataProfile->listAction($metadataProfileFilter);

        if(!$metadataProfileListResponse->totalCount)
        {
            KalturaLog::err('Required metadata profile not found on target partner - aborting');
            return $this->endTaskOnError($vendorTask);
        }
        $metadataProfileId = $metadataProfileListResponse->objects[0]->id;

        $targetMetadata = '<metadata><Control>Hello World</Control>></metadata>';

        try {
            $metadata = $metadataPlugin->metadata->add($metadataProfileId, KalturaMetadataObjectType::ENTRY, $vendorTask->entryId, $targetMetadata);
            $this->endTaskSuccess($vendorTask);
        } catch (Exception $e)
        {
            KalturaLog::err('Unable to complete task '  . $vendorTask->id . ' - metadata could not be added to entry');
            return $this->endTaskOnError($vendorTask);
        }

    }
}