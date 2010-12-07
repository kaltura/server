<?php
class kDistributionJobData extends kJobData
{
	/**
	 * @var int
	 */
	private $distributionProfileId;
	
	/**
	 * @var int
	 */
	private $entryDistributionId;

	/**
	 * Id of the media in the remote system
	 * @var string
	 */
	private $remoteId;
	
	/**
	 * @return the $remoteId
	 */
	public function getRemoteId()
	{
		return $this->remoteId;
	}

	/**
	 * @param $remoteId the $remoteId to set
	 */
	public function setRemoteId($remoteId)
	{
		$this->remoteId = $remoteId;
	}
	
	/**
	 * @return the $entryDistributionId
	 */
	public function getEntryDistributionId()
	{
		return $this->entryDistributionId;
	}

	/**
	 * @param int $entryDistributionId
	 */
	public function setEntryDistributionId($entryDistributionId)
	{
		$this->entryDistributionId = $entryDistributionId;
	}

	/**
	 * @return the $distributionProfileId
	 */
	public function getDistributionProfileId()
	{
		return $this->distributionProfileId;
	}

	/**
	 * @param $distributionProfileId the $distributionProfileId to set
	 */
	public function setDistributionProfileId($distributionProfileId)
	{
		$this->distributionProfileId = $distributionProfileId;
	}
}