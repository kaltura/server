<?php

//Require the PS 2 bootstrap
require_once (dirname(__FILE__) . "/../bootstrap/bootstrapPS2.php");

/**
 * 
 * Helper for using the PS2
 * @author Roni
 *
 */
class PS2Helper
{	
	/**
	 * 
	 * Calls a PS 2 action with the given array params
	 * @param string $action
	 * @param array $params
	 */
	public static function doHttpRequest($action, array $params= array(), $ks = null)
	{
		$url = kConf::get('apphome_url').'/index.php/partnerservices2/'.$action;
		$params['ks'] = $ks;
				
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, '');
		$opt = http_build_query($params, null, "&");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $opt);
		$result = curl_exec($ch);
		$curlError = curl_error($ch);
		curl_close($ch);
		
		if ($curlError) {
			throw new Exception('CurlError - '.$curlError);
		}
		
		return $result;
	}
	
	/**
	 * 
	 * Gets a user by its user id (using PS2)
	 * @param string $userId
	 */
	public static function ps2GetUser($userId)
	{
		$params = array ('user_id' => $userId);
		return PS2Helper::doHttpRequest('getuser', $params);
	}
	
	/**
	 * 
	 * Deletes a user by its user id (using PS2)
	 * @param string $userId
	 */
	public static function ps2DeleteUser($userId)
	{
		$params = array ('user_id' => $userId);
		return PS2Helper::doHttpRequest('deleteuser', $params);
	}
	
	/**
	 * 
	 * Updates a user by its user id (using PS2)
	 * @param string $user_id
	 * @param string $user_screenName
	 * @param string $user_fullName
	 * @param string $user_email
	 * @param string $user_aboutMe
	 * @param string $user_tags
	 * @param string $user_gender
	 * @param unknown_type $user_partnerData
	 */ 
	public static  function ps2UpdateUser( $user_id = null, $user_screenName = null, $user_fullName = null,	 
									$user_email = null, $user_aboutMe = null, $user_tags = null,
									$user_gender = null, $user_partnerData = null )
	{
		$params = array (
			'user_id' => $user_id, 
			'user_screenName' => $user_screenName,	 
			'user_fullName' => $user_fullName,	 
			'user_email' => $user_email,
			'user_aboutMe' => $user_aboutMe, 
			'user_tags' => $user_tags,
			'user_gender' => $user_gender,
			'user_partnerData' => $user_partnerData,
		);	
		return PS2Helper::doHttpRequest('updateuser', $params);
	}
	
	/**
	 * 
	 * Updates user ID for the given user id with the new user id
	 * @param unknown_type $userId
	 * @param unknown_type $newUserId
	 */
	public static function ps2UpdateUserId($userId, $newUserId)
	{
		$params = array ('user_id' => $userId, 'new_user_id' => $newUserId);
		return PS2Helper::doHttpRequest('updateuserid');
	}
	
	/**
	 * 
	 * Lists all users using PS2
	 */
	public static function ps2ListUsers()
	{
		return PS2Helper::doHttpRequest('listuser', array());
	}

	/**
	 * 
	 * Gets KS for PS2
	 * @param string $secret
	 * @param string $userId
	 * @param KalturaSessionType $type
	 * @param string $partnerId
	 * @param int $expiry
	 * @param unknown_type $privileges
	 */
	public static function getKs ($secret, $userId = "", $type = 0, $partnerId = null, $expiry = 86400 , $privileges = null )
	{
		$ks = '';
		$result = kSessionUtils::startKSession ( $partnerId , $secret , $userId , $ks , $expiry , $type , "" , $privileges);
		
		if ( $result >= 0 ) {
			return $ks;
		}
		else {
			throw new Exception("Error starting admin session for: Partner: {$partnerId}, with Secret: {$secret} \n");
		}
	}
}