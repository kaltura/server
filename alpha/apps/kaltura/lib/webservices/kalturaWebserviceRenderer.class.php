<?php
/**
 * This class will render the results of the webservices accroding to the required response type.
 *
 */
class kalturaWebserviceRenderer
{
	const RESPONSE_TYPE_JSON = 1;
	const RESPONSE_TYPE_XML = 2;
	const RESPONSE_TYPE_PHP = 3;
	const RESPONSE_TYPE_PHP_ARRAY = 4;
	const RESPONSE_TYPE_PHP_OBJECT = 5;
	const RESPONSE_TYPE_RAW = 6;
	const RESPONSE_TYPE_HTML = 7;
	const RESPONSE_TYPE_MRSS = 8;
	
	protected $escape_text = false;

	protected $response_type = self::RESPONSE_TYPE_XML;
	protected $container_action = null;

	protected $cachekey = null;
	
	private $my_cache;
	private $cached_content = null;
	public function kalturaWebserviceRenderer ( $container_action )
	{
		$this->container_action = $container_action;
	}

	public function setCacheKey ( $cachekey )
	{
		$this->cachekey = $cachekey;
	}
	
	public function deleteCacheKey ( $cachekey , $response_type )
	{
		if( $cachekey )	
		{
			$cache = new myCache( "kwr" , 0 );
			$cache->remove( $response_type . "_" . $cachekey->toString() ); // the cache is per $response_type
		}
	}
	
	public function hasContentForCacheKey ( $response_type )
	{
		if( $this->cachekey )	
		{
			if ( ! $this->my_cache )
			{
				$this->my_cache = new myCache( "kwr" , $this->cachekey->expiry );
			}
			
			$this->cached_content =  $this->my_cache->get ( $response_type . "_" . $this->cachekey->toString() ); // the cache is per $response_type
			
			return ( $this->cached_content != null );
		}
		return false;
	}
	/**
	 * Will return an array of 2:
	 * 1. an object (usually string) representing the response
	 * 2. a string representing the recommended content type header
	 */
	public function renderDataInRequestedFormat( $response_params , $response_type , $escape_text = false )
	{
		if ( $this->cached_content ) return $this->cached_content;
		
		$this->escape_text = $escape_text;
		$this->response_type = $response_type;

		if( $this->response_type == self::RESPONSE_TYPE_XML)
		{
			$content_type = "text/xml; charset=utf-8";
			$response =  '<?xml version="1.0" encoding="ISO-8859-1"?><xml>'.$this->array2xml( $response_params ).'</xml>';
		}
		elseif ( $this->response_type == self::RESPONSE_TYPE_PHP)
		{
			$content_type = "text/html; charset=utf-8";
			$response =  $this->array2phpserialize ( $response_params );
		}
		elseif ( $this->response_type == self::RESPONSE_TYPE_PHP_ARRAY || $this->response_type == self::RESPONSE_TYPE_PHP_OBJECT )
		{
			$content_type = "text/html; charset=utf-8";
			$response =  objectWrapperBase::toArrayImpl ( $response_params  );
		}
		elseif ( $this->response_type == self::RESPONSE_TYPE_RAW )
		{
			$content_type = "text/html; charset=utf-8";
			$response =  $response_params;
		}
		elseif ( $this->response_type == self::RESPONSE_TYPE_HTML )
		{
			$content_type = "text/html; charset=utf-8";
			$response =  $response_params;
		}
		elseif( $this->response_type == self::RESPONSE_TYPE_MRSS )
		{
			$content_type = "text/xml; charset=utf-8";
			//$response =  kalturaRssRenderer::renderMrssFeed( objectWrapperBase::toArrayImpl ( $response_params  ) );
			$mrss_renderer = new kalturaRssRenderer ( kalturaRssRenderer::TYPE_YAHOO ); 
			$response =  $mrss_renderer->renderMrssFeed( $response_params  );
		}
		else
		{
			$content_type = "text/html; charset=utf-8";
			$response =  self::json_serialize( $response_params );
		}

		$res =  array ( $response , $content_type );
		
		// we reached here and there is a $this->cachekey - store in the cahce for the next time
		if ( $this->cachekey )
		{
			if ( ! $this->my_cache )
			{
				$this->my_cache = new myCache( "kwr" , $this->cachekey->expiry );
			}
			
			$this->cached_content =  $this->my_cache->put ( $response_type . "_" . $this->cachekey->toString() , $res ); // the cache is per $response_type
		}
		return $res;
	}


	protected function json_serialize( $array )
	{
		if ( $array instanceof objectWrapperBase )
	    {
	    	return json_encode(  $array->toArray() );
	    }
	    else
	    {
	    	return json_encode( objectWrapperBase::toArrayImpl ( $array  ) );
	    }
	}


	protected function array2phpserialize( $array )
	{
		if ( $array instanceof objectWrapperBase )
	    {
	    	return serialize(  $array->toArray() );
	    }
	    else
	    {
	    	return serialize( objectWrapperBase::toArrayImpl ( $array  ) );
	    }
	}


	protected function array2xml($array, $num_prefix = "num_")
	{
		return self::array2xmlImpl ( $array , $num_prefix );
	}

