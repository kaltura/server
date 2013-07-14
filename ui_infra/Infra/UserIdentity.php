<?php
/**
 * @package UI-infra
 * @subpackage Authentication
 */
class Infra_UserIdentity
{
	/**
	 * Current user object
	 * @var Kaltura_Client_Type_User
	 */
	protected $user;
	
	/**
	 * Current kaltura session string
	 * @var string
	 */
	protected $ks;
	
	/**
	 * Current user permissions
	 * @var array<string>
	 */
	protected $permissions = null;
	
	/**
	 * @var int 
	 */
	protected $timezoneOffset;
	
	/**
	 * Partner id of the current logged-in partner.
	 * @var int
	 */
	protected $partnerId;
	
	/**
	 * Init a new UserIdentity instance with the given parameters
	 * @param Kaltura_Client_Type_User $user
	 * @param string $ks
	 * @param int $partnerId
	 */
	public function __construct(Kaltura_Client_Type_User $user = null, $ks = null, $timezoneOffset = null, $partnerId = null)
	{
		$this->user = $user;
		$this->ks = $ks;
		$this->timezoneOffset = $timezoneOffset;
		$this->partnerId = $partnerId;
	}
	
	/**
	 * @return Kaltura_Client_Type_User saved user object
	 */
	public function getUser()
	{
		return $this->user;
	}
	
	/**
	 * @return string ks string
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
		try{
			$client = Infra_ClientHelper::getClient();
			$permissions = $client->permission->getCurrentPermissions();
			$this->permissions = array_map('trim', explode(',', $permissions));
		}
		catch (Exception $e)
		{
			KalturaLog::err($e->getMessage());
			$this->permissions = array(Kaltura_Client_Enum_PermissionName::ALWAYS_ALLOWED_ACTIONS);
		}
	}
	
	/**
     * @return the $timezoneOffset
     */
    public function getTimezoneOffset ()
    {
        return $this->timezoneOffset;
    }
    
	/**
     * @return int $partnerId
     */
    public function getPartnerId ()
    {
        return $this->partnerId;
    }
}