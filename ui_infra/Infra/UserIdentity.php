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
	 * Current user partners
	 * @var array<string>
	 */
	private $partners = null;

	/**
	@var array<string>
	 */
	private $parterPackages = null;
	
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
	
	public function getAllowedPartners() {
		if (is_null($this->partners)) {
			$this->initPartners();
		}
		return $this->partners;
	}
	
	public function getAllowedPartnerPackages() {
		if (is_null($this->partnerPackages)) {
			$this->initPartnerPackages();
		}
		return $this->partnerPackages;
	}
	
	private function initPartnerPackages()
	{
		$client = Infra_ClientHelper::getClient();
		$user = $client->user->get($this->user->id, -2);
		$this->partnerPackages = array_map('trim', explode(',',$user->allowedPartnerPackages));
	}
	
	private function initPartners()
	{
		$client = Infra_ClientHelper::getClient();
		$user = $client->user->get($this->user->id);
		$userPartners = array_map('trim', explode(',',$user->allowedPartnerIds));
		$this->partners = $userPartners;
	}
	
	
	
	public function refreshAllowedPartners() {
		$this->initPartners();
	}
}