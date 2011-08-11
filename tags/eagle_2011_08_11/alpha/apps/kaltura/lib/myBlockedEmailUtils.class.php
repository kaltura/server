<?php
require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'lib/model/comment.php');

/**
 * Will handle creation of the URL, inserting into the DB and verifying that each email should indeed be sent
 */
class myBlockedEmailUtils
{
	const SEPARATOR = ";";
	const EXPIRY_INTERVAL = 2592000; // 30 days in seconds

	private static $key = "myBlockedEmailUtils";

	public static function createBlockEmailUrl ( $email )
	{
		$url = requestUtils::getWebRootUrl() .  "/mail/blockEmail?e=" . self::createBlockEmailStr ( $email );
		return $url;
	}

	public static function createBlockEmailStr ( $email )
	{
		return  $email . self::SEPARATOR . kString::expiryHash( $email , self::$key , self::EXPIRY_INTERVAL );
	}
	
	// TODO - remove  $should_update_db  - should always update DB !
	public static function blockEmail ( $email_str , $should_update_db = true )
	{
		$params = explode ( self::SEPARATOR , $email_str );
		$email = @$params[0];
		$email_hash = @$params[1];

		$valid = kString::verifyExpiryHash( $email , self::$key , $email_hash , self::EXPIRY_INTERVAL);

		if ( $valid )
		{
			if ( $should_update_db )
			{
				try 
				{
					$blocked_email = new blockedEmail();
					$blocked_email->setEmail  ( $email );
					$blocked_email->save();
				}
				catch ( PropelException $pe )
				{
					// already exists -  think it's better than to query the DB every time to see if the object exists or not 
					// before updating it
				}
			}
		}
		else
		{
			// hashing is wrong !
		}

		return $valid ;
	}

	public static function allowEmail ( $email )
	{
		$blocked_email = blockedEmailPeer::retrieveByPK ( $email );
		if ( $blocked_email )
		{
			$blocked_email->delete();
		}
	}
	
	public static function shouldSendEmail ( $email )
	{
		if ( empty ( $email ) ) return true;
		$exists = blockedEmailPeer::retrieveByPK ( $email );
		return ! $exists;
	}
}
?>