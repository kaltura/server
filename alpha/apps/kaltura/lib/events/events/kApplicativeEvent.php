<?php
/**
 * Applicative event that raised implicitly by the developer
 */
abstract class kApplicativeEvent extends KalturaEvent implements IKalturaContinualEvent
{
	/**
	 * @var BaseObject
	 */
	protected $object;
	
	/**
	 * @var BatchJob
	 */
	protected $raisedJob;
	
	/**
	 * @param BaseObject $object
	 * @param BatchJob $raisedJob
	 */
	public function __construct(BaseObject $object, BatchJob $raisedJob = null)
	{
		$this->object = $object;
		$this->raisedJob = $raisedJob;
		
		$additionalLog = '';
		if(method_exists($object, 'getId'))
			$additionalLog .= ' id [' . $object->getId() . ']';
		if($raisedJob)
			$additionalLog .= ' raised job id [' . $raisedJob->getId() . '] of type [' . $raisedJob->getJobType() . ']';
			
		KalturaLog::debug("Event [" . get_class($this) . "] object type [" . get_class($object) . "]" . $additionalLog);
	}
}