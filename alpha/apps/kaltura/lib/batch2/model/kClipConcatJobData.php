<?php
/**
 * @package Core
 * @subpackage model.data
 */

class kClipConcatJobData extends kJobData
{
	/**$entryId
	 * @var string
	 */
	private $entryId;

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
	public function getEntryId()
	{
		return $this->entryId;
	}

	/**
	 * @param string $entryId
	 */
	public function setEntryId($entryId)
	{
		$this->entryId = $entryId;
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