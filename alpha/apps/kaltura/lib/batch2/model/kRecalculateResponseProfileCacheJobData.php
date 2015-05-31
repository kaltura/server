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
	private $startDocId;

	/**
	 * @var string
	 */
	private $endDocId;
	
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
	public function setObjectId($protocol)
	{
		$this->objectId = $objectId;
	}
	
	/**
	 * @return string
	 */
	public function getStartDocId()
	{
		return $this->startDocId;
	}
	
	/**
	 * @param string $startDocId
	 */
	public function setStartDocId($startDocId)
	{
		$this->startDocId = $startDocId;
	}
	
	/**
	 * @return string
	 */
	public function getEndDocId()
	{
		return $this->endDocId;
	}
	
	/**
	 * @param string $endDocId
	 */
	public function setEndDocId($endDocId)
	{
		$this->endDocId = $endDocId;
	}
}
