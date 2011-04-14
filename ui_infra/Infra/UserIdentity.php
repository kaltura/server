<?php

class Infra_UserIdentity
{
	/**
	 * Current user object
	 * @var Kaltura_Client_Type_User
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
	 * @param Kaltura_Client_Type_User $user
	 * @param string $ks
	 */
	public function __construct(Kaltura_Client_Type_User $user, $ks)
	{
		$this->user = $user;
		$this->ks = $ks;
	}
	
	/**
	 * @return Kaltura_Client_Type_User saved user object
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
		$client = Infra_ClientHelper::getClient();
		$permissions = $client->permission->getCurrentPermissions();
		$this->permissions = array_map('trim', explode(',', $permissions));
	}
}