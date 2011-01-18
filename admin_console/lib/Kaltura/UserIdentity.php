<?php

class Kaltura_UserIdentity
{
	/**
	 * Current user object
	 * @var KalturaUser
	 */
	private $user;
	/**
	 * Current kaltura session string
	 * @var string
	 */
	private $ks;
	
	/**
	 * Init a new UserIdentity instance with the given parameters
	 * @param KalturaUser $user
	 * @param string $ks
	 */
	public function __construct($user, $ks)
	{
		$this->user = $user;
		$this->ks = $ks;
	}
	
	/**
	 * @return saved user object
	 */
	public function getUser()
	{
		return $this->user;
	}
	
	/**
	 * @return save ks string
	 */
	public function getKs()
	{
		return $this->ks;
	}
}