<?php
/**
 * @package Admin
 * @subpackage Authentication
 */
class Kaltura_AdminUserIdentity extends Infra_UserIdentity
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
	
	public static function isAllowedPartner($partnerId, $partnerPackage)
	{
		if(Infra_AuthHelper::getAuthInstance()->hasIdentity())
		{
			$partners = Infra_AuthHelper::getAuthInstance()->getIdentity()->getAllowedPartners();
			if(in_array('*', $partners))
			{
				return true;
			}
			$packages = Infra_AuthHelper::getAuthInstance()->getIdentity()->getAllowedPartnerPackages();
			if(in_array($partnerPackage, $packages))
			{
				return true;
			}
			return in_array((string)$partnerId, $partners);
		}
		return false;
	}
	
	/**
	 * Refresh the list of partners the user is allowed to access.
	 */
	public static function refreshCurrentUserAllowedPartners()
	{
		if(Infra_AuthHelper::getAuthInstance()->hasIdentity())
		{
			Infra_AuthHelper::getAuthInstance()->getIdentity()->refreshAllowedPartners();
		}
	}
}