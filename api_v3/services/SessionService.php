<?php

/**
 * Session service
 *
 * @service session
 * @package api
 * @subpackage services
 */
class SessionService extends KalturaBaseService
{
	/**
	 * Start a session with Kaltura's server.
	 * The result KS is the session key that you should pass to all services that requires a ticket.
	 * 
	 * @action start
	 * @param string $secret Remember to provide the correct secret according to the sessionType you want
	 * @param string $userId
	 * @param KalturaSessionType $type Regular session or Admin session
	 * @param int $partnerId
	 * @param int $expiry KS expiry time in seconds
	 * @param string $privileges 
	 * @return string
	 *
	 * @throws APIErrors::START_SESSION_ERROR
	 */
	function startAction($secret, $userId = "", $type = 0, $partnerId = -1, $expiry = 86400 , $privileges = null )
	{
		// make sure the secret fits the one in the partner's table
		$ks = "";
		$result = kSessionUtils::startKSession ( $partnerId , $secret , $userId , $ks , $expiry , $type , "" , $privileges );

		if ( $result >= 0 )
		{
			return $ks;
		}
		else
		{
			throw new KalturaAPIException ( APIErrors::START_SESSION_ERROR ,$partnerId );
		}
	}
	
	
	/**
	 * End a session with the Kaltura server, making the current KS invalid.
	 * 
	 * @action end
	 */
	function endAction()
	{
		$ks = $this->getKs();
		if($ks)
			$ks->kill();
	}

	/**
	 * Start an impersonated session with Kaltura's server.
	 * The result KS is the session key that you should pass to all services that requires a ticket.
	 * 
	 * @action impersonate
	 * @param string $secret Remember to provide the correct secret according to the sessionType you want
	 * @param int $impersonatedPartnerId
	 * @param string $userId
	 * @param KalturaSessionType $type Regular session or Admin session
	 * @param int $partnerId
	 * @param int $expiry KS expiry time in seconds
	 * @param string $privileges 
	 * @return string
	 *
	 * @throws APIErrors::START_SESSION_ERROR
	 */
	function impersonateAction($secret, $impersonatedPartnerId, $userId = "", $type = 0, $partnerId = -1, $expiry = 86400 , $privileges = null )
	{
		// verify partner is allowed to start session for another partner
		$partners = explode(',', $this->partnerGroup());
		if(!in_array($impersonatedPartnerId, $partners) || !isset($impersonatedPartnerId) || is_null($impersonatedPartnerId))
		{
			if($partnerId != $impersonatedPartnerId)
			{
				throw new KalturaAPIException ( APIErrors::START_SESSION_ERROR ,$partnerId );
			}
			else
			{
				// if partner A tries to impersonate as himself - let him 
				$impersonatedPartnerId = $partnerId;
			}
		}
		
		// get impersonated partner
		$impersonatedPartner = PartnerPeer::retrieveByPK($impersonatedPartnerId);
		if(!$impersonatedPartner)
		{
			// impersonated partner could not be fetched from the DB
			throw new KalturaAPIException ( APIErrors::START_SESSION_ERROR ,$partnerId );
		}
		
		// set the correct secret according to required session type
		if($type == KalturaSessionType::ADMIN)
		{
			$impersonatedSecret = $impersonatedPartner->getAdminSecret();
		}
		else
		{
			$impersonatedSecret = $impersonatedPartner->getSecret();
		}
		
		// make sure the secret fits the one in the partner's table
		$ks = "";
		$result = kSessionUtils::startKSession ( $impersonatedPartner->getId() , $impersonatedSecret, $userId , $ks , $expiry , $type , "" , $privileges );

		if ( $result >= 0 )
		{
			return $ks;
		}
		else
		{
			throw new KalturaAPIException ( APIErrors::START_SESSION_ERROR ,$partnerId );
		}
	}
	
	/**
	 * Start a session for Kaltura's flash widgets
	 * 
	 * @action startWidgetSession
	 * @param string $widgetId
	 * @param int $expiry
	 * 
	 * @throws APIErrors::INVALID_WIDGET_ID
	 * @throws APIErrors::MISSING_KS
	 * @throws APIErrors::INVALID_KS
	 * @throws APIErrors::START_WIDGET_SESSION_ERROR
	 * @return KalturaStartWidgetSessionResponse
	 */	
	function startWidgetSession ( $widgetId , $expiry = 86400 )
	{
		// make sure the secret fits the one in the partner's table
		$ksStr = "";
		
		$widget = widgetPeer::retrieveByPK( $widgetId );
		if ( !$widget )
		{
			throw new KalturaAPIException ( APIErrors::INVALID_WIDGET_ID , $widgetId );
		}

		$partnerId = $widget->getPartnerId();

		//$partner = PartnerPeer::retrieveByPK( $partner_id );
		// TODO - see how to decide if the partner has a URL to redirect to


		// according to the partner's policy and the widget's policy - define the privileges of the ks
		// TODO - decide !! - for now only view - any kshow
		$privileges = "view:*";
		$userId = 0;
		/*if ( $widget->getSecurityType() == widget::WIDGET_SECURITY_TYPE_FORCE_KS )
		{
			$user = $this->getKuser();
			if ( ! $this->getKS() )// the one from the base class
				throw new KalturaAPIException ( APIErrors::MISSING_KS );

			$widget_partner_id = $widget->getPartnerId();
			$res = kSessionUtils::validateKSession2 ( 1 ,$widget_partner_id  , $user->getId() , $ks_str , $this->ks );
			
			if ( 0 >= $res )
			{
				// chaned this to be an exception rather than an error
				throw new KalturaAPIException ( APIErrors::INVALID_KS , $ks_str , $res , ks::getErrorStr( $res ));
			}			
		}
		else
		{*/
			// 	the session will be for NON admins and privileges of view only
			$result = kSessionUtils::createKSessionNoValidations ( $partnerId , $userId , $ksStr , $expiry , false , "" , $privileges );
		//}

		if ( $result >= 0 )
		{
			$response = new KalturaStartWidgetSessionResponse();
			$response->partnerId = $partnerId;
			$response->ks = $ksStr;
			$response->userId = $userId;
			return $response;
		}
		else
		{
			// TODO - see that there is a good error for when the invalid login count exceed s the max
			throw new  KalturaAPIException  ( APIErrors::START_WIDGET_SESSION_ERROR ,$widgetId );
		}		
	}
}