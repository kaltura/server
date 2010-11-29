<?php

/**
 * Will hold helper functions and conventions for working with the HttpRequest object
 *
 */
class infraRequestUtils
{
	// the function check the http range header and sets http response headers accordingly
	// an array of the start, end and length of the requested range is returned.
	// multiple ranges are not allowed
	public static function handleRangeRequest($full_content_length, $set_content_length_header = false)
	{
		$size = $full_content_length;
        $length = $size;           // Content length
        $start  = 0;               // Start byte
        $end    = $size - 1;       // End byte
        // Now that we've gotten so far without errors we send the accept range header
        /* At the moment we only support single ranges.
         * Multiple ranges requires some more work to ensure it works correctly
         * and comply with the spesifications: http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
         *
         * Multirange support annouces itself with:
         * header('Accept-Ranges: bytes');
         *
         * Multirange content must be sent with multipart/byteranges mediatype,
         * (mediatype = mimetype)
         * as well as a boundry header to indicate the various chunks of data.
         */
        // header('Accept-Ranges: bytes');
        // multipart/byteranges
        // http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
        if (isset($_SERVER['HTTP_RANGE']))
        {
			header("Accept-Ranges: 0-$length");
      	
			$c_start = $start;
			$c_end   = $end;
			// Extract the range string
			list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
			// Make sure the client hasn't sent us a multibyte range
			if (strpos($range, ',') !== false)
			{
				// (?) Shoud this be issued here, or should the first
				// range be used? Or should the header be ignored and
				// we output the whole content?
				header('HTTP/1.1 416 Requested Range Not Satisfiable');
				header("Content-Range: bytes $start-$end/$size");
				// (?) Echo some info to the client?
				exit;
			}
			// If the range starts with an '-' we start from the beginning
			// If not, we forward the file pointer
			// And make sure to get the end byte if spesified
			if ($range{0} == '-')
			{
				// The n-number of the last bytes is requested
				$c_start = $size - substr($range, 1);
			}
			else
			{
				$range  = explode('-', $range);
				$c_start = $range[0];
				$c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
			}
			/* Check the range and make sure it's treated according to the specs.
			 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
			 */
			// End bytes can not be larger than $end.
			$c_end = ($c_end > $end) ? $end : $c_end;
			// Validate the requested range and return an error if it's not correct.
			if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size)
			{
				header('HTTP/1.1 416 Requested Range Not Satisfiable');
				header("Content-Range: bytes $start-$end/$size");
				// (?) Echo some info to the client?
		        exit;
			}
			$start  = $c_start;
			$end    = $c_end;
			$length = $end - $start + 1; // Calculate new content length
			header('HTTP/1.1 206 Partial Content');
			header("Content-Range: bytes $start-$end/$size");
		}
		// Notify the client the byte range we'll be outputting
		if ($set_content_length_header)
			header("Content-Length: $length");
        
		return array($start, $end, $length);
	}                  
	
	public static function sendCdnHeaders($ext, $content_length, $max_age = 8640000 , $mime = null, $private = false )
	{
		if ( $max_age === null ) $max_age = 8640000;
		while(FALSE !== ob_get_clean());
		
		if ( $mime == null )
		{
			if ($ext == "flv")
				$content_type ="video/x-flv";
			elseif ($ext == "mp4")
				$content_type ="video/mp4";
			elseif ($ext == "ogg")
				$content_type ="video/ogg";
			elseif ($ext == "mp3")
				$content_type ="audio/mpeg";
			elseif ($ext == "jpg")
				$content_type ="image/jpeg";
			elseif ($ext == "swf")
				$content_type ="application/x-shockwave-flash";
			else
				$content_type ="image/$ext";
		}
		else
		{
			$content_type = $mime ;
		}
		
		if ($max_age)
		{
			// added max-stale=0 to fight evil proxies
			$cache_scope = $private ? "private" : "public";
			header("Cache-Control: $cache_scope, max-age=$max_age max-stale=0");
			header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $max_age) . 'GMT'); 
			header('Last-Modified: Thu, 19 Nov 2000 08:52:00 GMT');
		}
		else
		{
			header("Cache-Control:");
			header("Expires: Thu, 19 Nov 2000 08:52:00 GMT");
		}
		
		header("Content-Length: $content_length ");
		header("Pragma:");
		header("Content-Type: $content_type");
	}
	

	public static function getRemoteAddress()
	{
		$remote_addr = null;
		if ( isset ( $_SERVER['HTTP_X_REAL_IP'] ))
		{
			$remote_addr = @$_SERVER['HTTP_X_REAL_IP'];
		}
			
		if (!$remote_addr && isset ( $_SERVER['HTTP_X_KALTURA_REMOTE_ADDR'] ) )
		{
			$remote_addr = @$_SERVER['HTTP_X_KALTURA_REMOTE_ADDR'];
		}
		
		// if still mepty .... 
		if (!$remote_addr && isset($_SERVER['REMOTE_ADDR']))
			$remote_addr = $_SERVER['REMOTE_ADDR'];
		
		return $remote_addr;
	}
}
?>