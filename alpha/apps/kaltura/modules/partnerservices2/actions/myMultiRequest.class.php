<?php
require_once ( "defPartnerservices2Action.class.php");

// this clas does not really  extend defPartnerservices2Action. Its only for now - to use the debug data
class myMultiRequest extends defPartnerservices2Action
{
	public function describe()
	{
		return array();		
	}
	
	const REQUEST_TYPE_JSON = 1;
	const REQUEST_TYPE_XML = 2;
	const REQUEST_TYPE_PHP = 3;
	const REQUEST_TYPE_HTTP_REQUEST = 4;

	const MULTI_END = "end";

	const STOP_ON_FIRST_ERROR = 1;

	private $original_request_params;
	private $global_request_params; // params with the global_ prefix - will be used for every request unless overriden using the rexuesX_

	private $errors;

	private $input_type = null;
	private $stop_on_error = null;

	private $request_request_params;

	private $current_action_index = 0; // this will become 1 with the first call to getNextRequestParams


	protected function ticketType () {				return self::REQUIED_TICKET_NONE;	}
	public function needKuserFromPuser ( )	{		return self::KUSER_DATA_NO_KUSER;	}
	protected function addUserOnDemand ( )	{		return self::CREATE_USER_FALSE;		}

	// a list of params that cause some problems when calling from testme 
	private static $nast_params = array ( "testme" , "service" );
	/**
	 * recieves the initilal param list and cracks it down to the mulriple ones based on naming convenstionsor the input type
	 *
	 *
	 * @param array $original_request_params
	 * @return myMultipleRequest
	 */
	public function myMultiRequest ( $original_request_params ,
									$container_action = null ,
									$input_type = self::REQUEST_TYPE_HTTP_REQUEST ,
									$stop_on_error = self::STOP_ON_FIRST_ERROR )
	{
		parent::defPartnerservices2Action ( $container_action );
//		print_r ($original_request_params);

		$this->original_request_params = $original_request_params;
		ksort ( $this->original_request_params );

//		print_r ($this->original_request_params);

		$this->input_type = $input_type;
		$this->stop_on_error = $stop_on_error;
		$this->errors = array();
		list ( $this->multi_request , $this->global_request_params ) = $this->buildParamsFromHttp();
	}

/*	public function execute ( )
	{
		return $this->executeMultiRequest();
	}
	*/
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		return $this->executeMultiRequest();
	}


	public function executeMultiRequest ()
	{
		// several default params
		$global_format = @$this->original_request_params["format"];
		if (!$global_format)
			$global_format = kalturaWebserviceRenderer::RESPONSE_TYPE_XML;
		$this->response_type = $global_format; //
		$multi_response = array();

		while ( ( $params = $this->getNextRequestParams( $multi_response ) ) != null )
		{
			// start by resetting the peers so there will be no effect from the previous "request"
			myPartnerUtils::resetAllFilters();
//			echo ( "Now executing request [{$this->current_action_index}}]<br>" );

			$service_name = @$params["service"];
			if ( !$service_name )
			{
				// error - cannot find service to execute !
				// LOG & STOP !
				break ;
			}

			$service_name = strtolower( str_replace ( "_" , "" , $service_name ) );
			if( $service_name == self::MULTI_END )
				break;

			$clazz_name = $service_name . "Action";
$include_result = include_once ( "{$clazz_name}.class.php");
			if ( $include_result )
			{
				$myaction = new $clazz_name(  );

				// request the response as
				// becuase the format is global - it cannot be used per service anyway.
				$params["format"] = kalturaWebserviceRenderer::RESPONSE_TYPE_PHP_ARRAY;
				$myaction->setInputParams ( $params );
				//$myaction->setResponseContext ( "response{$this->current_action_index}");
				$response = $myaction->execute( false ); // foreach single action - pass false so no extra debug data will be outputed

	//			$this->addMsg ( "response{$this->current_action_index}" , $response );
				$multi_response["response{$this->current_action_index}"] = $response;

				$error = @$response["error"];
			}
			else
			{
				$error = "Invalid service [$service_name]";
			}

			if ( $error != null ) //&& ( is_array ( $error ) && count ( $error)>0 ) )
			{
				if ( $this->stop_on_error == self::STOP_ON_FIRST_ERROR )
				{
					// make the error public as well as in the specific response
//					$this->addError( $error );
//					$this->addError( "errornos_request" , $this->current_action_index );

					//$this->setRawError( $error );
					$this->addRawError( $error );
					$this->addRawError( array ( "errornos_request" => $this->current_action_index ) );
					
//					$multi_response["error"] = $error;
//					$multi_response["errornos_request"] = $this->current_action_index;

					// don't continue iterating
					break;
				}
			}
		}

		if ( count ( $this->errors ) > 0 )
		{
			if ( ! isset ( $multi_response["debug"] ) ) $multi_response["debug"] = array();
//			$this->addDebug ( "error_replacing" , $this->errors );
			$multi_response["debug"]= $this->errors;

		}

		$this->setMsg ( $multi_response );

		return $multi_response;

	}

	/**
	 * Will return a list of the name-value params of the coming request.
	 * The param list will be populated with runtime parameters - globals and replaced place-holders
	 *
	 */
	public function getNextRequestParams ( $multi_response )
	{
		$this->current_action_index++;
		//echo "Returning [{$this->current_action_index}]<br>";
		$res = $this->global_request_params;
		$request_params = @$this->multi_request[$this->current_action_index];
		if ( is_array ( $request_params ) )
			$res = array_merge ( $res ,  $request_params);
/*		else
			echo "in request [{$this->current_action_index}]:" . print_r ( $request_params , true );
	*/

		// use the $multi_response values to replace place-holders for the next iteration
		foreach ( $res as $name => &$value )
		{
			$original_value = $value;
			// we need the PREG_PATTERN_ORDER
			$m = preg_match_all ( "/\{([^\}].*?)\}/" , $value , $matchs , PREG_SET_ORDER );
			if ( $m )
			{
//				echo "Replacing [$value]<br>" . print_r ( $matchs , true );
				foreach ( $matchs as $place_holder )
				{
					// $place_holder[0] - the original string including {}
					// $place_holder[1] - the path of the place-holder
					$place_holder_path = $place_holder[1]; // without the curly brackets
					list ( $current_value , $err )= self::getValue ( $multi_response , $place_holder_path ) ;
					if ( $err )
						$this->errors[] = "Cannot find field [{$err}] in path [{$place_holder_path}] in string [$original_value] for variable [$name]";

					$value = str_replace ( $place_holder[0] , $current_value , $value );
				}
			}
		}

		return $res;
	}



	protected function buildParamsFromRequestByType ( )
	{
		if ( $this->input_type == self::REQUEST_TYPE_HTTP_REQUEST )
		{
			return $this->buildParamsFromHttp();
		}
	}

	protected function buildParamsFromHttp ( )
	{
		// assume the original_request_params is the request (or an array with a given prefix
		$input_param_list = $this->original_request_params;
		$output_param_list = array();
		$global_request_params = array();
		$implicit_request_params = array();

		$min_request_id = 200;
		$max_request_id = 0;

		foreach ( $input_param_list as $param => $value )
		{

			//if ( kString::beginsWith( $param , "req_") )
			$res = preg_match ( "/request(.+?)_(.*)/", $param , $match ) ;
//			echo "$param: " . print_r ( $match , true ) , " [$res]<br>";

			if ( $res )
			{
				$request_id = $match[1];
				if ( ! is_numeric( $request_id ))
				{
					echo "MultiRequest requires a number as a request id.  not [{$request_id}]";
					continue;
				}

				if ( $max_request_id < $request_id ) $max_request_id = $request_id;
				if ( $min_request_id > $request_id ) $min_request_id = $request_id;

				$param_name = $match[2];

	//			echo "setting [$request_id][$param_name] = [$value]<br>";

				if ( !isset ( $output_param_list[$request_id] ) )
				{
					$output_param_list[$request_id]=array();
				}

				$output_param_list[$request_id][$param_name] = $value;
			}
			else
			{
				$res = preg_match ( "/global_(.*)/", $param , $match ) ;
				if ( $res )
				{
					$param_name = $match[1];
//					echo "GLOBALS [$param_name]=[$value]<br>";
					$global_request_params[$param_name]= $value;
				}
				else
				{
					// params not starting with requestX_ or global_
					// exclude a list of nasty params 
					if ( in_array ( $param , self::$nast_params ) ) continue;
					$implicit_request_params[$param] = $value;
				}
			}

			// make sure the request array has no gaps.
		}

		// merge the global params (explicit ones) with the implicit ones (no known prefix).
		// give president to the global ones
		$global_request_params = array_merge( $implicit_request_params , $global_request_params );

		//print_r ( $output_param_list );
		return array ( $output_param_list , $global_request_params );
	}

	// assuming the place_holder is a path to the field
	private static function getValue ( $multi_response , $place_holder )
	{
		$fields = explode ( "." , $place_holder );
		$arr = $multi_response;
		$count = 0;
		$err = "";
		foreach ( $fields as $field )
		{
			$current = @$arr[$field];
			if ( is_array ( $current ) )
			{
				// will be used as the next array to further iterate
				$arr = $current;
			}

			if ( !$current )
			{
				 $err = $field;
			}
		}

//		echo "getValue [$current]<br>";
		return array ( $current , $err );
	}
}
?>