<?php
class debugUtils
{
	public static function DEBUG (  $debug_str , $str = "" )
	{
		//echo ( isset ( $debug_str ) ? $debug_str : ""  . " " . isset ( $str ) ? $str : "" );
		if ( isset ( $debug_str ))
		{
			$debug_str .= " " . $str;
		}
	}
	
	public static  function log ( $str )
	{
		KalturaLog::log($str);
	}
	
	// stack trace
	public static  function st ( $should_return_as_string = false , $line_break = "\n")
	{
		$list = debug_backtrace();
		
		$str  = "";
		$i = 0 ;
		foreach ( $list as $func )
		{//config_core_compile.yml.php
			$file = @$func["file"];
			++$i;
/*			
			if ( strpos( $file , "config_core_compile" ) > 0 ) 
			{
				$str .= "skipping $file\n";
				continue;
			}
	*/		
//			$str .= "[$i] " . print_r ( $func , true );
			
			$str .= "$i$line_break";
			$str .= "file: " . @$func["file"] . "$line_break";
			$str .= " line: " . @$func["line"] . "$line_break";
			$str .= " function: " . @$func["function"] . "$line_break";
			$str .= " class: " . @$func["class"] . "$line_break";
			$str .= " args: " . count ( @$func["args"] ) . "$line_break";

/*			
			           [file] => F:\web\kaltura\alpha\apps\kaltura\lib\kalturaAction.class.php
            [line] => 76
            [function] => debug
            [class] => kalturaAction
*/
		}
		if ( $should_return_as_string)
		{
			return $str;
		}
		else
		{
			self::log ( $str , true  );
		}
	}
	
}
?>