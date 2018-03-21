<?php
/**
 * @package Core
 * @subpackage model.data
 */

class kClipConcatJobData extends kJobData
{
	/**$destEntryId
	 * @var string
	 */
	private $destEntryId;

	/**$tempEntryId
	 * @var string
	 */
	private $tempEntryId;

	/** $partnerId
	 * @var int
	 */
	private $partnerId;

	/** $priority
	 * @var int
	 */
	private $priority;


	/** clip operations
	 * @var array $operationAttributes
	 */
	private $operationAttributes;

	/**
	 * @return string $entryId
	 */
	public function getDestEntryId()
	{
		return $this->destEntryId;
	}

	/**
	 * @param string $entryId
	 */
	public function setDestEntryId($entryId)
	{
		$this->destEntryId = $entryId;
	}

	/**
	 * @return string $entryId
	 */
	public function getTempEntryId()
	{
		return $this->tempEntryId;
	}

	/**
	 * @param string $entryId
	 */
	public function setTempEntryId($entryId)
	{
		$this->tempEntryId = $entryId;
	}

	/**
	 * @return string $partnerId
	 */
	public function getPartnerId()
	{
		return $this->partnerId;
	}

	/**
	 * @param string $partnerId
	 */
	public function setPartnerId($partnerId)
	{
		$this->partnerId = $partnerId;
	}


	/**
	 * @return string $priority
	 */
	public function getPriority()
	{
		return $this->priority;
	}

	/**
	 * @param string $priority
	 */
	public function setPriority($priority)
	{
		$this->priority = $priority;
	}


	/**
	 * @return kOperationAttributes[] $operationAttributes
	 */
	public function getOperationAttributes()
	{
		return $this->operationAttributes;
	}

	/**
	 * @param kOperationAttributes[] $operationAttributes
	 */
	public function setOperationAttributes($operationAttributes)
	{
		$this->operationAttributes = $operationAttributes;
	}


}