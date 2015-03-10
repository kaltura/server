<?php
/**
 * @package plugins.businessProcessNotification
 */
class kBusinessProcessCase
{
	/**
	 * @var string
	 */
	protected $id;
	
	/**
	 * @var string
	 */
	protected $businessProcessId;

	/**
	 * @var boolean
	 */
	protected $suspended;

	/**
	 * @var string
	 */
	protected $activityId;
	
	/**
	 * @return the $id
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return the $businessProcessId
	 */
	public function getBusinessProcessId()
	{
		return $this->businessProcessId;
	}

	/**
	 * @return the $suspended
	 */
	public function getSuspended()
	{
		return $this->suspended;
	}

	/**
	 * @return the $activityId
	 */
	public function getActivityId()
	{
		return $this->activityId;
	}

	/**
	 * @param string $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @param string $businessProcessId
	 */
	public function setBusinessProcessId($businessProcessId)
	{
		$this->businessProcessId = $businessProcessId;
	}

	/**
	 * @param boolean $suspended
	 */
	public function setSuspended($suspended)
	{
		$this->suspended = $suspended;
	}

	/**
	 * @param string $activityId
	 */
	public function setActivityId($activityId)
	{
		$this->activityId = $activityId;
	}
}

