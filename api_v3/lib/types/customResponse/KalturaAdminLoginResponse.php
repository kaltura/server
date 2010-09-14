<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAdminLoginResponse extends KalturaObject
{
	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;

	/**
	 * @var string
	 * @readonly
	 */
	public $ks;
	
	/**
	 * @var string
	 * @readonly
	 */
	public $userId;
	
	/**
	 * @var KalturaAdminUser
	 * @readonly
	 */
	public $adminUser;
	
	public function fromObjects( adminKuser $kuser , $ks , PuserKuser $puser_kuser , Partner $partner )
	{
		//parent::fromObject( $kuser );
		$adminUser = new KalturaAdminUser;
		$adminUser->fromObject( $kuser );
		$this->adminUser = $adminUser;
		$this->partnerId = $partner->getId();
		$this->subpId = $partner->getSubpId();
		$this->ks = $ks;
		$this->userId = $puser_kuser->getPuserId();
	}
}