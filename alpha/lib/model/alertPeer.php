<?php

/**
 * Subclass for performing query and update operations on the 'alert' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class alertPeer extends BasealertPeer
{
	
	/*
	 * This function checks if a user with param:$kuser_id has an alert of type $alert_type set, and
	 * sends an email to him with additional paramaters $params_array 
	 * 
	 */
	public static function sendEmailIfNeeded( $kuser_id, $alert_type, $params_array )
	{
		$c = new Criteria();
		$c->add( alertPeer::KUSER_ID, $kuser_id );
		$c->add( alertPeer::ALERT_TYPE, $alert_type );
		$alert = self::doSelectOne( $c);
		if ( $alert )
		{
			$alert->setAdditionalParamsArray( $params_array );
			$alert->sendEmailAlert();
		}	
	}
}
