<?php

/**
 * @package plugins.dropFolder
 * @subpackage model.enum
 */
class ReachInternalQueueHandlerBatchJobType implements IKalturaPluginEnum, BatchJobType
{
    const REACH_INTERNAL_QUEUE_HANDLER = 'ReachInternalQueueHandler';

    public static function getAdditionalValues()
    {
        return array(
            'REACH_INTERNAL_QUEUE_HANDLER' => self::REACH_INTERNAL_QUEUE_HANDLER,
        );
    }

    /**
     * @return array
     */
    public static function getAdditionalDescriptions()
    {
        return array();
    }
}
