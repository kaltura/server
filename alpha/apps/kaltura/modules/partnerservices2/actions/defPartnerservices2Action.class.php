<?php

require_once ( "webservices/kalturaBaseWebserviceAction.class.php");
require_once ( "webservices/APIErrors.class.php");
require_once ( "webservices/APIException.class.php");
require_once ( "myPartnerUtils.class.php");
require_once ( "webservices/kSessionUtils.class.php");

/**
 * This class is NOT a symfony action any more.
 * It is the base class for all the kaltura servies.
 * When used from an action, the action can pass itself to the ctor  so that at the end the response type will
 *
 * @package    kaltura
 * @subpackage emailImport
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
abstract class defPartnerservices2Action //extends kalturaBaseWebserviceAction
{
	const SIG_TYPE_POST = 1;
	const SIG_TYPE_GET = 2;
	const SIG_TYPE_COOKIE = 3;
	const SIG_TYPE_REQUEST = 4;

	const KUSER_DATA_NO_KUSER = 0;
	const KUSER_DATA_KUSER_ID_ONLY = 1;
	const KUSER_DATA_KUSER_DATA = 2;

	const CREATE_USER_FALSE = 0;
	const CREATE_USER_FROM_PARTNER_SETTINGS = 1;
	const CREATE_USER_FORCE = 2;
/*
	const REQUIED_TICKET_NOT_ACCESSIBLE = 'N';
	const REQUIED_TICKET_NONE = 0;
	const REQUIED_TICKET_REGULAR = 1;
	const REQUIED_TICKET_ADMIN = 2;
*/
	const __TOTAL_TIME__ = "__TOTAL_TIME__";

	const DEFAULT_FORMAT  = 2; // XML ?
	//	protected $kshow_id;
	//	protected $kshow;
	private $msg;
	private $error;
	private $debug;

	protected $ks;

	protected $response_context = null;

	protected $benchmarks = array();
	protected $benchmarks_names = array();
	
	protected $should_debug = true;
	protected $response_type = kalturaWebserviceRenderer::RESPONSE_TYPE_XML;

	protected static $escape_text = true;

	protected $input_params = null;

	// this will be the calling container that holds HTTP inforamtion
	protected $container_action = null;

	private $force_ticket_check = true;

	private $partner = null;
	private $operating_partner = null;
	
	// the esrvice will be the one of the operating_partner
	private $service_config = null;
	
	private $private_partner_data; // will be set after the validation
	
	public function defPartnerservices2Action ( $container_action = null )
	{
		self::$escape_text = true;
		// hold a refernce to the container in case there are callbacks to execute
		$this->container_action = $container_action;
	}

	public function setInputParams ( $params )	{		 $this->input_params = $params;	}

	protected function getInputParams ()	{		return $this->input_params;	}

	public function setResponseContext ( $response_context )	{		$this->response_context = $response_context;	}

	/**
	 * return the current active partner
	 *
	 * @return Partner
	 */
	protected function getPartner()	{		return $this->partner;	}

	protected function getOperatingPartner()	{		return $this->operating_partner;	}
	
	protected function setP ( $param_name , $param_value  )	{		 $this->input_params[$param_name] = $param_value;	}

	// get Paraameter Mandatory
	protected function getPM ( $param_name , $allow_zero = false )
	{
		$res = $this->getP ( $param_name , null , $allow_zero );
		if ( ! $res ) 
		{
			$this->addException( APIErrors::MANDATORY_PARAMETER_MISSING , $param_name );
		}
		return $res;
	}
	
	// TODO - fix the name of the params - always remove underscore and move to lowercase
	protected function getP ( $param_name , $default_val = NULL , $allow_zero = false )
	{
		if ( $this->input_params != null && is_array ( $this->input_params ))
		{
			$value = @$this->input_params[$param_name];
			if ( $allow_zero && ($value === '0' ) ) return "0";
			if ( ! $value && $default_val )
				return $default_val;
			return $value;
		}
		else
		{
			return requestUtils::getParameter ( $param_name , $default_val );
		}
	}

	protected function isAdmin()
	{
		// in case there is no ks - return false
		if ( $this->ks )
			return $this->ks->isAdmin();
		return false;
	}
	
	public function logMessage($message, $priority = SF_LOG_INFO )
	{
	}


	// the cahcekey is an array with a string (the key) and an integer (the expiry)
	private function getExecutionCacheKeyWrapper ( $partner_id , $subp_id , $puser_id  )
	{
		$cachekey = $this->getExecutionCacheKey( $partner_id , $subp_id , $puser_id  );
		if ( $cachekey != null ) 
		{
			$cachekey->service = get_class($this);
		}
		return $cachekey;
	}

	// TODO - add ability to decide not to cache in the executeImpl - for now it cannot be done because the cachekey is returned before the implementation
	// services that choose to use the cache key - should return an md5 of the relvant parameters that should be used in the key	
	protected function getExecutionCacheKey ( $partner_id , $subp_id , $puser_id  )
	{
		return null;
	}
	
	// the interface include 4 paramters
	// the first 3 will not be empty
	// $puser_kuser might be according to the method needKuserFromPuser()
	abstract protected function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser );

