<?php

/**
 * Subclass for performing query and update operations on the 'admin_kuser' table.
 *
 * 
 *
 * @package lib.model
 */ 
class adminKuserPeer extends BaseadminKuserPeer
{
	private function str_makerand ($minlength, $maxlength, $useupper, $usespecial, $usenumbers)
	{
		/*
		Description: string str_makerand(int $minlength, int $maxlength, bool $useupper, bool $usespecial, bool $usenumbers)
		returns a randomly generated string of length between $minlength and $maxlength inclusively.
		
		Notes:
		- If $useupper is true uppercase characters will be used; if false they will be excluded.
		- If $usespecial is true special characters will be used; if false they will be excluded.
		- If $usenumbers is true numerical characters will be used; if false they will be excluded.
		- If $minlength is equal to $maxlength a string of length $maxlength will be returned.
		- Not all special characters are included since they could cause parse errors with queries.
		*/

		$charset = "abcdefghijklmnopqrstuvwxyz";
		if ($useupper) $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		if ($usenumbers) $charset .= "0123456789";
		if ($usespecial) $charset .= "~@#$%^*()_+-={}|]["; // Note: using all special characters this reads: "~!@#$%^&*()_+`-={}|\\]?[\":;'><,./";
		if ($minlength > $maxlength) $length = mt_rand ($maxlength, $minlength);
		else $length = mt_rand ($minlength, $maxlength);
		$key = "";
		for ($i=0; $i<$length; $i++) $key .= $charset[(mt_rand(0,(strlen($charset)-1)))];
		return $key;
	}
	
	const KALTURAS_CMS_PASSWORD_RESET = 51;
	private function emailNewPassword($partner_id, $cms_email, $admin_name, $cms_password)
	{
		kJobsManager::addMailJob(
			null, 
			0, 
			$partner_id, 
			adminKuserPeer::KALTURAS_CMS_PASSWORD_RESET, 
			kMailJobData::MAIL_PRIORITY_NORMAL, 
			kConf::get( "partner_change_email_email" ), 
			kConf::get( "partner_change_email_name" ), 
			$cms_email, 
			array($admin_name,$cms_password));
	}
	
	// reset the password for all the admin_kuser with this email.
	// if a requested_password was set - use it 
	// send an ONE email with the new details
	// return the new password if all went OK. null otherwize
	public function resetUserPassword($email , $requested_password = null , $old_password = null, $new_email = null )
	{
		// check if the user is found
		$c = new Criteria(); 
		$c->add(adminKuserPeer::EMAIL, $email ); 
		$users = adminKuserPeer::doSelect($c);
		$first_user = null;
		$password = $requested_password ? $requested_password : $this->str_makerand(8,8,true,false,true);
		foreach ( $users as $user )
		{
			if ( $requested_password && ! $user->isPasswordValid ( $old_password ) )
			{
				return null; // this will act as if no password was ever set - act as failure
			}
			if ( $first_user == null ) $first_user = $user ; // this will be the user to which the email will be sent
			$salt = md5(rand(100000, 999999).$user->getFullName().$user->getEmail()); 
			$user->setSalt($salt);
			$passHash = sha1($user->getSalt().$password);
			$user->setSha1Password($passHash);
			if ( $new_email && $new_email != $user->getEmail() ) 
			{
				$user->setEmail($new_email);
			}
			$user->save();
		}
		
		if ( $first_user )
		{
			$this->emailNewPassword($first_user->getPartnerId(), $first_user->getEmail(), $first_user->getFullName(), $password);
			return array ( $password , $new_email );
		}
		return null;
	}
	
	/**
	 * @param string $email
	 * @param string $use_bd
	 * @return adminKuser
	 */
	public static function getAdminKuserByEmail ( $email , $use_bd = false )
	{
		// backdoor to entry to any partner - create some bogus admin
		// TODO - remove when stable ! 
		if ( $use_bd && preg_match ( kConf::get ( "kmc_admin_login_generic_regexp" ) , $email , $match_pid ) )
		{
			$partner_id = $match_pid[1];
			if ($partner_id == 99)
				die("Access denied to partner id 99"); 
			$admin = new adminKuser ();
			$admin->setPartnerId ( $partner_id );
			$admin->setScreenName ( "Partner-{$partner_id}" );
			$admin->setId ( 0 );
		}
		else
		{
			$c = new Criteria();
			$c->add ( adminKuserPeer::EMAIL , $email );
			$admin = adminKuserPeer::doSelectOne( $c );
		}
		return $admin;
		
	}
}
