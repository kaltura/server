<?php
/**
 * Will handle a kConversionCommand and make sure the best kConversionEngine does the conversion successfully.
 * The result will be a kConversionResult object that reflect the whole conversion process for the multiple results from the command
 * depending on the number of the kConversionParam objects.
 * 
 * @package Core
 * @subpackage Conversion
 * @deprecated 
 */
class kConversionEngineMgr 
{
	public static function simulate ( kConversionCommand $conv_cmd , $commercial = false )
	{
		$simulation_results = array ();
		for ( $i=1; $i<=3 ; $i++ )
		{
			$converter = kConversionEngine::getInstance( $i );
			$simulation_results[$i] =$converter->simulate ($conv_cmd );
		}  
		return $simulation_results;
	}
	
	/**
	 * Will create the best kConversionEngine it can to handle the cmd
	 *
	 * @param kConversionCommand $conv_cmd
	 * @return kConversionResult
	 */
	public static function convert ( kConversionCommand $conv_cmd , $commercial )
	{
		$conv_result = new kConversionResult( $conv_cmd );
		if ( $commercial )
		{
			$cli_encode_count = exec("ps aux | grep -c [c]li_encode | grep -v \"sh \"");

			if ($cli_encode_count >= 4 && !$conv_cmd->forceOn2)
			{
				// try encoding.com
				$converter = kConversionEngine::getInstance( kConversionEngine::ENGINE_TYPE_ENCODINGCOM );
			}
			elseif ($cli_encode_count >= 2 && $cli_encode_count < 4 && !$conv_cmd->forceOn2)
			{
				// check if the length of the clip is more than 30 minutes and use encoding.com
				$media_info = shell_exec("mediainfo ".realpath($conv_cmd->source_file));
				preg_match_all("/Duration\s*: (([0-9]*)h ?)?(([0-9]*)mn ?)?(([0-9]*)s ?)?(([0-9]*)ms ?)?/", $media_info, $duration_output);

				$hour 	= @$duration_output[2][0];
				$min 	= @$duration_output[4][0];
				//$sec 	= @$duration_output[6][0];
				//$msec = @$duration_output[8][0];
				
				$min  += ($hour * 60);
				
				if ($min >= 30)
				{
					// try encoding.com
					$converter = kConversionEngine::getInstance( kConversionEngine::ENGINE_TYPE_ENCODINGCOM );
				}
				else
				{
					// try flix
					$converter = kConversionEngine::getInstance( kConversionEngine::ENGINE_TYPE_FLIX );					
				}
			}
			else
			{
				TRACE("using ON2 because: cli_encode_count [$cli_encode_count] and forceOn2 [".$conv_cmd->forceOn2."]");
				// try flix  
				$converter = kConversionEngine::getInstance( kConversionEngine::ENGINE_TYPE_FLIX );
			}
			
			list ( $ok , $first_failed_index ) = $converter->convert ( $conv_cmd , $conv_result  );
			
			
			if ( !$ok )
			{
				self::failed ( $converter , $conv_cmd,  $first_failed_index );
				// try ffmpeg 
				$converter = kConversionEngine::getInstance( kConversionEngine::ENGINE_TYPE_FFMPEG );
				list ( $ok , $first_failed_index ) = $converter->convert ( $conv_cmd , $conv_result , $first_failed_index );
				if ( ! $ok )
				{
					self::failed ( $converter , $conv_cmd, $first_failed_index );
					// try mencoder
					$converter = kConversionEngine::getInstance( kConversionEngine::ENGINE_TYPE_MENCODER );
					list ( $ok , $first_failed_index ) = $converter->convert ( $conv_cmd , $conv_result , $first_failed_index ); 
				}
			}			 
		}
		else
		{
			// try ffmpeg - if failed, try mencoder
			$converter = kConversionEngine::getInstance( kConversionEngine::ENGINE_TYPE_FFMPEG ); 
			list ( $ok , $first_failed_index ) = $converter->convert ( $conv_cmd , $conv_result );
			if ( !$ok )
			{
				self::failed ( $converter , $conv_cmd, $first_failed_index );
				// try mencoder 
				$converter = kConversionEngine::getInstance( kConversionEngine::ENGINE_TYPE_MENCODER );
				list ( $ok , $first_failed_index ) = $converter->convert ( $conv_cmd , $conv_result , $first_failed_index );
				if ( ! $ok )
				{
					self::failed ( $converter , $conv_cmd, $first_failed_index );
					// try flix ?? 
					$converter = kConversionEngine::getInstance( kConversionEngine::ENGINE_TYPE_FLIX );
					list ( $ok , $first_failed_index ) = $converter->convert ( $conv_cmd , $conv_result , $first_failed_index ); 
				}
			}
		}

		// set the status_ok of the result
		$conv_result->status_ok = $ok;
		
		return $conv_result;
	}
	
	private static function failed ( kConversionEngine $converter , kConversionCommand $conv_cmd, $first_failed_index )
	{
		TRACE ( "Error: Engine [" . $converter->getName() . "] failed to convert [$first_failed_index]" );
	}
}
?>