<?php
/**
 * @package Core
 * @subpackage model.data
 */

class kMultiClipConcatJobData extends kJobData
{
	/**
	 * @var string
	 */
	private $destEntryId;

	/**
	 * @var string
	 */
	private $multiTempEntryId;

	/**
	 * @var int
	 */
	private $partnerId;

	/**
	 * @var int
	 */
	private $priority;

	/**
	 * @var array
	 */
	private $operationResources;

	public function __construct()
	{
	}

	/**
	 * @return string
	 */
	public function getDestEntryId()
	{
		return $this->destEntryId;
	}

	/**
	 * @param string $destEntryId
	 */
	public function setDestEntryId($destEntryId)
	{
		$this->destEntryId = $destEntryId;
	}

	/**
	 * @return string
	 */
	public function getMultiTempEntryId()
	{
		return $this->multiTempEntryId;
	}

	/**
	 * @param string $entryId
	 */
	public function setMultiTempEntryId($entryId)
	{
		$this->multiTempEntryId = $entryId;
	}

	/**
	 * @return int
	 */
	public function getPartnerId()
	{
		return $this->partnerId;
	}

	/**
	 * @param int $partnerId
	 */
	public function setPartnerId($partnerId)
	{
		$this->partnerId = $partnerId;
	}

	/**
	 * @return int
	 */
	public function getPriority()
	{
		return $this->priority;
	}

	/**
	 * @param int $priority
	 */
	public function setPriority($priority)
	{
		$this->priority = $priority;
	}

	/**
	 * @return kOperationResource[]
	 */
	public function getOperationResources()
	{
		return $this->operationResources;
	}

	/**
	 * @param kOperationResource[] $operationResources
	 */
	public function setOperationResources($operationResources)
	{
		$this->operationResources = $operationResources;
	}

}