<?php
/**
 * @package Var
 * @subpackage Authentication
 */
class Kaltura_VarUserIdentity
{
	/**
	 * @var string
	 */
	protected $password;
	
	/**
	 * @return string $password
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @param string $password
	 */
	public function setPassword($password)
	{
		$this->password = $password;
	}
}