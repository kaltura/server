<?php
class myHealthCheckAndSecurityFilter extends sfFilter
{	
	// all parameters should have names shorter than this
	const MAX_VALID_PARAM_NAME = 60;
	// a list of paramter names to skip thhe basic security check
	private static $special_params = array( "contributorsxml" , "metadata" , "xml" ,"myspace_shareHTML" , "recipientList" , "data" , 
		"post_title" , "post_content" , "kshow_permissions" , "widget_partnerdata" );
	
	private static $special_suffix = array ( "xml" , "datacontent" , "conffile"); // dataContent - for entries of type DVD_PROJECT & BUBBLES
	
	const ERROR_URL = "/error";
	
	
	const RULE = "rule_";
	
	const TYPE_REQUEST = 0;
	
	const NAME_RULE = "name_rule";
	const DEFAULT_RULE = "default_rule";
	
	private $m_name_rule = null;
	private $m_default_rule = null;
	
	private $m_invalid_params;
	private $m_basic_security_time;
	
	private $rules = array();
	
	
	public function execute($filterChain)
	{
		// Execute this filter only once
		if ($this->isFirstCall())
		{
			if ( $this->getParameter ( "enable" ) == TRUE )
			{
				if ( $this->getParameter ( "health_check" ) == TRUE )
				{
					$this->healthCheck ();
				}
				if ( $this->getParameter ( "security" ) == TRUE )
				{
					$this->secureRequest ( );	
				}
			}
			
$request = $this->getContext()->getRequest();
$partner_id =  $request->getParameter ( "partner_id" );
if ( ! $partner_id ) $partner_id =  $request->getParameter ( "p" );
if ( $partner_id == -1 ) // can add code for specific partners whentrying to debug them
{
	// add an extra logfile for specific partners - the directory will be the same as the kaltura_prod 
	// the name will be p_<partner_id>.log
	$log_path = sfConfig::get( "sf_log_dir" );
	$logger = sfLogger::getInstance();
	$log = new sfFileLogger();
	$log->initialize(array (  			'file' => $log_path . "/p_{$partner_id}.log",));
	$logger->registerLogger($log);
}			
			
		}

		// Execute next filter
		$filterChain->execute();
		
		// use this point to flush all the modified objects in mystatisticsMgr
		myStatisticsMgr::saveAllModified();
		
		
	}

	/**
	 * test several mandatory elements in the system.
	 * if one of them doesn't work - DIE !
	 * this will prevent users to see half baked pages
	 */
	private function healthCheck()
	{
		try
		{
			$request = $this->getContext()->getRequest();
			$r = new sfWebRequest();
			
			if ( $request->isXmlHttpRequest() )
			{
				// don't tdo the heavy tests for ajax
				return;	
			}
			
			// 	memcache
			$dummy_cache = new myCache ( "healthCheck" );

			// TODO - performance 
			// DB
			$c = new Criteria();
			$c->add ( kshowPeer::ID , 1  );
			$id_list = kshowPeer::selectIdsForCriteria ( $c );
		}
		catch ( Exception $ex )
		{
			// on error - redirect to maintenance page
			$context = $this->getContext();
			return $context->getController()->redirect( self::ERROR_URL );
		}
	}

	private function secureRequest ( )
	{
		$this->basicSecureRequest();
		
//		$this->complexSecureRequest();
	}
		
	/**
	 * will remove all unwanted characters fro mthe request -
	 * it will be hard coded for now !!!
	 * TODO - the complex security will use configuration
	 */
	private function basicSecureRequest ( )
	{
		$start = microtime(true);
		
		$stop_at_first = false;
		
		$this->m_invalid_params = array ();
		$all_valid = true;
		
		$logger = sfLogger::getInstance();
		
		// fix all the request containers.
		// fixing only _REQUEST does not modify the rest! 
		$this->fixRequestContainer( $_REQUEST );
		$this->fixRequestContainer( $_POST );
		$this->fixRequestContainer( $_GET );
		$this->fixRequestContainer( $_COOKIE );
		
//		$logger = sfLogger::getInstance();
		
		$this->m_basic_security_time = ( microtime(true) - $start );
		
		if ( count ( $this->m_invalid_params ) > 0 )
		{
			$logger->warning ( "basicSecureRequest: errors\n" . print_r (  $this->m_invalid_params , true ) );
		}
				
		$logger->warning ( "basicSecureRequest: took [" . $this->m_basic_security_time . "] seconds");
		
		return $all_valid ;
	}
	
