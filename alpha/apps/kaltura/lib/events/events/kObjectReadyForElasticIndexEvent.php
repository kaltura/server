<?php
/**
 * Applicative event that raised by the developer when indexed object is ready for indexing to elasticsearch
 */
class kObjectReadyForElasticIndexEvent extends kApplicativeEvent
{

    const EVENT_CONSUMER = 'kObjectReadyForElasticIndexEventConsumer';

    protected $params;

    public function __construct($object, $params = null)
    {
        $this->object = $object;
        $this->params = $params;

        $additionalLog = '';
        if(method_exists($object, 'getId'))
            $additionalLog .= ' id [' . $object->getId() . ']';
        if($params)
            $additionalLog .= ' with params [' . print_r($params, true) . ']';

        KalturaLog::debug("Event [" . get_class($this) . "] object type [" . get_class($object) . "]" . $additionalLog);
    }

    /**
     * @return string - name of consumer interface
     */
    public function getConsumerInterface()
    {
        return self::EVENT_CONSUMER;
    }

    /**
     * Executes the consumer
     * @param KalturaEventConsumer $consumer
     * @return bool true if should continue to the next consumer
     */
    protected function doConsume(KalturaEventConsumer $consumer)
    {
        if(!$consumer->shouldConsumeReadyForElasticIndexEvent($this->object, $this->params))
            return true;

        $additionalLog = '';
        if(method_exists($this->object, 'getId'))
            $additionalLog .= 'id [' . $this->object->getId() . ']';
        if($this->params)
            $additionalLog .= ' with params [' . print_r($this->params, true) . ']';

        KalturaLog::debug('consumer [' . get_class($consumer) . '] started handling [' . get_class($this) . '] object type [' . get_class($this->object) . '] ' . $additionalLog);
        $result = $consumer->objectReadyForElasticIndex($this->object, $this->params);
        KalturaLog::debug('consumer [' . get_class($consumer) . '] finished handling [' . get_class($this) . '] object type [' . get_class($this->object) . '] ' . $additionalLog);
        return $result;
    }
}
