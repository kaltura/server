<?php

/**
 * Interface kObjectReadyForIndexContainerEventConsumer
 */
interface kObjectReadyForIndexContainerEventConsumer extends KalturaEventConsumer
{
    /**
     * @param $object
     * @param $params
     * @return bool true if should continue to the next consumer
     */
    public function objectReadyForIndexContainer($object ,$params = null);

    /**
     * @param $object
     * @param $params
     * @return bool true if the consumer should handle the event
     */
    public function shouldConsumeReadyForIndexContainerEvent($object, $params = null);
}
