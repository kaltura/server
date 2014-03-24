<?php
/**
 * Applicative event that raised implicitly by the developer
 * @package Core
 * @subpackage events
 */
abstract class kApplicativeEvent extends KalturaEvent implements IKalturaContinualEvent,  IKalturaObjectRelatedEvent
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
	
	public function getKey()
	{
		if(method_exists($this->object, 'getId'))
			return get_class($this)."_".get_class($this->object)."_".$this->object->getId();
		
		return null;
	}
	
	/**
	 * @return BaseObject $object
	 */
	public function getObject() 
	{
		return $this->object;
	}

	/**
	 * @return BatchJob $raisedJob
	 */
	public function getRaisedJob() 
	{
		return $this->raisedJob;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaEvent::getScope()
	 */
	public function getScope()
	{
		$scope = parent::getScope();
		
		if($this->raisedJob)
		{
			$scope->setPartnerId($this->raisedJob->getPartnerId());
			$scope->setParentRaisedJob($this->raisedJob);
		}
		elseif(method_exists($this->object, 'getPartnerId'))
		{
			$scope->setPartnerId($this->object->getPartnerId());
		}
		
		return $scope;
	}
}