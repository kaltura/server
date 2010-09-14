<?php
require_once ( "kalturaSystemAction.class.php" );
class conversionParamsAction extends kalturaSystemAction
{
	public function execute()
	{
		$this->forceSystemAuthentication();
		
		myDbHelper::$use_alternative_con = null;

		$this->ok_to_save = $this->getP ("oktosave" );
		$this->error = "";
		
		$conv_params_id = $this->getP ( "convparams_id" );
		$command = $this->getP ( "command" );
		$this->close_after_save = $this->getP ( "close_after_save" );
		if ( $command == "removeCache" )
		{
		}
		elseif ( $command == "save" || $command == "fill" )
		{
			$conv_params = new ConversionParams ();
			$wrapper = objectWrapperBase::getWrapperClass( $conv_params , 0 );
			$extra_fields  = array ( "partnerId" , "ffmpegParams" , "mencoderParams" , "flixParams" ); // add fields that cannot be updated using the API
			$allowed_params = array_merge ( $wrapper->getUpdateableFields() , $extra_fields );	

			$fields_modified = baseObjectUtils::fillObjectFromMap ( $_REQUEST , $conv_params , "convparams_" , $allowed_params , BasePeer::TYPE_PHPNAME , true );
			
			if ( $command == "save" )
			{
				if ( $conv_params_id ) // when exists $conv_params_id - save
				{
					$conv_params_from_db = ConversionParamsPeer::retrieveByPK( $conv_params_id );
					if ( $conv_params_from_db )
					{
						baseObjectUtils::fillObjectFromObject( $allowed_params , $conv_params , $conv_params_from_db , baseObjectUtils::CLONE_POLICY_PREFER_NEW , null , BasePeer::TYPE_PHPNAME , true );
					}
		
					$conv_params_from_db->save();
				}
				else // when not exists $conv_params_id - creaet new and return id
				{
					$conv_params->save();
					$conv_params_id = $conv_params->getId();
				}
			}
		}
		if ( $command == "fill" )
		{
			// when in command fill - don't fetch object from db
			$this->conv_params = $conv_params ;
		}
		else
		{
			$this->conv_params = ConversionParamsPeer::retrieveByPK( $conv_params_id );
			if ( ! $this->conv_params ) 
			{
				$this->error = "Cannot find ConversionParams [$conv_params_id]"; 
			}
		}
		$this->conv_params_id= $conv_params_id;
		
		$this->simulation = null;
		// will help simulate the conversion strings:
		if ( $this->conv_params )
		{
			$conv_param_from_db  = $this->conv_params ;
			$conv_params = new kConversionParams();
			$conv_params->width = $conv_param_from_db->getName();
			$conv_params->width =$conv_param_from_db->getWidth() ;
			$conv_params->height =  $conv_param_from_db->getHeight() ;
			$conv_params->aspect_ratio = $conv_param_from_db->getAspectRatio() ;
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
						
			$conv_params_list = array();
			$conv_cmd = new kConversionCommand();
			$conv_params_list[] = $conv_params;
			$conv_cmd->conversion_params_list = $conv_params_list;
			$this->simulation = kConversionEngineMgr::simulate( $conv_cmd );
		}
		
		
	}
}

function TRACE ( $str )
{
	
}
?>