	protected function array2xmlImpl($array, $num_prefix = "num_" , $depth = 0)
	{
		$depth++;
//		echo ( "[$depth]array2xmlImpl:" . print_r ( $array , true ) . "<br>");
//		echo ( "[$depth]--array2xmlImpl: $num_prefix <br>");

		$result = "";

		if ( $array instanceof myBaseObject )
	    {
/*
 * 			$obj =  objectWrapperBase::toArrayImpl ( $array  );
		    $result.= self::array2xmlImpl( $array, $num_prefix , $depth);
 */
			$result = null;
			$fields = $array->getFieldNames();
	    	foreach( $fields as $key ) // subnode
	        {
	        	if ( empty ( $key ) ) continue;

	        	$val = $array->get ( $key );
	        	// TODO - think if want to compare to === null -
				// this will return 0 and empty strings (doesn't happen now)
	        	//if ( empty ( $val ) && ( $val !== 0 ) ) continue;
				if ( !self::shouldDisplayValue ( $val ) )  continue;

				try
				{
	            	$key =  self::tagNameFromField ( $key , $num_prefix ); // fix key if needed
	            	$result.="<".$key.">".self::array2xmlImpl( $val, $num_prefix , $depth )."</".$key.">";
				}
				catch ( Exception $ex )
				{
					$result.="<".$key.">ERROR</".$key.">";
				}
	        }
	    }
		else if ( $array instanceof objectWrapperBase )
	    {
/*
	    	$obj =  $array->toArray();
		    $result.= self::array2xmlImpl( $array, $num_prefix , $depth);
	*/
			$result = "";
			$fields = $array->getFieldNames();

			$i=0;
	    	foreach( $fields as $key ) // subnode
	        {
	        	if ( empty ( $key ) ) continue;
	        	$val = $array->get ( $key );
				if ( !self::shouldDisplayValue ( $val ) )  continue;

				try
				{
		            $key =  self::tagNameFromField ( $key , $num_prefix ); // fix key if needed
	                $result.="<".$key.">".self::array2xmlImpl( $val, $num_prefix , $depth)."</".$key.">";
				}
				catch ( Exception $ex )
				{
					$result.="<".$key.">ERROR</".$key.">";
				}
	        }
	    }
/*
		if ( $array instanceof myBaseObject )
		{
			$obj =  objectWrapperBase::toArrayImpl ( $array  );
		    $result.= self::array2xmlImpl( $array, $num_prefix , $depth);
		}
		elseif ( $array instanceof objectWrapperBase )
		{
			$obj =  $array->toArray();
		    $result.= self::array2xmlImpl( $array, $num_prefix , $depth);
		}*/
	    elseif( is_array ( $array )  ) // text
	    {
			$result = "";

			if ( kArray::array_is_associative ( $array) )
			{
		    	foreach($array as $key => $val ) // subnode
		        {
		        	try
					{
			            $key =  self::tagNameFromField ( $key , $num_prefix ); // fix key if needed
		                $result.="<".$key.">".self::array2xmlImpl( $val, $num_prefix , $depth)."</".$key.">";
					}
					catch ( Exception $ex )
					{
						$result.="<".$key.">ERROR</".$key.">";
					}
		        }
			}
			else
			{
				$array_size = count ( $array );
				for ( $i = 0 ; $i < $array_size ; ++$i )
		        {
					if ( key_exists( $i , $array ) )
					{
						$val = $array[$i];
					}
					else
					{
					}

		        	try
					{
			            $key =  self::tagNameFromField ( $i , $num_prefix ); // fix key if needed

		                $result.="<".$key.">".self::array2xmlImpl( $val, $num_prefix , $depth )."</".$key.">";
					}
					catch ( Exception $ex )
					{
						$result.="<".$key.">ERROR</".$key.">";
					}
		        }
			}
	    }
	    elseif ( is_object ( $array ) )
	    {
	    	return "ERROR fromating object of type [" . get_class ( $array ) . "]" ;
//echo ( "[$depth]array2xmlImpl:is_object " . get_class ( $array ) . "<br>" );
	    }
	    else  // text
	    {
//cho ( "[$depth]array2xmlImpl: " . get_class ( $array ) . "<br>");
	    	//return htmlentities ( $array );
			if ( $this->escape_text )
			{
				// TODO - decide whether to encode or cdata or nothing - according to the name of the field
				$escaped = kString::xmlEncode ( $array ) ;
				return $escaped;
				/*
				if ( $escaped != $array )
				{
					return "<![CDATA[$array]]>";
				}
				*/
			}
			return $array;
	    }

	    return $result;
	}


	private static function shouldDisplayValue ( $val )
	{
		return ( ! empty ( $val ) || $val === 0 );
		// TODO - maybe prefer this - it will return empty strings too
//		return ( $val !== null);
	}
	
	private static function tagNameFromField ( $key , $num_prefix )
	{
//		echo "tagNameFromField ( $key , $num_prefix )<br>";

		if ( is_numeric($key) )
		{
			return $num_prefix . $key;
		}
		elseif ( kString::beginsWith( $key , "__"  ) )
		{
			$pat = "/^__[^_]*_(.*)$/" ;//
			preg_match( $pat , $key , $suffix );

			return $suffix[1];
		}

		return $key;

	}
}



?>