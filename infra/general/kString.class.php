<?php
/**
 * kString -
 * a bundle of helpful functions for manipulating strings
 */

// TODO - think if kClass is a good convention for helper classes ?

class kString
{
	/**
	 * return true if $str starts with $desired_prefix, false otherwise
	 *
	 *
	 * @param string $str
	 * @param string $desired_prefix
	 */
	public static function beginsWith ( $str , $desired_prefix )
	{
		if ( $str == NULL || $desired_prefix == NULL )
		{
			return false;
		}

		// if the $desired_prefix is an array - return true if at least one prefix in the array of prefixes that matchs
		if ( is_array ( $desired_prefix ) )
		{
			foreach ( $desired_prefix as $pref )
			{
				if ( self::beginsWith( $str , $pref) )
				{
					return true;
				}
			}
			
			return false;
		}
		// TODO- is strpos faster ?
		return ( substr( $str, 0, strlen ( $desired_prefix ) ) === $desired_prefix );
	}

	public static function endsWith( $str, $desired_suffix )
	{
		return ( substr( $str, strlen( $str ) - strlen( $desired_suffix ) ) === $desired_suffix );
	}


	static function camelBack2Lowercase( $str , $upper_case_replace_char )
	{
		$res = "";
		for ( $i = 0 ; $i < strlen( $str ) ; ++$i )
		{
			$ch = $str[$i];
			if ( ctype_upper ( $ch ) )
			{
				$res .= $upper_case_replace_char . strtolower( $ch );
			}
			else
			{
				$res .= $ch;
			}
		}
		return $res;
	}

	public static function camelBack2Hyphened ( $str )
	{
		return self::camelBack2Lowercase ( $str , "-" );
	}

	public static function camelBack2Underscore ( $str )
	{
		return self::camelBack2Lowercase ( $str , "_" );
	}

	// replace only the first occurance of $search in $content with the new value $replace
	public static function replaceOnce($search, $replace, $content){

		$pos = strpos($content, $search);
		if ($pos === false) { return $content; }
		else { return substr($content, 0, $pos) . $replace . substr($content, $pos+strlen($search)); }

	}

	public static function isEmpty ( $str )
	{
		return ( $str == NULL || strlen ( $str ) == 0 );
	}


	/**
	 * return the first part of $str - less or equal to $max_number_of_chars.
	 * If this part splits a word (string between 2 white spaces) - the whole word will not appear in the result.
	 * After getting this prefix that does not hold any split words, if it's shorter than the original word, the
	 * $suffix_to_add is added at the end
	 */
	public static function getWords ( $str , $max_number_of_chars , $suffix_to_add = "" )
	{
		if ( $str == NULL || $str == "" )
			return "";
			
		if ( $max_number_of_chars >= strlen ( $str ) )
		{
			// no need to trim the original string
			return $str;
		}

		$str_prefix = substr ( $str , 0 , $max_number_of_chars );

		if ( false )
		{
			// if the prefix splits a word into 2 - drop that word
			if ( self::isWhitespace ( $str[$max_number_of_chars] ) || self::isWhitespace ( $str[$max_number_of_chars+1] ) )
			{
				return $str_prefix . $suffix_to_add;
			}
		}
		return $str_prefix . $suffix_to_add;
	}

	/**
	 * formats a given date according to the following rules
	 *	(If today) hh:mm AM/PM (x hours ago)
	 *	Yesterday
	 *	x days ago
	 *	Over a month ago
	 *	Over 3 months ago
	 *	Over 6 months ago
	 * 
	 */
	public static function formatDate( $date )
	{
		$diff = time() - $date;
		$days = floor($diff / 86400);
		if ($days < 1) {
			$s = strftime('%I:%M %p', $date);
			if ($days == 0)
			{
				$hours = floor($diff / 3600);
				if ( $hours == 0 ) 
				{
					if ( floor($diff / 60) < 2 )
						$s = "about a minute ago";
					else
						$s = floor($diff / 60)." minutes ago";
				}
				else 
					$s = $hours." hour".( $hours > 1 ? 's':'')." ago";
			}
		}
		else if ($days == 1)
			$s = "Yesterday";
		else if ($days < 30)
			$s = "$days days ago";
		else if ($days < 90)
			$s = "Over a month ago";
		else if ($days < 180)
			$s = "Over 3 months ago";
		else
			$s = "Over 6 months ago";
			
		return $s;
	}
	