public function INFO__ticketType () { return $this->ticketType(); }
public function INFO__requiredPrivileges () { return $this->requiredPrivileges (); }
public function INFO__needKuserFromPuser () { return $this->needKuserFromPuser (); }
public function INFO__addUserOnDemand () { return $this->addUserOnDemand (); }
public function INFO__allowEmptyPuser () { return $this->allowEmptyPuser (); }
	
	
	// should be true for all services except for the startSession service - which starts the session
	protected function ticketType ()	{		return self::REQUIED_TICKET_REGULAR;	}
	protected function ticketType2 ()	{		return $this->getServiceConfig()->getTicketType();	}

	public function requiredPrivileges ()	{		return null;	}
//	public function requiredPrivileges2 ()	{		return $this->getServiceConfig()->getRequiredPrivileges();	}

	protected function verifyPrivileges ( $priv_name , $priv_value = null  )
	{
		$matched_privs = $this->ks->verifyPrivileges ( $priv_name , $priv_value  );
		$this->logMessage( "verifyPrivileges name [$priv_name], priv [$priv_value] [$matched_privs]" );		

		if ( ! $matched_privs )
			throw new Exception ( "Did not match required privlieges [$priv_name:$priv_value]" );
	}

	// this can be overriden incase a service does not need the kuser.
	// it has 3 levels:
	// KUSER_DATA_NO_KUSER - will not fetch data about this puser
	// KUSER_DATA_KUSER_ID_ONLY - will get data from puser_kuser but only the kuser_id
	// KUSER_DATA_KUSER_DATA - will fetch data from puser_kuser & from kuser tables
	protected function needKuserFromPuser ( )	{		return self::KUSER_DATA_KUSER_ID_ONLY;	}
	protected function needKuserFromPuser2 ( )	{		return $this->getServiceConfig()->getNeedKuserFromPuser();	}

	protected function addUserOnDemand ( )	{		return self::CREATE_USER_FALSE;	}
	protected function addUserOnDemand2 ( )	{		return $this->getServiceConfig()->getCreateUserOnDemand();	}

	// if a specific allows empty user (empty uid) - override this with true
	protected function allowEmptyPuser()	{		return true;	}
	protected function allowEmptyPuser2 ()	{		return $this->getServiceConfig()->getAllowEmptyPuser();	}

	// altough there was never kalturaNetwork - keep the 2 as a suffix
	protected function kalturaNetwork2() { return $this->getServiceConfig()->getKalturaNetwork();	}
	
	// altough there was never partnerGroup - keep the 2 as a suffix	
	protected function partnerGroup2() { return $this->getServiceConfig()->getPartnerGroup();	}

	// altough there was never matchIp - keep the 2 as a suffix	
	protected function matchIp2() { return $this->getServiceConfig()->getMatchIp();	}

	// altough there was never requirePartner - keep the 2 as a suffix	
	protected function requirePartner2() { return $this->getServiceConfig()->getRequirePartner();	}
	
	// this will help each service describe the input and output structures
	abstract function describe();


	/**
	 * Will execute the executeImpl method with a little change:
	 * the ouput will be an associative-list of name + OBJECTs - the originals NOT THE WRAPPERS !
	 */
	public function internalExecute ( $add_extra_debug_data = false )
	{
		// ignore ticketType
		$this->response_type = $this->getP ( "format" , self::DEFAULT_FORMAT ); //

		$this->force_ticket_check = false; // HERE AND ONLY HERE !!
		if ($this->response_type == kalturaWebserviceRenderer::RESPONSE_TYPE_PHP_OBJECT )
		{
			// 	return objects - NOT the wrapped objects
			objectWrapperBase::shouldWrap( false );
		}
		$res = $this->execute( $add_extra_debug_data );
		return $res;
	}

	public function execute( $add_extra_debug_data = true )
	{
		date_default_timezone_set( kConf::get ( "date_default_timezone" ) /*America/New_York*/ );
		
		// TODO - remove for production - use some configuration to determine
		kConfigTable::$should_use_cache = false;
		
		$start_impl = $end_impl = 0;
		
		$nocache = false;
		if ( $this->getP ( "nocache" ) )
		{
			$nocache = true;
			$this->logMessage( "Not using cache!");
			objectWrapperBase::useCache( false );
		}

		$add_benchmarks = $this->getP ( "add_benchmarks" , false );

		// while testing our system - will match each service agains its description
		// $this->matchInDescription();

		$this->msg = array();
		$this->error = array();
		$this->debug = array();
		$start_time = microtime(true);
		$start = microtime( true );
$this->benchmarkStart( "beforeImpl" );		
$this->benchmarkStart( "signature" );		
		$sig_type = $this->getP ( "sigtype" , self::SIG_TYPE_POST );
		$signature_params = self::getParamsFromSigType ( $sig_type );
		$signatura_valid = self::validateSignature( $signature_params );
$this->benchmarkEnd( "signature" );
		$this->response_type = $this->getP ( "format" , self::DEFAULT_FORMAT ); //

/*
		$should_debug = $this->getP ( "should_debug" , true );
		if ( $should_debug == "false" ) $this->should_debug  = false;
 */
		if ( $this->should_debug && $add_extra_debug_data )
		{
			$this->addDebug( "sigtype" , $sig_type );
			$this->addDebug( "validateSignature" , $signatura_valid );
			$this->addDebug( "signature" , self::signature( $signature_params ) );
//			$this->addDebug( "rawsignature" , self::signature( $signature_params , false ) );
		}

		$partner_id = $this->getP ( "partner_id");
		if(!$partner_id)
			$partner_id = $this->getP ( "partnerId");
			
		$subp_id = $this->getP ( "subp_id" );
		if(!$subp_id)
			$subp_id = $this->getP ( "subpId");
			
		$puser_id = $this->getP ( "uid" );
		$ks_str = $this->getP ( "ks" );
		if ( $ks_str == "{ks}" )  $ks_str = ""; // if the client DIDN'T replace the dynamic ks - ignore it 
		
		// the $execution_cache_key can be used by services to cache the results depending on the inpu parameters
		// if the $execution_cache_key is not null, the rendere will search for the result of the rendering depending on the $execution_cache_key
		// if it doesn't find it - it will create it (per format) and store it for next time
		$execution_cache_key = null;

		// moved the renderer here to see if has the $execution_cache_key and if so - skip the implementation
		$renderer = new kalturaWebserviceRenderer( $this->response_context );
		
		$private_partner_data = false;
		
		try
		{
			$arr = list ( $partner_id , $subp_id , $uid , $private_partner_data ) = $this->validateTicketSetPartner ( $partner_id , $subp_id , $puser_id , $ks_str );
			
			// if PS2 permission validation is enabled for the current partner, only the actions defined in kConf's parameter "ps2_actions_not_blocked_by_permissions" will be allowed
			$currentPartner = $this->getPartner();
			if ($currentPartner && $currentPartner->getEnabledService(PermissionName::FEATURE_PS2_PERMISSIONS_VALIDATION))
			{
				if (!in_array(strtolower(get_class($this)), kConf::get('ps2_actions_not_blocked_by_permissions')))
				{
					KalturaLog::log('PS2 action '.get_class($this).' is being blocked for partner '.$currentPartner->getId().' defined with FEATURE_PS2_PERMISSIONS_VALIDATION enabled');
					$this->addException( APIErrors::SERVICE_FORBIDDEN );
				}
			}
			
			$this->private_partner_data = $private_partner_data;
//print_r ( $arr ); 
			// TODO - validate the matchIp is ok with the user's IP
			$this->validateIp ( );

			// most services should not attempt to cache the results - for them this will return null 
			$execution_cache_key = $this->getExecutionCacheKeyWrapper ( $partner_id , $subp_id , $puser_id  );
			
			// if the key is not null - it will be used in the renderer for using the cotent from the cache
			if ( $nocache ) $renderer->deleteCacheKey ( $execution_cache_key , $this->response_type ) ;
			else $renderer->setCacheKey( $execution_cache_key );
			
			if ( ! $renderer->hasContentForCacheKey ( $this->response_type ) )
			{
				
	$this->benchmarkStart( "applyPartnerFilters" );
			// apply filters for Criteria so there will be no chance of exposure of date from other partners !
			// TODO - add the parameter for allowing kaltura network
			myPartnerUtils::applyPartnerFilters ( $partner_id , $private_partner_data , $this->partnerGroup2() , $this->kalturaNetwork2()  );
	$this->benchmarkEnd( "applyPartnerFilters" );
	$this->benchmarkStart( "puserKuser" );						
				list ( $partner_id , $subp_id , $puser_id , $partner_prefix ) = $this->preparePartnerPuserDetails ( $partner_id , $subp_id , $puser_id );
				$puser_kuser = $this->getPuserKuser ( $partner_id , $subp_id, $puser_id );
	$this->benchmarkEnd( "puserKuser" );		
	$this->benchmarkEnd( "beforeImpl" );

				// ----------------------------- impl --------------------------
				$start_impl = microtime( true );
				$result = $this->executeImpl( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser );
				$end_impl = microtime( true );
			}
			else
			{
				/// the renderer claims to have the desired result ! just flow down the code ... ;)
			}
				// ----------------------------- impl --------------------------				
		}
		catch ( APIException $api_ex )
		{
			$message = $api_ex->getMessage();
			if ( $this->should_debug && $message )
			{
				$this->addError ( APIErrors::SERVERL_ERROR , "[$message]" );
			}
			elseif ( $api_ex->api_code )
			{
				call_user_func_array( array ( &$this , 'addError' ), $api_ex->extra_data ); 
//				$this->addError ( $api_ex->api_code ,$api_ex->extra_data );
			}
		}
		catch ( Exception $ex )
		{
			// TODO -  in this case there is no reason to display the message - its internal - remove the message
			$this->addError ( APIErrors::INTERNAL_SERVERL_ERROR , $ex->getMessage());
			$this->logMessage("Error: " . $ex->getMessage() );
		}

		$execute_impl_end_time = microtime(true);
		// render according to the format_type
		$res = array();
		$this->addMsg( "serverTime" , time() );
		$res['result'] = $this->msg;
		$res['error'] = $this->error;

		if ( $this->should_debug )
		{
			// this specific debug line should be used
			$this->addDebug ( "execute_impl_time" , $end_impl - $start_impl);
			$this->addDebug ( "execute_time" , $execute_impl_end_time - $start_time );
			
			// will be used as a place holder and will be replaced after the rendering.
			if ( $add_extra_debug_data )
			{
				$this->addDebug ( "total_time" , self::__TOTAL_TIME__ );
			}
			
			if ( $add_benchmarks && count($this->benchmarks) > 0 )
			{
				$this->addDebug ( "host" , @$_ENV["HOSTNAME"] );
				$this->addDebug ( "benchmarks" , $this->getBenchmarks() );
			}
			
			$res['debug'] = $this->debug;
		}

		// ignore all the errors and debug - the first msg is the only html used
		if ( $this->response_type == kalturaWebserviceRenderer::RESPONSE_TYPE_HTML )
		{
			$res = "<html>";
			foreach ( $this->msg as $html_bit )
			{
				$res .= $html_bit;
			}
			$res .= "</html>";
		}
		if ( $this->response_type == kalturaWebserviceRenderer::RESPONSE_TYPE_MRSS )
		{
			// in case of mRss - render only the result not the errors ot the debug
			list ( $response , $content_type ) = $renderer->renderDataInRequestedFormat( $res['result'] , $this->response_type , true , self::$escape_text );
		}
		else
		{
			list ( $response , $content_type ) = $renderer->renderDataInRequestedFormat( $res , $this->response_type , true , self::$escape_text );
		}
		$end_time = microtime( true );

		if ( is_string($response))
		{
			$this->logMessage( "Rendereing took: [" . ( $end_time - $start_time ) . "] seconds. Response size [" . strlen( $response ). "]" , SF_LOG_WARNING );
			$this->logMessage( $response , SF_LOG_WARNING );
		}
		else
		{
			$this->logMessage( "Rendereing took: [" . ( $end_time - $start_time ) . "]" ) ;
		}

		if ( $this->should_debug && $add_extra_debug_data )
		{
			// fix the total time including the render time
			$str_time = (string)($end_time - $start_time );
			if ( $this->response_type == kalturaWebserviceRenderer::RESPONSE_TYPE_PHP )
			{
				// replcate the placehoder with the real execution time
				// this is a nasty hack - we replace the serialized PHP value - the length of the placeholder is 14 characters
				// the length of the str_time can be less - replace the whole string phrase
				$replace_string = 's:' . strlen ( $str_time ) .':"' . $str_time ;
				$response = str_replace( 's:14:"' . self::__TOTAL_TIME__ , $replace_string , $response );
			}
			elseif ( $this->response_type == kalturaWebserviceRenderer::RESPONSE_TYPE_PHP_ARRAY || $this->response_type == kalturaWebserviceRenderer::RESPONSE_TYPE_PHP_OBJECT )
			{
				// the $response is not a string - we can't just replace it
				$res["debug"]["total_time"] = $str_time;
			}
			elseif ( $this->response_type == kalturaWebserviceRenderer::RESPONSE_TYPE_MRSS )
			{
				// do nothing to the result
			}
			else
			{
				$response = str_replace( self::__TOTAL_TIME__ , $str_time , $response );
			}
		}

		$this->setContentType( $content_type );

		// while testing our system - will match each service agains its description
		// $this->matchOutDescription();

		return $response ;
	}

	
	/*

	 * validataTicketSetPartner
	 * 
	 * if the is a ks_str - 
	 * 1. crack down the ticket 
	 * 2. extract partner_id
	 * 3. retrieve partner
	 * 4. validate ticket per service for the ticket's partner
	 * 5. see partner is allowed to access the desired partner (if himself - easy, else - should appear in the partnerGroup)
	 * 6. set the partner to be the desired partner and the operating_partner to be the one from the ks 
	 * 7. if ok - return the partner_id to be used from this point onwards 
	 * 
	 * if there is not a ks_str 
	 * 1. extract partner by partner_id
	 * 2. retrieve partner
	 * 3. make sure the service can be accessed with no ticket 
	 * 4. set the partner & operating_partner to be the one-and-only partner of this session  
	 * 5. return partner_id

	 */
	// TODO - what about the puser_id in this case ?? - shold create some 'guest-<operating_partner_id> ? 
	// should take the uid as-is assuming it's from the partner_id that is being impostured ?? 
	private function validateTicketSetPartner ( $partner_id , $subp_id , $puser_id , $ks_str )
	{
		if ( $ks_str )
		{
			// 	1. crack the ks - 
			$ks = kSessionUtils::crackKs ( $ks_str );
			
			// 2. extract partner_id
			$ks_partner_id= $ks->partner_id;
			$master_partner_id = $ks->master_partner_id;
			if(!$master_partner_id)
				$master_partner_id = $ks_partner_id;

			if ( ! $partner_id ) $partner_id = $ks_partner_id;
			// use the user from the ks if not explicity set 
			if ( ! $puser_id ) $puser_id = $ks->user;
			
			kCurrentContext::$ks = $ks_str;
			kCurrentContext::$partner_id = $partner_id;
			kCurrentContext::$ks_partner_id = $ks_partner_id;
			kCurrentContext::$master_partner_id = $master_partner_id;
			kCurrentContext::$uid = $puser_id;
			kCurrentContext::$ks_uid = $ks->user;

			// 3. retrieve partner
			$ks_partner = PartnerPeer::retrieveByPK( $ks_partner_id );
			// the service_confgi is assumed to be the one of the operating_partner == ks_partner

			if ( ! $ks_partner )
			{
				$this->addException( APIErrors::UNKNOWN_PARTNER_ID , $ks_partner_id );
			}
			
			$this->setServiceConfigFromPartner( $ks_partner );
			if ( $ks_partner && ! $ks_partner->getStatus() )
			{
				$this->addException( APIErrors::SERVICE_FORBIDDEN_PARTNER_DELETED );
			}
			
			// 4. validate ticket per service for the ticket's partner
			$ticket_type = $this->ticketType2();
			if ( $ticket_type == kSessionUtils::REQUIED_TICKET_NOT_ACCESSIBLE )
			{
				// partner cannot access this service
				$this->addException( APIErrors::SERVICE_FORBIDDEN );
			}
			
			if ( $this->force_ticket_check && $ticket_type != kSessionUtils::REQUIED_TICKET_NONE )
			{
				// TODO - which user is this ? from the ks ? from the puser_id ? 
				$ks_puser_id = $ks->user;
				//$ks = null;
				$res = kSessionUtils::validateKSession2 ( $ticket_type , $ks_partner_id , $ks_puser_id , $ks_str , $ks );

				if ( 0 >= $res )
				{
					// chaned this to be an exception rather than an error
					$this->addException ( APIErrors::INVALID_KS , $ks_str , $res , ks::getErrorStr( $res ));
				}
				$this->ks = $ks;
			}
			elseif ($ticket_type == kSessionUtils::REQUIED_TICKET_NONE && $ks_str) // ticket is not required but we have ks
			{
				$ks_puser_id = $ks->user;
				$res = kSessionUtils::validateKSession2 ( $ticket_type , $ks_partner_id , $ks_puser_id , $ks_str , $ks );
				if ( $res > 0)
				{
					$this->ks = $ks;
				}
			}
			// 5. see partner is allowed to access the desired partner (if himself - easy, else - should appear in the partnerGroup)
			$allow_access = myPartnerUtils::allowPartnerAccessPartner ( $ks_partner_id , $this->partnerGroup2() , $partner_id );
			if ( ! $allow_access )
			{
				$this->addException( APIErrors::PARTNER_ACCESS_FORBIDDEN , $ks_partner_id , $partner_id ); 
			}
			
			// 6. set the partner to be the desired partner and the operating_partner to be the one from the ks
			$this->partner = PartnerPeer::retrieveByPK( $partner_id );
			$this->operating_partner = $ks_partner;
			// the config is that of the ks_partner NOT of the partner
			// $this->setServiceConfigFromPartner( $ks_partner ); - was already set above to extract the ks
			// TODO - should change  service_config to be the one of the partner_id ?? 

			// 7. if ok - return the partner_id to be used from this point onwards 
			return array ( $partner_id , $subp_id , $puser_id , true ); // allow private_partner_data
		}
		else
		{
			// no ks_str
	 		// 1. extract partner by partner_id +
			// 2. retrieve partner
	 		$this->partner = PartnerPeer::retrieveByPK( $partner_id );
			if ( ! $this->partner )
			{
				$this->partner = null;
				{
					// go to the default config 
					$this->setServiceConfigFromPartner( null );
				}
				
				if ( $this->requirePartner2() )
				{
					$this->addException( APIErrors::UNKNOWN_PARTNER_ID , $partner_id );
				}
			}
			if ( $this->partner && ! $this->partner->getStatus() )
			{
				$this->addException( APIErrors::SERVICE_FORBIDDEN_PARTNER_DELETED );
			}

			kCurrentContext::$ks = null;
			kCurrentContext::$partner_id = $partner_id;
			kCurrentContext::$ks_partner_id = null;
			kCurrentContext::$uid = $puser_id;
			kCurrentContext::$ks_uid = null;
			
			
			// 3. make sure the service can be accessed with no ticket
 			$this->setServiceConfigFromPartner( $this->partner );
			$ticket_type = $this->ticketType2();
			if ( $ticket_type == kSessionUtils::REQUIED_TICKET_NOT_ACCESSIBLE )
			{
				// partner cannot access this service
				$this->addException( APIErrors::SERVICE_FORBIDDEN );
			}
			if ( $this->force_ticket_check && $ticket_type != kSessionUtils::REQUIED_TICKET_NONE )
			{
				// NEW: 2008-12-28
				// Instead of throwing an exception, see if the service allows KN.
				// If so - a relativly week partner access 
				if ( $this->kalturaNetwork2() )
				{
					// if the service supports KN - continue without private data 
					return array ( $partner_id , $subp_id , $puser_id , false ); // DONT allow private_partner_data
				}
				
				// chaned this to be an exception rather than an error
				$this->addException ( APIErrors::MISSING_KS  );
			}
			
			// 4. set the partner & operating_partner to be the one-and-only partner of this session
			$this->operating_partner = $this->partner;
			return array ( $partner_id , $subp_id , $puser_id , true ); // allow private_partner_data			
		}
	}

	private function validateIp ( )
	{
		if ( ! $this->matchIp2() ) return; // no need to match the IP
		$ip_to_match = $this->getPartner()->getMatchIp();
		if ( ! $ip_to_match ) return ; // althogh the service requires the match - the partner didn't specify the ip prefix.
		$user_ip = null;
		if ( ! requestUtils::validateIp( $ip_to_match , $user_ip ) )
		{
			$this->addException( APIErrors::ACCESS_FORBIDDEN_FROM_UNKNOWN_IP , $user_ip );		
		}
	}
	
	
	private function setServiceConfigFromPartner (  $partner )
	{
		$service_name = str_replace ( "Action" , "" , get_class( $this ) ); // service name is the class name without the word Action
		if ( $partner && $partner->getStatus() == Partner::PARTNER_STATUS_CONTENT_BLOCK )
		{
			$partner_services_config = $partner->getServiceConfigId();
			$partner->setServiceConfigId( Partner::CONTENT_BLOCK_SERVICE_CONFIG_ID );
			$this->service_config = myPartnerUtils::getServiceConfig( $partner );
			$partner->setServiceConfigId( $partner_services_config );
		}
		elseif ( $partner && $partner->getStatus() == Partner::PARTNER_STATUS_FULL_BLOCK )
		{
			$partner_services_config = $partner->getServiceConfigId();
			$partner->setServiceConfigId( Partner::FULL_BLOCK_SERVICE_CONFIG_ID );
			$this->service_config = myPartnerUtils::getServiceConfig( $partner );
			$partner->setServiceConfigId( $partner_services_config );
		}
		else
		{
			$this->service_config = myPartnerUtils::getServiceConfig( $partner );			
		}
		
		kCurrentContext::$host = @$_SERVER["HOSTNAME"];
		kCurrentContext::$user_ip = requestUtils::getRemoteAddress();
		kCurrentContext::$ps_vesion = "ps2";
		kCurrentContext::$service = "partnerservices2";
		kCurrentContext::$action =  $service_name;
		
		
		$this->service_config->setServiceName ( $service_name );
	}
	
	private function getServiceConfig ()
	{
		return $this->service_config;
	}
	
	protected function  securityViolation( $kshow_id )
	{
		$xml = "<xml><kshow id=\"$kshow_id\" securityViolation=\"true\"/></xml>";
		$this->getResponse()->setHttpHeader ( "Content-Type" , "text/xml; charset=utf-8" );
		$this->getController()->setRenderMode ( sfView::RENDER_NONE );
		return $this->renderText( $xml );
	}

	// TODO - combine with preparePartnerPuserDetails and createUserOnDemand - no need for 3 functions
	private function getPuserKuser ( $partner_id , $subp_id, $puser_id )
	{
		// TODO - remove dead code
		//$fetch_kuser_data = $this->needKuserFromPuser();
		$fetch_kuser_data = $this->needKuserFromPuser2();

		if ( $fetch_kuser_data == self::KUSER_DATA_NO_KUSER )
		{
			$puser_kuser = null;
		}
		else
		{
			$join_with_kuser = ( $fetch_kuser_data == self::KUSER_DATA_KUSER_DATA ); // decide if to fetch extra data about kuser
			$puser_kuser = PuserKuserPeer::retrieveByPartnerAndUid ( $partner_id , null/*$subp_id*/,  $puser_id , $join_with_kuser );
		}
		// for forward compatibility with PS3 - try to get Kuser by puserID
		$kuser = kuserPeer::getKuserByPartnerAndUid($partner_id, $puser_id);
		if(!$kuser) $kuser = null;
		// will create the user (puser_kuser + kuser) if necessary,
		// if $kuser exists send it as well so only puser_kuser will be created for that kuser
		$puser_kuser = $this->createUserOnDemand ($partner_id , $subp_id, $puser_id , $puser_kuser, $kuser );

		// if $puser_kuser is still null after fetching it / creating it, and was supposed to be here - display error
		if ( $fetch_kuser_data != self::KUSER_DATA_NO_KUSER && $puser_kuser === null  )
		{
			$this->addError( APIErrors::INVALID_USER_ID ,$puser_id );
		}

		return $puser_kuser;
	}
	

	// TODO - change method -  the partner was already validated 
	protected function preparePartnerPuserDetails ( $partner_id , $subp_id , $puser_id )
	{
		$partner_prefix = myPartnerUtils::getPrefix ( $partner_id );

		if ( $partner_prefix === null && ! $this->requirePartner2() )
		{
//			$this->addError ( "unknown partner_id($partner_id)" );
			$this->addException( APIErrors::UNKNOWN_PARTNER_ID , $partner_id );
//			throw new Exception ( "unknown partner_id ($partner_id)" );
		}

		// allow puser_id=0 - this might be some anonymous user on the partner's side
		// TODO - dead code
		//if ( empty ( $puser_id ) && ( $puser_id !== "0" ) && !$this->allowEmptyPuser() )
		if ( empty ( $puser_id ) && ( $puser_id !== "0" ) && !$this->allowEmptyPuser2() )
		{
//			$this->addError ( "Invalid puser ($partner_id,$puser_id)" );
			throw new Exception ( "invalid puser ($partner_id,$puser_id)");
		}

		// verify the signature
		// TODO - security!
		$puserhash = $this->getP ( "puserhash" );

		return array ( $partner_id , $subp_id , $puser_id , $partner_prefix );
	}

	/**
	 * Fetch data about the kuser puser (kuser) and the relevant kshow_id.
	 * $verify_producer_only - set to true if want to make ssre the kuser is indeed the producer of the kshow
	 */
	protected function getKshowAndKuser ( $partner_id , $puser_id , $verify_producer_only = false )
	{
		$kshow_id = $this->getP ( "kshow_id" );

		$kshow = kshowPeer::retrieveByPK( $kshow_id );
		if ( !$kshow )
		{
			// TODO - error
//			$this->addError ( "No such kshow [$kshow_id]" );
			throw new Exception ( "No such kshow [$kshow_id]" );
		}

		$kuser = kuserPeer::getKuserByPartnerAndUid( $partner_id , $puser_id );
		if ( ! $kuser )
		{
//			$this->addError ( "puser ($partner_id,$puser_id) does not exist" );
			throw new Exception (  "puser ($partner_id,$puser_id) does not exist" );
		}

		$kuser_id = $kuser->getId();
		if ( $verify_producer_only )
		{
			// make sure the puser (kuser) is the producer of the kshow
			if ( $kshow->getProducerId() != $kuser_id )
			{
//				$this->addError ( "puser ($partner_id,$puser_id) cannot publish kshow [$kshow_id]" );
				throw new Exception ( "puser ($partner_id,$puser_id) cannot publish kshow [$kshow_id]" );
			}
		}
		return array ( $kshow , $kuser );
	}


	protected function forceProducerOnly ( $partner_id , $puser_id , $kshow_id )
	{
		$kshow = kshowPeer::retrieveByPK( $kshow_id );
		if ( ! $kshow )
		{
			$this->addError ( "no such kshow [$kshow_id]" );
			throw new Exception();
		}

		if ( $kshow->getProducerId() != $kshow_id )
		{
			$this->addError ( "no such kshow [$kshow_id]" );
			throw new Exception();
		}
	}

	protected function getKsUniqueString()
	{
		if ( $this->ks )
		{
			return $this->ks->getUniqueString();
		}
		else
		{
			return substr ( md5( rand ( 10000,99999 ) . microtime(true) ) , 1 , 7 );
			//throw new Exception ( "Cannot find unique string" );
		}

	}

	// TODO - move to nyPartnerUtils
	protected function createUserOnDemand ( $partner_id , $subp_id, $puser_id  , $puser_kuser , $kuser = null)
	{
		// make sure the user [puser_kuser + kuser] exists according to addUserOnDemand
		// TODO - remove dead code
		//$create_user_on_demand = $this->addUserOnDemand();
		$create_user_on_demand = $this->addUserOnDemand2();

		$create_user = false;
		if ( $puser_kuser == null )
		{
			if( $create_user_on_demand  ==  self::CREATE_USER_FALSE )
			{
				$create_user = false;
			}
			elseif( $create_user_on_demand  ==  self::CREATE_USER_FORCE )
			{
				$create_user = true;
			}
			elseif( $create_user_on_demand  ==  self::CREATE_USER_FROM_PARTNER_SETTINGS )
			{
				$partner = PartnerPeer::retrieveByPK( $partner_id);
				$create_user = $partner->getCreateUserOnDemand();
			}
		}

		if ( $create_user )
		{
			// prefer the user_screenName on user_name
			$user_name = $this->getP ( "user_screenName" , $this->getP ( "user_name" ) );
			if ( !$user_name )
			{
				$puser_name = $kuser_name = myPartnerUtils::getPrefix ( $partner_id ) . $puser_id;
			}
			else
			{
				$puser_name = $kuser_name = $user_name;
			}
			$puser_kuser = PuserKuserPeer::createPuserKuser ( $partner_id , $subp_id, $puser_id , $kuser_name , $puser_name, false , $kuser );
		}

		return $puser_kuser;
	}


	protected function getMsg ( $param_name  )
	{
		return $this->msg [ $param_name ] ;
	}

	protected function setMsg ( $param_list )
	{
		$this->msg = $param_list;
	}

	protected function addMsg ( $param_name , $obj )
	{
		$this->msg [ $param_name ] = $obj;
	}

	protected function setRawError ( $error_node )
	{
		$this->error = $error_node;
	}

	protected function addRawError ( $error_node )
	{
		$this->error[] = $error_node;
	}
	
	protected function addError ( $error_code )
	{
		if ( is_array ( $error_code ) )
		{
			$args = $error_code;
			$error_code = $error_code[0];
		}
		else
		{
			$args = func_get_args();
		}
		array_shift($args);
		
		$error = explode(",", $error_code, 2);
		
		$error_code = $error[0];
		$error_message = $error[1];

		$formated_desc = @call_user_func_array('sprintf', array_merge((array)$error_message, $args)); 
		
		$this->error[] = array("code" => $error_code, "desc" => $formated_desc);
	}

	// all this does is add an error and throw an APIException
	protected function addException ( $error_code )
	{
		$args = func_get_args();
//print ( __METHOD__ . " " . print_r ( $args , true ) ) . "<br>";		
		call_user_func_array( array ( &$this , 'addError' ), $args ); 
		throw new APIException("");
	}
	
	protected function  addDebug ( $param_name , $str , $sub_node = null )
	{
		if ( $sub_node != null )
		{
			if ( @$this->debug [$sub_node] == null )
			{
				$this->debug [$sub_node] = array ();
			}
			$this->debug [$sub_node][ $param_name ] = $str;
		}
		else
		{
			$this->debug [ $param_name ] = $str;
		}
	}

	protected function  benchmarkStart ( $name )
	{
		$name = preg_replace ( "/[^a-zA-Z0-9\-_]/" , "" , $name );
		$this->benchmarks_names[] = $name;
		$this->benchmarks[$name] = microtime(true);
	}

	protected function  benchmarkEnd ( $name )
	{
		$name = preg_replace ( "/[^a-zA-Z0-9\-_]/" , "" , $name );
		$this->benchmarks["_end_{$name}"] = microtime(true);
	}
	
	protected function getBenchmarks()
	{
		$bench = array ();
		foreach ( $this->benchmarks_names as $benchmark_name )
		{
			$s = @$this->benchmarks[$benchmark_name];  			// started
			$e = @$this->benchmarks["_end_$benchmark_name"];		// ended
			if ( !$e ) $bench[$benchmark_name] = "Started but never ended";
			else
			{
				$bench[$benchmark_name] = ((int)(100000*($e - $s)))/100000;
			}
		}
		
		return $bench;
	}
	
	protected static function validateSignature ( $params )
	{
		$kalsig = @$params["kalsig"];
		if ( ! $kalsig ) return false;

		$parmas_to_validate =   $params;
		return  ( $kalsig == self::signature($parmas_to_validate ) );
	}

	protected static function signature ( $params , $add_hash = true )
	{
		ksort($params);
		$str = "";
		foreach ($params as $k => $v)
		{
			if ( $k == "kalsig" ) continue;
			if ( $k == "raw_kalsig" ) continue;
			$str .= $k.$v;
		}
		if  ( $add_hash )
			return  md5($str);
		else
			return  $str;
	}

	// returns the map of parameters relevant for the signature type
	protected function getParamsFromSigType (  $sig_type )
	{
		if ( $sig_type == self::SIG_TYPE_GET ) return $_GET;
		if ( $sig_type == self::SIG_TYPE_POST ) return $_POST;
		if ( $sig_type == self::SIG_TYPE_COOKIE ) return $_COOKIE;
		if ( $sig_type == self::SIG_TYPE_REQUEST ) return $_REQUEST;

	}

	// handle the Content-Type if needed -
	// assume the container has a method called setHttpHeader
	protected function setContentType ( $hdr )
	{
		if ( $this->container_action != null )
		{
			 $this->container_action->setHttpHeader ( "Content-Type" ,  $hdr );
		}
	}


	protected function isOwnedBy ( $obj , $kuser_id )
	{
		if ( $obj instanceof entry )
		{
			return ( $obj->getKuserId() == $kuser_id );
		}
		elseif ( $obj instanceof kshow )
		{
			return ( $obj->getProducerId() == $kuser_id );
		}
		else
		{
			throw new Exception ( "Cannot handle objects of type [" . get_class ( $obj ) . "]" );
		}
	}

	protected function maxPageSize ( $limit )
	{
		return min ( $limit , 30 );
	}
	
	protected function applyPartnerFilterForClass ( $peer , $partner_id )
	{
		myPartnerUtils::addPartnerToCriteria ( $peer , $partner_id , $this->private_partner_data , $this->partnerGroup2() , $this->kalturaNetwork2()  );
	}
	
	protected function getPrivatePartnerData()
	{
		return $this->private_partner_data;
	}
}


class executionCacheKey 
{
	public $service = null;
	public $key;
	public $expiry = 60;
	
	public function toString() { return $this->service . $this->key ; }
}

?>