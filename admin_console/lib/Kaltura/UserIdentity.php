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
	 * Current user permissions
	 * @var array<string>
	 */
	private $permissions = null;
	
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
	
	public function getPermissions()
	{
		if (is_null($this->permissions)) {
			$this->initPermissions();
		}
		
		return $this->permissions;
	}
	
	private function initPermissions()
	{
		$client = Kaltura_ClientHelper::getClient();
		$permissions = $client->permission->getCurrentPermissions();
		$this->permissions = array_map('trim', explode(',', $permissions));
	}
}