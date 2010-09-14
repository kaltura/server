<?php

/**
 * Subclass for representing a row from the 'conversion_params' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ConversionParams extends BaseConversionParams
{
	const CONVERSION_PARAMS_CREATION_MODE_MANUAL = 1;
	const CONVERSION_PARAMS_CREATION_MODE_KMC = 2;
	const CONVERSION_PARAMS_CREATION_MODE_AUTOMATIC = 3;
	
	
	public function save(PropelPDO $con = null)
	{
		$this->setCustomDataObj();
		return parent::save ( $con ) ;		
	}
/*
 * 	public $video = true; 		// should attempt to convert with video
	public $audio = true; 		// should attempt to convert with audio
	public $ffmpeg_params = "";		// general params to append to the ffmpeg command in case the ffmpegEngine is used
	public $mencoder_params = "";	// general params to append to the mencoder command in case the mencoderEngine is used
	public $flix_params = "";	// general params to append to the flix command in case the flixEngine is used
 * 
 */	
	public function getVideo() { return $this->getFromCustomData( "video" , null , null ) ;} 
	public function setVideo( $v ) { return $this->putInCustomData( "video" , $v  , null ); }

	public function getAudio() { return $this->getFromCustomData( "audio" , null , null ) ;} 
	public function setAudio( $v ) { return $this->putInCustomData( "audio" , $v  , null ); }
	
	public function getFfmpegParams() { return $this->getFromCustomData( "ffmpegParams" , null , null ) ;} 
	public function setFfmpegParams( $v ) { return $this->putInCustomData( "ffmpegParams" , $v  , null ); }
	
	public function getMencoderParams() { return $this->getFromCustomData( "mencoderParams" , null , null ) ;} 
	public function setMencoderParams( $v ) { return $this->putInCustomData( "mencoderParams" , $v  , null ); }
		
	public function getFlixParams() { return $this->getFromCustomData( "flixParams" , null , null ) ;} 
	public function setFlixParams( $v ) { return $this->putInCustomData( "flixParams" , $v  , null ); }
	
	public function getCommercialTranscoder() { return $this->getFromCustomData( "commercialTranscoder" , null , null ) ;} 
	public function setCommercialTranscoder( $v ) { return $this->putInCustomData( "commercialTranscoder" , $v  , null ); }

	public function getFramerate() { return $this->getFromCustomData( "framerate" , null , null ) ;} 
	public function setFramerate( $v ) { return $this->putInCustomData( "framerate" , $v  , null ); }

	public function getAudioBitrate() { return $this->getFromCustomData( "audioBitrate" , null , null ) ;} 
	public function setAudioBitrate( $v ) { return $this->putInCustomData( "audioBitrate" , $v  , null ); }
	
	public function getAudioSamplingRate() { return $this->getFromCustomData( "audioSamplingRate" , null , null ) ;} 
	public function setAudioSamplingRate( $v ) { return $this->putInCustomData( "audioSamplingRate" , $v  , null ); }
	
	public function getAudioChannels() { return $this->getFromCustomData( "audioChannels" , null , null ) ;} 
	public function setAudioChannels( $v ) { return $this->putInCustomData( "audioChannels" , $v  , null ); }
}