	public static function renderNumber ( $num , $digits_after_decimal_point = 1)
	{
		return number_format( $num , $digits_after_decimal_point );	
	}
	
	
	public static function hash ( $str , $salt )
	{
		return sha1 ( $salt . $str );	
	}
	
	public static function verifyHash ( $str , $salt , $hashed_value )
	{
		return ( $hashed_value == self::hash ( $str , $salt ) );
	}
	
	public static function signString($strToSign, $signature)
	{
		$signedStr = self::hash($strToSign, $signature). "|" . $strToSign;
		return base64_encode($signedStr);
	}
	
	public static function crackString($signedStr, $signature)
	{
		$signedStr = base64_decode($signedStr, true);
		
		if (!is_string($signedStr))
			return null;
			
		if (strpos($signedStr, "|") === false)
			return null;
			
		list($hash, $realStr) = explode("|", $signedStr, 2);
		
		if ($hash === self::hash($realStr, $signature))
			return $realStr;
		else
			return null;
	}

	public static function expiryHash ( $str , $salt , $expiry_interval_in_seconds , $time = null , $offset = 0 )
	{
		if ( $time == null )	$time = time();
		$rounded_time = (int)($time/$expiry_interval_in_seconds) + $offset ;
		return sha1 ( $salt . $rounded_time . $str );	
	}
	
	/**
	 * returns 1 if matchs current time interval, 2 if matchs prev time interval and 0 if no match == expired or wrong 
	 */
	public static function verifyExpiryHash ( $str , $salt , $hashed_value , $expiry_interval_in_seconds , $time = null)
	{
		$current_time_result = ( $hashed_value == self::expiryHash ( $str , $salt ,$expiry_interval_in_seconds , $time  ) );
		if ( $current_time_result ) return 1;
		// now try with an offset of -1 - if this is OK, the string is still valid because it belonged to the previous interval
		$prev_time_result = ( $hashed_value == self::expiryHash ( $str , $salt , $expiry_interval_in_seconds , $time , -1 ) );
		if ( $prev_time_result ) return 2;
		
		return 0;
	}
	
	public static function generateSalt ( $str_tip )
	{
		$salt = md5(rand(100000, 999999).$str_tip); 
		return $salt;
	}
	
	public static function stripText($text)
	  {
	    $text = strtolower($text);
	
	    $text = preg_replace('/\&/', 'and', $text);
	
	    // strip all non word chars
	    $text = preg_replace('/\W/', ' ', $text);
	
	    // replace all white space sections with a dash
	    $text = preg_replace('/\ +/', '-', $text);
	
	    // trim dashes
	    $text = preg_replace('/\-$/', '', $text);
	    $text = preg_replace('/^\-/', '', $text);
	
	    return $text;
	  }
	   
	  public static function add_http($site)
	  {
	  	if(!empty($site))
	  	{
	  		$hp = str_replace("http://","",$site);
	  		return 'http://'.$hp;
	  	}
	  }

	public static function xmlEncode($str)
	{
		return str_replace ( array ( "&", '"' , '<', '>', "'" ) , array ( "&amp;", "&quot;" , "&lt;", "&gt;", "&apos;" ), $str );
	}
	
	public static function generateRandomString($minlength, $maxlength, $useupper, $usespecial, $usenumbers)
	{
		// copied from myPartnerRegistration.class.php
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
	
	public static function generateStringId()
	{
		return substr(base_convert(md5(uniqid(rand(), true)), 16, 36), 1, 8);
	}
	
	
	public static function isEmailString($string)
	{
		return preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i', $string);
	}
}
