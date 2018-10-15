<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.events
 */
interface kESearchSearchHistoryInfoEventConsumer extends KalturaEventConsumer
{

    /**
     * @param $object
     * @return bool true if should continue to the next consumer
     */
    public function consumeESearchSearchHistoryInfoEvent($object);

    /**
     * @param $object
     * @return bool true if the consumer should handle the event
     */
    public function shouldConsumeESearchSearchHistoryInfoEvent($object);

}
