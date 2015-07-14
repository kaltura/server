<?php

/**
 * @package plugins.queue
 */
abstract class QueueProvider 
{
    public static function getInstance($objectType = null)
    {
        return KalturaPluginManager::loadObject('QueueProvider', $objectType);
    }

    abstract public function exists($queueName);
    abstract public function create($queueName);    
    abstract public function send($queueName, $message);    
}
