<?php
class kConversionEngineMencoder  extends kConversionEngine
{
	const MENCODER = "mencoder";
		
	public function getName()
	{
		return self::MENCODER;
	}
	
	public static function getCmd ()
	{
		return kConf::get ( "bin_path_mencoder" );
	}
	
	protected function getExecutionCommandAndConversionString ( kConversionCommand $conv_cmd , $params_index )
	{
/*		
		$frame_rate = 25 ; // frames / second
		$audio_bitrate = "56";  //  kbit/s
		$audio_sampling_rate = 22050; // in Hz
		$audio_channels = 2; // sterio
*/
		// assume there always will be this index
		$conv_params = @$conv_cmd->conversion_params_list[$params_index];

		$size_arg = $conv_params->width == -1 ?
			" -vf harddup" : // don't append anything if we don't want to affect the size
			" -vf scale={$conv_params->width}:{$conv_params->height},harddup"; 
		
		$conversion_string = "" .
			" -of lavf " .
			self::ne ( " -ofps " , $conv_params->framerate ) .
			" -oac mp3lame " . 
			self::ne (  " -lameopts abr:br=" , $conv_params->audio_bitrate ) . 
			self::ne ( " -srate " , $conv_params->audio_sampling_rate  ) ;
		
		if ( $conv_params->audio )  // if has audio 
		{
			$conversion_string .=
				" -ovc lavc " .
				" -lavcopts vcodec=flv" . 
				self::ne ( ":vbitrate=" , $conv_params->bitrate ) . 
				":mbd=2:mv0:trell:v4mv:cbp:last_pred=3" .
				self::ne ( ":keyint=" , $conv_params->gop_size ).   
 				self::ne ( ":vqscale=" , $conv_params->qscale ) . 
				$size_arg ;
		}
		else
		{
			$conversion_string .= " -ovc frameno ";
		}

		$conversion_string .= $conv_params->mencoder_params;
		// from the new version of mencoder and onwards - no need to use this flag
//		$conversion_string .= " -lavfopts i_certify_that_my_video_stream_does_not_use_b_frames " ;

		$exec_cmd = self::getCmd() . " \"{$conv_cmd->source_file}\"" .
			$conversion_string .
			" -o " . "\"{$conv_cmd->getTargetFileWithSuffix( $params_index )}\""  . " 2>>" . "\"{$conv_cmd->getLogFileWithSuffix ( $params_index ) }\"";

		return array ( $exec_cmd , $conversion_string )	;			
	}
}
?>