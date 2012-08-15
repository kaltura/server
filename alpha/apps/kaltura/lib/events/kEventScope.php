<?php
/**
 * @package Core
 * @subpackage events
 */
class kEventScope extends kScope
{
	/**
	 * @var KalturaEvent
	 */
	protected $event;
	
	/**
	 * @var int
	 */
	protected $partnerId;
	
	/**
	 * @var BatchJob
	 */
	protected $parentRaisedJob;
	
	/**
	 * @param KalturaEvent $v
	 */
	public function __construct(KalturaEvent $v)
	{
		$this->event = $v;
	}
	
	/**
	 * @return KalturaEvent
	 */
	public function getEvent()
	{
		return $this->event;
	}
	
	/**
	 * @return int $partnerId
	 */
	public function getPartnerId()
	{
	    if (! $this->partnerId)
	    {
	        return kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
	    }
		
	    return $this->partnerId;
	}

	/**
	 * @return BatchJob $parentRaisedJob
	 */
	public function getParentRaisedJob()
	{
		return $this->parentRaisedJob;
	}

	/**
	 * @param int $partnerId
	 */
	public function setPartnerId($partnerId)
	{
		$this->partnerId = $partnerId;
	}

	/**
	 * @param BatchJob $parentRaisedJob
	 */
	public function setParentRaisedJob(BatchJob $parentRaisedJob)
	{
		$this->parentRaisedJob = $parentRaisedJob;
	}

	
}