	private function fixRequestContainer ( &$arr )
	{
		foreach ( $arr as $param_name => $param_value )
		{
			$param_name = strtolower($param_name);
			if ( strlen ( $param_name ) > self::MAX_VALID_PARAM_NAME )
			{
				// remove !
				unset( $arr[$param_name]);
				$this->m_invalid_params[$param_name] = $param_value;
			}
			
			if ( in_array( $param_name , self::$special_params ) )
			{
				continue;
			}
			
			$valid = false; 
			foreach ( self::$special_suffix as $allowed_suffix )
			{
				// the match can be case insensitive
				if ( kString::endsWith( strtolower($param_name ), $allowed_suffix )) 
				{
					$valid = true;
					break;
				}
			}
			
			if ( $valid ) continue;

			$found = 0;
			// for now - replace the characters < >
			$fixed_param_value = preg_replace( "/[<>]/" , "" , $param_value , -1 , $found );
			if ( $found > 0 )
			{
				$arr[$param_name] = $fixed_param_value;
				$this->m_invalid_params[$param_name] = $param_value;
			}
		}		
	}
	/**
	 * 
	 */
	private function complexSecureRequest ( )
	{
		$start = microtime(true);
		$configCache = sfConfigCache::getInstance();
		// get config instance
  		$sf_app_config_dir_name = sfConfig::get('sf_app_config_dir_name');
		
		include($configCache->checkConfig($sf_app_config_dir_name.'/rules.yml'));
		
		// Filters don't have direct access to the request and user objects.
		// You will need to use the context object to get them
		$request = $this->getContext()->getRequest();
		$user    = $this->getContext()->getUser();

		$stop_at_first = false;
		
		$invalid_params = array ();
		$all_valid = true;
		foreach ( $_REQUEST as $param_name => $param_value )
		{
			$all_valid &= $this->validateParam ( $param_name , $param_value , self::TYPE_REQUEST , $invalid_params );
			if ( !$all_valid  && $stop_at_first )
			{
				// TODO - remove the bad parameter
				$this->handleBadParameter ( $param_name , $param_value );
				return $all_valid;
			}
		}
		
		echo ( "complexSecureRequest: " . ( microtime(true) - $start ) );
		
		return $all_valid ;
	}
	
	private function handleBadParameter ( $param_name , $param_value )
	{
		
	}
	
	private function validateParam ( $param_name , $param_value , $param_type , $invalid_params )
	{
		echo ( "validateParam ( $param_name , $param_value , $param_type )<br>" );
		// validate param_name
		$name_rule = $this->getNameRule();
		$this->validateImpl ( $name_rule , $param_name );
		
	}
	
	private function validateImpl ( mySecurityRule $rule , $value )
	{
		// check valid values

		// check length
		$len = strlen ( $value );
		if ( $rule->getMinLength() > $len || $rule->getMaxLength() < $len )
		{
			return new mySecurityError ( $rule->getErrLength() );
		}

		// check charset
		
		
		// check regexp
	}
	
	private function getNameRule ()
	{
		if ( $this->m_name_rule == NULL )
		{
			$this->m_name_rule = new mySecurityRule ( self::RULE . self::NAME_RULE );
			if ( $this->m_name_rule == NULL )
			{
				// ERROR - this is a basic feature of the validaiton 
			}
		}
		return $this->m_name_rule;
	}

	private function getDefaultRule ()
	{
		if ( $this->m_default_rule == NULL )
		{
			$this->m_default_rule = new mySecurityRule ( self::RULE . self::DEFAULT_RULE );
			if ( $this->m_default_rule == NULL )
			{
				// ERROR - this is a basic feature of the validaiton 
			}
		}
		return $this->m_default_rule;
	}
	
	private static function getRule ( $rule_name )
	{
		
	}

	
}

class mySecurityRule
{
	private $m_valid_values = null;
	private $m_min_len = null;
	private $m_max_len = null;
	private $m_charset_len = null;
	private $m_regexp = null;
	private $m_err_lanegth = null;
	private $m_err_charset = null;
	private $m_err_regexp = null; // will be used for valid_values too 
	
	private $m_rule_name = null;
	
	function mySecurityRule ( $rule_name )
	{
		$this->m_rule_name = $rule_name;
		$rule = sfConfig::get ( $rule_name );
		if ( $rule == null ) return null;
		 	
		
	}
	
	function getMinLength()
	{
		if ( $this->m_min_len == null )
		{
			$this->m_min_len  = sfConfig::get ( $this->m_rule_name . "_min_len" );
		}
		
		return $this->m_min_len;
	}

	function getMaxLength()
	{
		if ( $this->m_max_len == null )
		{
			$this->m_max_len  = sfConfig::get ( $this->m_rule_name . "_max_len" );
		}
		
		return $this->m_max_len;
	}
	
	function getErrLength ()
	{
		if ( $this->m_err_lanegth == null )
		{
			$this->m_err_lanegth  = sfConfig::get ( $this->m_rule_name . "_err_length" );
			if ( $this->m_err_lanegth )
				$this->m_err_lanegth  = sfConfig::get ( $this->m_rule_name . "_err" );
		}
		
		return $this->m_err_lanegth;		
	}
}

class mySecurityError
{
	function mySecurityError ( $error )
	{
		echo "<br><strong>$error</strong><br>";
	}
}
?>