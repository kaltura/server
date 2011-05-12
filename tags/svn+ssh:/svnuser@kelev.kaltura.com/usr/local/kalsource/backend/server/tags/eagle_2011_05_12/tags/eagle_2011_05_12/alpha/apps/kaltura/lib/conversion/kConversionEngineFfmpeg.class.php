<?php
/**
 * @package Core
 * @subpackage Conversion
 * @deprecated
 */
class kConversionEngineFfmpeg  extends kConversionEngine
{
	const FFMPEG = "ffmpeg";
	
	public function getName()
	{
		return self::FFMPEG;
	}
	
	public static function getCmd ()
	{
		return kConf::get ( "bin_path_ffmpeg" );
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
 
		if ( ! $conv_params->video )				$video_audio_str = " -vn "; 			// video none
		elseif  ( ! $conv_params->audio )		$video_audio_str = " -an ";		// audio node
		else $video_audio_str = " ";
		
		$size_arg = $conv_params->width == -1 ?
			"" : // don't append anything if we don't want to affect the size
			" -s " . $conv_params->width ."x" . $conv_params->height; 
			
		$conversion_string = 		
			self::ne ( " -r " , $conv_params->framerate ) . 
			self::ne ( " -b " , $conv_params->bitrate ) . ( $conv_params->bitrate ? "k" : "" ) .  // make sure the integer is followed by the letter 'k'
			self::ne ( " -qscale " , $conv_params->qscale ) . 
			self::ne ( " -g " , $conv_params->gop_size ) .
			$size_arg .
			self::ne ( " -ab " , $conv_params->audio_bitrate ) . ( $conv_params->audio_bitrate ? "k" : "" ) . // make sure the integer is followed by the letter 'k'
			self::ne ( " -ar " ,  $conv_params->audio_sampling_rate ) .
			self::ne ( " -ac " , $conv_params->audio_channels  ).
			$video_audio_str .
			" " . $conv_params->ffmpeg_params . " " .  // extra params for ffmpeg if exist 

			" -y ";
						
		// I have commented out the audio parameters so we don't decrease the quality - it stays as-is
		$exec_cmd = self::getCmd() . " -i \"" . $conv_cmd->source_file ."\"".
			$conversion_string ." \"".
			$conv_cmd->getTargetFileWithSuffix( $params_index ) . "\"  2" . ">>"  . "\"{$conv_cmd->getLogFileWithSuffix ( $params_index ) }\"";

		return array ( $exec_cmd , $conversion_string )	;			
	}
}
?>