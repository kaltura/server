<?php
/**
 * @package Core
 * @subpackage model.data
 */

class kClipConcatJobData extends kJobData
{
	/**
	 * @var string
	 */
	private $destEntryId;

	/**
	 * @var string
	 */
	private $tempEntryId;

	/**
	 * @var string
	 */
	private $sourceEntryId;

	/**
	 * @var string
	 */
	private $importUrl;

	/**
	 * @var int
	 */
	private $partnerId;

	/**
	 * @var int
	 */
	private $priority;

	/**
	 * @var array $operationAttributes
	 */
	private $operationAttributes;

	/**
	 * @var bool
	 */
	private $importNeeded;

	/**
	 * @var int
	 */
	private $resourceOrder;

	public function __construct($importUrl = null)
	{
		if($importUrl)
		{
			$this->importUrl = $importUrl;
			$this->importNeeded = true;
		}
		else
		{
			$this->importNeeded = false;
		}
	}

	/**
	 * @return string
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
	 * @return string
	 */
	public function getTempEntryId()
	{
		return $this->tempEntryId;
	}

	/**
	 * @param string $sourceEntryId
	 */
	public function setSourceEntryId($sourceEntryId)
	{
		$this->sourceEntryId = $sourceEntryId;
	}

	/**
	 * @return string
	 */
	public function getSourceEntryId()
	{
		return $this->sourceEntryId;
	}

	/**
	 * @param string $importUrl
	 */
	public function setImportUrl($importUrl)
	{
		$this->importUrl = $importUrl;
	}

	/**
	 * @return string
	 */
	public function getImportUrl()
	{
		return $this->importUrl;
	}

	/**
	 * @param string $entryId
	 */
	public function setTempEntryId($entryId)
	{
		$this->tempEntryId = $entryId;
	}

	/**
	 * @return string
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
	 * @return string
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
	 * @return kOperationAttributes[]
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

	public function setImportNeeded($isNeeded)
	{
		$this->importNeeded = $isNeeded;
	}

	public function getImportNeeded()
	{
		return $this->importNeeded;
	}

	/**
	 * @return int
	 */
	public function getResourceOrder()
	{
		return $this->resourceOrder;
	}

	/**
	 * @param int $resourceOrder
	 */
	public function setResourceOrder($resourceOrder)
	{
		$this->resourceOrder = $resourceOrder;
	}

}