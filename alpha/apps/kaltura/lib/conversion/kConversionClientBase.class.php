<?php
/**
 * Will be incharge of 
 * 1. envoking one of the kConversionServers by setting kConversionCommand in the correct place 
 * 	on disk in a specific directory or in the DB to be fetched by a direct query or dedicated service (phase 2).
 * 2. fetching the kConversionResult from the server (depending on the server's command-result mechanism).
 * Each Client can be triggered by different events in the system and update status of objects accordingly.
 * 
 * @package Core
 * @subpackage Conversion
 * @deprecated
 */
class kConversionClientBase extends myBatchBase
{
	public $in_path ;//, $out_path;
	protected $server_cmd_path , $server_res_path ;
	protected $commercial_server_cmd_path; 
	protected $client_id;
	
	protected $mode ; 
	
	/**
	 * @var kConversionCommand
	 */
	protected $conv_cmd;


	public function __construct ( $script_name ,  $in_path , $server_cmd_path , $server_res_path , $commercial_server_cmd_path = null , $mode = 3 )
	{
		$this->script_name = $script_name;
		if ( $script_name )	$this->register( $script_name , $in_path , $server_res_path , $mode );
		
		$this->in_path = realpath($in_path);
//		$this->out_path = realpath($out_path);
		$this->server_cmd_path = realpath($server_cmd_path);	
		$this->server_res_path = realpath($server_res_path);
		$this->commercial_server_cmd_path = realpath ( $commercial_server_cmd_path );
		KalturaLog::debug ( "------------------- kConversionClient [$mode]----------------------");
		KalturaLog::debug ( "--- in_path: [" . $this->in_path . "] ---" );
		KalturaLog::debug ( "--- server_cmd_path: [" . $this->server_cmd_path . "] ---" );
		KalturaLog::debug ( "--- server_res_path: [" . $this->server_res_path . "] ---" );
		KalturaLog::debug ( "--- commercial_server_cmd_path: [" . $this->commercial_server_cmd_path . "] ---" );
		
		$this->mode = $mode;
//echo "<br>".__METHOD__ .":[$in_path][$server_cmd_path][$server_res_path]<br>"; 		
//echo "<br>".__METHOD__ .":[$this->in_path][$this->server_cmd_path][$this->server_res_path]<br>";
	}

	// TODO - this will determine if flv + bypass transcoding...
	public function createConversionCommandFromConverionProfile ( $source_file , $target_file , $conv_profile , $entry = null  )
	{
		$conv_cmd = new kConversionCommand();
		$conv_cmd->source_file = $source_file;
		$conv_cmd->target_file = $target_file ;
		$conv_cmd->result_path = $this->server_res_path; // in the command itself - set the result path
		$conv_cmd->entry_id = $entry ? $entry->getId() : null ; // can be null - in this case it might be a conversion not related to a specific entry

		if ( $conv_profile == null )
		{
			throw new kConversionException ( "Cannot convert [$source_file] using a null ConversionProfile" );
		}
		
		KalturaLog::debug ( "ConversionProfile: " . print_r ( $conv_profile , true ));
		
		$fallback_mode = array();
		$conv_params_list_from_db = $conv_profile->getConversionParams( $fallback_mode );
		
		KalturaLog::debug ( "ConversionParams chosen by fallback_mode [" . print_r ( $fallback_mode, true ) . "]" );
		
		if ( ! $conv_params_list_from_db || count ( $conv_params_list_from_db ) == 0 )
		{
			throw new kConversionException( "ConversionProfile [" .$conv_profile->getId() . "] has no ConversionParams");
		}
		
		$conv_cmd->commercial_transcoder = $conv_profile->getCommercialTranscoder();
 
		$conv_params_list = array ( );
		foreach ( $conv_params_list_from_db as $conv_param_from_db )
		{
			if ( ! $conv_param_from_db->getEnabled() ) 
			{
				continue;
			}
			
			// TODO - for now override properties from the ConvProf over the ConvParams...
			// width , height & aspect ratio.
			// wherever we'll have more properties to override, we should use a ConvParams object for the profile and merge the 2 objects 
			// copy the relevan parameters to the kConversionParams from the ConversioParams 
//			$conv_param_from_db  = new ConversionParams; 
			$conv_params = new kConversionParams();
			$conv_params->enable = $conv_param_from_db->getEnabled();
			if ( $entry )
			{
				$conv_params->audio = $conv_param_from_db->getAudio();
				$conv_params->video = $entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_VIDEO ;  // expect video only when a video type
			}
			else
			{
				$conv_params->audio = $conv_param_from_db->getAudio();
				$conv_params->video = $conv_param_from_db->getVideo();
			}
			
			$conv_params->width = $conv_param_from_db->getName();
			$conv_params->width = self::choose ( $conv_profile->getWidth() , $conv_param_from_db->getWidth() );
			$conv_params->height = self::choose ( $conv_profile->getHeight() , $conv_param_from_db->getHeight() );
			$conv_params->aspect_ratio = self::choose ( $conv_profile->getAspectRatio() , $conv_param_from_db->getAspectRatio() );
			$conv_params->gop_size = $conv_param_from_db->getGopSize();
			$conv_params->bitrate = $conv_param_from_db->getBitrate();
			$conv_params->qscale = $conv_param_from_db->getQscale();
			$conv_params->file_suffix = $conv_param_from_db->getFileSuffix();
			$conv_params->ffmpeg_params = $conv_param_from_db->getFfmpegParams();
			$conv_params->mencoder_params = $conv_param_from_db->getMencoderParams();
			$conv_params->flix_params = $conv_param_from_db->getFlixParams();
			$conv_params->comercial_transcoder = $conv_param_from_db->getCommercialTranscoder(); // is not really used today per ConvParams
			$conv_params->framerate = $conv_param_from_db->getFramerate();
			$conv_params->audio_bitrate = $conv_param_from_db->getAudioBitrate();
			$conv_params->audio_sampling_rate = $conv_param_from_db->getAudioSamplingRate();
			$conv_params->audio_channels = $conv_param_from_db->getAudioChannels();			
			// TODO - move this to the server, fillConversionParams requires ffmpeg to determine the dimensions of the video 
			// for ascpet ration 
//			kConversionHelper::fillConversionParams ( $source_file , $conv_params );
			$conv_params_list[] = $conv_params;
		}
		if($conv_profile->getPartnerId() == 38050 || $conv_profile->getPartnerId() == 27121)
		{
			$conv_cmd->forceOn2 = true;
		}		
		$conv_cmd->conversion_params_list = $conv_params_list;
		$conv_cmd->log_file = $conv_cmd->target_file . ".log";
		
		$this->conv_cmd = $conv_cmd;

		return $conv_cmd;
	}
	
	protected static function choose ( $opt1 , $opt2 )
	{
		if ( $opt1 ) return $opt1;
		return $opt2; 
	}
}
?>
