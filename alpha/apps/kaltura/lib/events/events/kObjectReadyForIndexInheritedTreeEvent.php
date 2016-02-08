<?php
/**
 * Applicative event that raised by the developer when indexed object is ready for indexing inherited tree in the index server
 */
class kObjectReadyForIndexInheritedTreeEvent extends kApplicativeEvent
{
	const EVENT_CONSUMER = 'kObjectReadyForIndexInheritedTreeEventConsumer';

	private $partnerCriteriaParams;

	public function getConsumerInterface()
	{
		return self::EVENT_CONSUMER;
	}

	/**
	 * @param BaseObject $object
	 * @param array $partnerCriteriaParams
	 */
	public function __construct( BaseObject $object, array $partnerCriteriaParams, BatchJob $raisedJob = null)
	{
		parent::__construct($object, $raisedJob);
		$this->partnerCriteriaParams = $partnerCriteriaParams;
	}

	public function getPartnerCriteriaParams()
	{
		return $this->partnerCriteriaParams;
	}

	/**
	 * @param kObjectAddedEventConsumer $consumer
	 * @return bool true if should continue to the next consumer
	 */
	protected function doConsume(KalturaEventConsumer $consumer)
	{
		if(!$consumer->shouldConsumeReadyForIndexInheritedTreeEvent($this->object))
			return true;

		$additionalLog = '';
		if(method_exists($this->object, 'getId'))
			$additionalLog .= 'id [' . $this->object->getId() . ']';

		KalturaLog::debug('consumer [' . get_class($consumer) . '] started handling [' . get_class($this) . '] object type [' . get_class($this->object) . '] ' . $additionalLog);
		$result = $consumer->objectReadyForIndexInheritedTreeEvent($this->object, $this->partnerCriteriaParams, $this->raisedJob);
		KalturaLog::debug('consumer [' . get_class($consumer) . '] finished handling [' . get_class($this) . '] object type [' . get_class($this->object) . '] ' . $additionalLog);
		return $result;
	}

}