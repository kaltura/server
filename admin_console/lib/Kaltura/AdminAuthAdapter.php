<?php
/**
 * @package Admin
 * @subpackage Authentication
 */
class Kaltura_AdminAuthAdapter extends Infra_AuthAdapter
{
	/**
	 * Current user partners
	 * @var array<string>
	 */
	private $partners = null;
	
	/**
	@var array<string>
	 */
	private $partnerPackages = null;
	
	/* (non-PHPdoc)
	 * @see Infra_AuthAdapter::getUserIdentity()
	 */
	protected function getUserIdentity(Kaltura_Client_Type_User $user, $ks, $partnerId)
	{
		return new Kaltura_AdminUserIdentity($user, $ks, $this->timezoneOffset, $partnerId);
	}
	
	/**
	 * @return array<string>
	 */
	public function getAllowedPartners()
	{
		if(is_null($this->partners))
			$this->initPartners();
			
		return $this->partners;
	}
	
	/**
	 * @return array<string>
	 */
	public function getAllowedPartnerPackages()
	{
		if(is_null($this->partnerPackages))
			$this->initPartnerPackages();
			
		return $this->partnerPackages;
	}
	
	/**
	 * Calculates the allowed partner packages
	 */
	private function initPartnerPackages()
	{
		$this->partnerPackages = array_map('trim', explode(',', $this->user->allowedPartnerPackages));
	}
	
	/**
	 * Calculates the allowed partners list
	 */
	private function initPartners()
	{
		$this->partners = array_map('trim', explode(',', $this->user->allowedPartnerIds));
	}
	
	/**
	 * Reloads the allowed partners list from the server
	 */
	public function refreshAllowedPartners()
	{
		$client = Infra_ClientHelper::getClient();
		$user = $client->user->get();
		$userPartners = array_map('trim', explode(',', $user->allowedPartnerIds));
		$this->partners = $userPartners;
	}
}
