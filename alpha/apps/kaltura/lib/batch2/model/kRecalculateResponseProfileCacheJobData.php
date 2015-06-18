<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kRecalculateResponseProfileCacheJobData extends kRecalculateCacheJobData
{
	/**
	 * http / https
	 * @var string
	 */
	private $protocol;

	/**
	 * @var int
	 */
	private $ksType;

	/**
	 * @var array
	 */
	private $userRoles;

	/**
	 * Class name
	 * @var string
	 */
	private $objectType;

	/**
	 * @var string
	 */
	private $objectId;

	/**
	 * @var string
	 */
	private $startObjectKey;

	/**
	 * @var string
	 */
	private $endObjectKey;
	
	/**
	 * @return the $startObjectKey
	 */
	public function getStartObjectKey()
	{
		return $this->startObjectKey;
	}

	/**
	 * @return the $endObjectKey
	 */
	public function getEndObjectKey()
	{
		return $this->endObjectKey;
	}

	/**
	 * @param string $startObjectKey
	 */
	public function setStartObjectKey($startObjectKey)
	{
		$this->startObjectKey = $startObjectKey;
	}

	/**
	 * @param string $endObjectKey
	 */
	public function setEndObjectKey($endObjectKey)
	{
		$this->endObjectKey = $endObjectKey;
	}

	/**
	 * @return string
	 */
	public function getProtocol()
	{
		return $this->protocol;
	}
	
	/**
	 * @param string $protocol
	 */
	public function setProtocol($protocol)
	{
		$this->protocol = $protocol;
	}
	
	/**
	 * @return string
	 */
	public function getKsType()
	{
		return $this->ksType;
	}
	
	/**
	 * @param string $ksType
	 */
	public function setKsType($ksType)
	{
		$this->ksType = $ksType;
	}
	
	/**
	 * @return string
	 */
	public function getUserRoles()
	{
		return $this->userRoles;
	}
	
	/**
	 * @param string $userRoles
	 */
	public function setUserRoles($userRoles)
	{
		$this->userRoles = $userRoles;
	}
	
	/**
	 * @return string
	 */
	public function getObjectType()
	{
		return $this->objectType;
	}
	
	/**
	 * @param string $objectType
	 */
	public function setObjectType($objectType)
	{
		$this->objectType = $objectType;
	}
	
	/**
	 * @return string
	 */
	public function getObjectId()
	{
		return $this->objectId;
	}
	
	/**
	 * @param string $objectId
	 */
	public function setObjectId($objectId)
	{
		$this->objectId = $objectId;
	}
}
