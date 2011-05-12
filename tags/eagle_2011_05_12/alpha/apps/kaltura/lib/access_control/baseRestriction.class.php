<?php
/**
 * @package Core
 * @subpackage model.data
 */
abstract class baseRestriction
{
	/**
	 * @var accessControl
	 */
	protected $accessControl;
	
	/**
	 * @param accessControl $accessControl
	 */
	public function __construct(accessControl $accessControl = null)
	{
		$this->accessControl = $accessControl;
	}
	/**
	 * @return bool
	 */
	public abstract function isValid();
	
	/**
	 * @param accessControl $v
	 */
	public function setAccessControl($v)
	{
		$this->accessControl = $v;
	}
	
	/**
	 * @return accessControl
	 */
	public function getAccessControl()
	{
		return $this->accessControl;
	}
	
	/**
	 * @return accessControlScope
	 */
	public function getAccessControlScope()
	{
		if ($this->accessControl === null)
			throw new Exception("No scope because access control is null");
			
		return $this->accessControl->getScope();
	}
}