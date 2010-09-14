<?php
/**
 * Will hold data about a single conversion of a file.
 * This data will be passed to the actual kConversionEngine that will be incharge of using the data to create 
 * the best conversionString depending on the data and the real engine (ffmpeg/mencoder/flix) 
 * 
 * This class is VERY similar to the one from the DB ConversionProfile, but becase we serialize it and manipulate it's data
 * I chose to create a thinner class  
 */
class kConversionParams
{
	const CONV_PARAMS_ASPECT_RATIO_KEEP_ORIG_RATIO = 1;
	const CONV_PARAMS_ASPECT_RATIO_KEEP_ORIG_DIMENSIONS = 2;
	const CONV_PARAMS_ASPECT_RATIO_4_3 = 3;
	const CONV_PARAMS_ASPECT_RATIO_16_9 = 4;
	const CONV_PARAMS_ASPECT_RATIO_KEEP_HEIGHT = 5;
	const CONV_PARAMS_ASPECT_RATIO_IGNORE = 6; // will ignore the request and will take from the params
	
	public $id = "" ; 			// a string representing this set of params (can easily be pointed to by a kConversionProfile) 
	public $enabled  = true ;	// (0/1) - if false, this set of params will be ignored
	public $name = ""; 			// readable name of the set
	public $width ;
	public $height ;
	public $aspect_ratio ; // keep original , 4:3 , 16:9
	public $gop_size;
	public $bitrate ;
	public $qscale;	
	public $video = true; 		// should attempt to convert with video
	public $audio = true; 		// should attempt to convert with audio
	public $file_suffix = ""; 	// will be used together with the target from the kConversionCommand to create a good name for the conversion
	public $ffmpeg_params = "";		// general params to append to the ffmpeg command in case the ffmpegEngine is used
	public $mencoder_params = "";	// general params to append to the mencoder command in case the mencoderEngine is used
	public $flix_params = "";	// general params to append to the flix command in case the flixEngine is used
	public $desired_converter = "";
	public $comercial_transcoder = "";

	// added on 2009-05-25 - all parameters can be set - no hard coded settings !
	public $framerate ;
	public $audio_bitrate;
	public $audio_sampling_rate;
	public $audio_channels;
	
}
?>