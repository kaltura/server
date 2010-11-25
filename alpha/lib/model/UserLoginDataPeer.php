<?php


/**
 * Skeleton subclass for performing query and update operations on the 'user_login_data' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class UserLoginDataPeer extends BaseUserLoginDataPeer {

	public static function generateNewPassword()
	{
		$minPassLength = 8;
		$maxPassLength = 14;
		
		$mustCharset[] = 'abcdefghijklmnopqrstuvwxyz';
		$mustCharset[] = '0123456789';
		$mustCharset[] = '~!@#$%^*-=+?()[]{}';
		
		$mustChars = array();
		foreach ($mustCharset as $charset) {
			$mustChars[] = $charset[mt_rand(0, strlen($charset)-1)];
		}
		$newPassword = self::str_makerand($minPassLength-count($mustChars), $maxPassLength-count($mustChars), true, true, true);
		foreach ($mustChars as $c) {
			$i = mt_rand(0, strlen($newPassword));
			$newPassword = substr($newPassword, 0, $i) . $c . substr($newPassword, $i);
		}

		return $newPassword;		
	}
	
	
} // UserLoginDataPeer
