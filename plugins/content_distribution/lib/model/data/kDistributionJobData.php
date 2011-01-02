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
	 * enum from DistributionProviderType
	 * @var int
	 */
	private $providerType;

	/**
	 * Additional data that relevant for the provider only
	 * @var kDistributionJobProviderData
	 */
	private $providerData;

	/**
	 * The results as returned from the remote destination
	 * @var string
	 */
	private $results;

	/**
	 * The data as sent to the remote destination
	 * @var string
	 */
	private $sentData;
	
	/**
	 * @return the $results
	 */
	public function getResults()
	{
		return $this->results;
	}

	/**
	 * @param string $results
	 */
	public function setResults($results)
	{
		$this->results = $results;
	}

	/**
	 * @return the $providerType
	 */
	public function getProviderType()
	{
		return $this->providerType;
	}

	/**
	 * @param int $providerType
	 */
	public function setProviderType($providerType)
	{
		$this->providerType = $providerType;
	}

	/**
	 * @return kDistributionJobProviderData $providerData
	 */
	public function getProviderData()
	{
		return $this->providerData;
	}

	/**
	 * @param kDistributionJobProviderData $providerData
	 */
	public function setProviderData(kDistributionJobProviderData $providerData)
	{
		$this->providerData = $providerData;
	}

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
	
	/**
	 * @return the $sentData
	 */
	public function getSentData()
	{
		return $this->sentData;
	}

	/**
	 * @param string $sentData
	 */
	public function setSentData($sentData)
	{
		$this->sentData = $sentData;
	}
}