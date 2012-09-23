<?php
/**
 * @package Admin
 * @subpackage Authentication
 */
class Kaltura_AdminAuthAdapter extends Infra_AuthAdapter
{
	/* (non-PHPdoc)
	 * @see Infra_AuthAdapter::getUserIdentity()
	 */
	protected function getUserIdentity(Kaltura_Client_Type_User $user, $ks, $partnerId)
	{
		return new Kaltura_AdminUserIdentity($user, $ks, $this->timezoneOffset, $partnerId);
	}
}
