<?php
/**
 * @package Core
 * @subpackage Conversion
 * @deprecated
 */
class kConversionEngineFlix  extends kConversionEngine
{
	const FLIX = "cli_encode";
	
	public function getName()
	{
		return self::FLIX;
	}

	public static function getCmd ()
	{
		return kConf::get ( "bin_path_flix" );
	}
	
	protected function getExecutionCommandAndConversionString ( kConversionCommand $conv_cmd , $params_index )
	{
/*		
		$frame_rate = 25 ; // frames / second
		$audio_bitrate = "56k";  //  kbit/s
		$audio_sampling_rate = 22050; // in Hz
		$audio_channels = 2; // sterio
*/
		// assume there always will be this index
		$conv_params = @$conv_cmd->conversion_params_list[$params_index];
 /*
  * for now - irrelevant in case of commercial encoder
		if ( ! $conv_params->video )				$video_audio_str = " -vn "; 			// video none
		elseif  ( ! $conv_params->audio )		$video_audio_str = " -an ";		// audio node
		else $video_audio_str = " ";
*/
		
/*		
Usage: cli_encode [OPTION...]
  -i, --in=input file                      infile
  -o, --out=output file                    outfile
  -w, --width=output width                 width
  -h, --height=output height               height
  -d, --deinterlace=deinterlace method     deinterlace
  -k, --kffreq=keyframe frequency          kffreq
  -b, --bitrate=bitrate                    bitrate
*/	
  
		$size_arg = $conv_params->width == -1 ?
			"" : // don't append anything if we don't want to affect the size
			" -w " . $conv_params->width .	" -h " . $conv_params->height ;
		
		$conversion_string = 		
			self::ne ( " -r " , $conv_params->framerate ) .
			self::ne ( " -b " , $conv_params->bitrate ) .  // make sure the integer is followed by the letter 'k'
			self::ne ( " -k " , $conv_params->gop_size ) .
			self::ne ( " -a " , $conv_params->audio_bitrate ) .
			$size_arg .
			" " . $conv_params->flix_params . " "   // extra params for flix if exist 
			;
						
		// I have commented out the audio parameters so we don't decrease the quality - it stays as-is
		$exec_cmd = self::getCmd() . " -i " . $conv_cmd->source_file . " -o " . $conv_cmd->getTargetFileWithSuffix( $params_index ) .
			$conversion_string .
			"  2>&1 >>"  . "\"{$conv_cmd->getLogFileWithSuffix ( $params_index ) }\"";

		return array ( $exec_cmd , $conversion_string )	;			
	}
}
?>