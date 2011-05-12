<?php
// TODO - cleanup for the whole class
/**
 * Will help retrieve a ConversionProfile for an entry or a partner
 *  
 */
class myConversionProfileUtils
{
	/**
	 * return the flavor params the best fits the fileFormat for a given partner_id
	 * 
	 * @param int $partnerId
	 * @param string $fileFormat
	 * @return FlavorParams
	 */
	public static function getFlavorParamsFromFileFormat( $partnerId , $fileFormat, $ignoreSourceTag = true )
	{
		$defaultCriteria = flavorParamsPeer::getCriteriaFilter()->getFilter();
		$defaultCriteria->remove(flavorParamsPeer::PARTNER_ID);
		
//		flavorParamsPeer::allowAccessToPartner0AndPartnerX($partnerId); // the flavor params can be from partner 0 too
		$c = new Criteria();
		$c->addAnd ( flavorParamsPeer::PARTNER_ID , array ( $partnerId , 0 ) , Criteria::IN );
//		$c->add (  flavorParamsPeer::FORMAT , $fileFormat );
		$possible_flavor_params = flavorParamsPeer::doSelect( $c );
		flavorParamsPeer::setDefaultCriteriaFilter();
		
		$best_fp = null;
		
		foreach ( $possible_flavor_params as $fp )
		{
			if ( $fileFormat != $fp->getFormat() )
				continue;
				
			if ( $ignoreSourceTag && $fp->hasTag(flavorParams::TAG_SOURCE) )
				continue;
				
			if ( ! $best_fp ) 
				$best_fp =  $fp;
				
			if ( $fp->getPartnerId() != $partnerId )
				continue;
				
			// same format for the partner
			$best_fp =  $fp;
			break;
		}
		
		// if not fount any - choose the first flavor params from the list
		if ( ! $best_fp ) $best_fp = $possible_flavor_params[0];
		return $best_fp;
	}

	private static function calcHeight ( $width , $aspect_ratio )
	{
		if ( $aspect_ratio == 0 ) return $width;
		return  (int)( $width / $aspect_ratio );
	}
	
	private static function fixDimensionsByAspectRatio ( kConversionParams $conv_params, &$description )
	{
		// if should use "aspect_ratio" but have no width - act as if "original_size" 
		if (
				$conv_params->aspect_ratio == kConversionParams::CONV_PARAMS_ASPECT_RATIO_KEEP_ORIG_DIMENSIONS 
				|| 
			 	($conv_params->aspect_ratio == kConversionParams::CONV_PARAMS_ASPECT_RATIO_KEEP_ORIG_RATIO &&  $conv_params->width <= 0 ) 
			)
		{
			$description = 'Keeping original height';
			$conv_params->width = 0;
			$conv_params->height = 0; 
			return;
		}

		if ( $conv_params->aspect_ratio == kConversionParams::CONV_PARAMS_ASPECT_RATIO_IGNORE )
		{
			// use the width & heigth from the params - IGNORE all the external requests 
		}
		elseif ( $conv_params->aspect_ratio == "" && $conv_params->height > 0 && $conv_params->width > 0 )
		{
			// leave untouched 
		}
		
		// if the aspect_ratio implies to keep the height - see if the height is 0 or not... 
		elseif (
					$conv_params->height == 0 
					|| 
					( 	
						$conv_params->aspect_ratio != kConversionParams::CONV_PARAMS_ASPECT_RATIO_KEEP_HEIGHT 
						&&  
						$conv_params->aspect_ratio != kConversionParams::CONV_PARAMS_ASPECT_RATIO_KEEP_ORIG_DIMENSIONS 
					)
			) // can be empty , null or 0
		{
			if ( $conv_params->aspect_ratio == kConversionParams::CONV_PARAMS_ASPECT_RATIO_KEEP_ORIG_RATIO )
			{
				$conv_params->height = 0; 
			}
			elseif ( $conv_params->aspect_ratio == kConversionParams::CONV_PARAMS_ASPECT_RATIO_16_9 )
			{
				$conv_params->height = self::calcHeight ( $conv_params->width , ( 16/9 ) );
			}
		
			elseif( $conv_params->aspect_ratio == kConversionParams::CONV_PARAMS_ASPECT_RATIO_4_3 )
			{
				// default is CONV_PARAMS_ASPECT_RATIO_4_3
				$conv_params->height = self::calcHeight ( $conv_params->width , ( 4/3 ) );
			}			
		}
		
		
		// make sure the width and heigth are delimited by 16
		$modWidth = $conv_params->width % 16;
		if ( $modWidth != 0 ) 
		{
			$newWidth = $conv_params->width - $modWidth;
			$description = "Width changed from [$conv_params->width] to [$newWidth], since API V3";
			$conv_params->width = $newWidth;
		}
		
		$modHeight = $conv_params->height % 16;
		if ( $modHeight != 0 ) 
		{
			$newHeight = $conv_params->height - $modHeight;
			$description = "Height changed from [$conv_params->height] to [$newHeight], since API V3";
			$conv_params->height = $newHeight;
		}
		
		// if by the end of all the calculations - still 0 or smaller - set to hard-coded defaults...
		if ( $conv_params->width <= 0 )
			$conv_params->width = 0;
			
		if ( $conv_params->height <= 0 )
			$conv_params->height = 0;
	}
	
	/**
	 * Will return for each OLD ConversionProfile a new one, IF NEEDED.
	 * The old conversionProfile will hold a reference to the new one, so if already created a new one, it will be re-used.
	 * All the necessary flavorParams will be added too to fit the old conversion params.
	 *  
	 * @param ConversionProfile $conversion_profile
	 * @return conversionProfile2
	 */
	public static function createConversionProfile2FromConversionProfile ( ConversionProfile $old_conversion_profile  )
	{
		if ( !$old_conversion_profile )
		{
			throw new Exception ( "Cannot create new conversionProfile2 for null" );
		}
		
		if ( $old_conversion_profile->getConversionProfile2Id() )
		{
			$new_profile = conversionProfile2Peer::retrieveByPK(  $old_conversion_profile->getConversionProfile2Id() );
			if ( $new_profile )
			{
				// found a valid new profile - return it
				return $new_profile;
			}
		}
		// whether there was no id or no profile - create on now and set it to be the conversionProfile2Id
		$new_profile = new conversionProfile2();
		$new_profile->setPartnerId( $old_conversion_profile->getPartnerId() );
		$new_name = $old_conversion_profile->getName();
		$new_name =  $new_name ? $new_name : "From [{$old_conversion_profile->getId()}]";
		$new_profile->setName( $new_name . " " . $old_conversion_profile->getProfileType() );
		
		if ( $old_conversion_profile->getBypassFlv() )
		{
			$new_profile->setCreationMode ( conversionProfile2::CONVERSION_PROFILE_2_CREATION_MODE_AUTOMATIC_BYPASS_FLV );
			$map = flavorParams::TAG_WEB . "," . flavorParams::TAG_MBR;
		}
		else
		{
			$new_profile->setCreationMode ( conversionProfile2::CONVERSION_PROFILE_2_CREATION_MODE_AUTOMATIC );			
			$map = flavorParams::TAG_WEB;
		} 		
		$new_profile->setInputTagsMap($map);
		
		// use the OLD code to simulate what was performed on the old_conversion_profile to retrieve the old_conversion_params list		
		$conv_client = new kConversionClient ( "" , "" , "" , "" ); 
 
		$old_conversion_command = $conv_client->createConversionCommandFromConverionProfile ( "src" , "target" , $old_conversion_profile );
		$description = ''; 
		foreach ( $old_conversion_command->conversion_params_list as $old_conversion_params )
		{
			// use the helper utility to fill the gaps
			$desc = ''; 
			self::fixDimensionsByAspectRatio ( $old_conversion_params, $desc );
			$description .= $desc;
		} 
		
		$new_profile->setDescription($description);
		$new_profile->save();
		
		// at this point - the all $old_conversion_params are filled with the values used by the old conversion servers
		// transform from old to new ...  
		
		// create the flavorParams and the flavorParamsConversionParams table 
		foreach ( $old_conversion_command->conversion_params_list as $old_conversion_params )
		{
			$new_flavor_params = new flavorParams();
			// set all the properties for the new flavor_params
			$new_flavor_params->setPartnerId ( $old_conversion_profile->getPartnerId() );
			$new_flavor_params->setCreationMode ( flavorParams::FLAVOR_PARAMS_CREATION_MODE_AUTOMATIC );
			
			$audio_bitrate = $old_conversion_params->audio_bitrate;
			if ( !$audio_bitrate )  $audio_bitrate = 96; // if empty - hard-code 96
			$new_flavor_params->setAudioBitrate( $audio_bitrate ); // default
			$new_flavor_params->setAudioChannels ( 0 ); // default
			$new_flavor_params->setAudioResolution( 0);
			$new_flavor_params->setAudioSampleRate( 0 );
			if ( $old_conversion_profile->getCommercialTranscoder() ) // this should be done according to the profile AND NOT the params
			{
				// first comes ON2...
				$new_flavor_params->setConversionEngines( conversionEngineType::ON2 . "," 
					. conversionEngineType::ENCODING_COM . "," 
					. conversionEngineType::FFMPEG . "," 
					. conversionEngineType::FFMPEG_AUX . "," 
					. conversionEngineType::MENCODER ); //
				$new_flavor_params->setConversionEnginesExtraParams( $old_conversion_params->flix_params . "|" 
					. $old_conversion_params->flix_params . "|" 
					. $old_conversion_params->ffmpeg_params . "|" 
					. $old_conversion_params->ffmpeg_params . "|"
					. $old_conversion_params->mencoder_params );
			}
			else
			{
				// first comes ffmpeg ... 
				 $new_flavor_params->setConversionEngines( conversionEngineType::FFMPEG . "," 
				 	. conversionEngineType::FFMPEG_AUX . "," 
				 	. conversionEngineType::MENCODER . "," 
				 	. conversionEngineType::ON2 . "," 
				 	. conversionEngineType::ENCODING_COM, "," ); //
				 $new_flavor_params->setConversionEnginesExtraParams( $old_conversion_params->ffmpeg_params . "|"
				 	. $old_conversion_params->ffmpeg_params . "|"  
				 	. $old_conversion_params->mencoder_params . "|" 
				 	. $old_conversion_params->flix_params . "|" 
				 	. $old_conversion_params->flix_params );
			}
			
			$target_format = "flv" ; // this code will always be called for flv files
			// the format can be flv | mp4 | mov | avi | mp3
			// IMPORTANT: 
			// except for the FLV videos, none of the formats should be assumed WEB - they are not supposed to be played using our player at first stage.
			switch (  $target_format  )
			{
				case "mp3":
					$new_flavor_params->setFormat( "flv" ) ;
					$new_flavor_params->setAudioCodec( flavorParams::AUDIO_CODEC_MP3 ); // set default mp3
					$new_flavor_params->setVideoCodec( flavorParams::VIDEO_CODEC_VP6 );
					/* $new_flavor_params->setTags ( flavorParams::TAG_WEB ); */
					break;
				case "mp4":
					$new_flavor_params->setFormat( $target_format  ) ;
					$new_flavor_params->setAudioCodec( flavorParams::AUDIO_CODEC_AAC );
					$new_flavor_params->setVideoCodec( flavorParams::VIDEO_CODEC_H264 );					
					$new_flavor_params->setTags ( /*flavorParams::TAG_WEB .*/ ',mp4_export' ); 
					break;
				case "mov":
					$new_flavor_params->setFormat( $target_format  ) ;
					$new_flavor_params->setAudioCodec( flavorParams::AUDIO_CODEC_AAC );
					$new_flavor_params->setVideoCodec( flavorParams::VIDEO_CODEC_H264 );					
					$new_flavor_params->setTags ( 'mov_export' ); 
					break;
				case "avi":
					$new_flavor_params->setFormat( $target_format  ) ;
					$new_flavor_params->setAudioCodec( flavorParams::AUDIO_CODEC_MP3 );
					$new_flavor_params->setVideoCodec( flavorParams::VIDEO_CODEC_H264 );					
					$new_flavor_params->setTags ( 'avi_export' ); 
					break;
				case "flv":
					$new_flavor_params->setFormat( $target_format  ) ;
					$new_flavor_params->setAudioCodec( flavorParams::AUDIO_CODEC_MP3  );
					$new_flavor_params->setVideoCodec( flavorParams::VIDEO_CODEC_VP6 );					
					$new_flavor_params->setTags ( flavorParams::TAG_WEB . "," . flavorParams::TAG_MBR ); 
					break;
			}
			
			$new_flavor_params->setName ( $new_name ) ;
			
			$new_flavor_params->setFrameRate( 0 ) ; // DONT set the framerate $old_conversion_params->framerate );
			
			if($old_conversion_params->gop_size == 5) // editable
			{
				$new_flavor_params->setGopSize(5);
				$new_flavor_params->removeTag(flavorParams::TAG_MBR);
				$new_flavor_params->addTag(flavorParams::TAG_EDIT);
			}
			else
			{
				$new_flavor_params->setGopSize(0); // 0 will automatically allow default gopsize
			}  

			$new_flavor_params->setWidth( $old_conversion_params->width );
			$new_flavor_params->setHeight( $old_conversion_params->height );
			
			$new_flavor_params->setVersion( 1 );
			$new_flavor_params->setReadyBehavior( flavorParamsConversionProfile::READY_BEHAVIOR_OPTIONAL );
			$new_flavor_params->setVideoBitrate( $old_conversion_params->bitrate ? $old_conversion_params->bitrate : "" );
			
			
			// TODO - fill the rest ... 
			$new_flavor_params->save();
			
			// add to the 1-to-many table
			$flavor_params_conversion_profile = new flavorParamsConversionProfile();
			$flavor_params_conversion_profile->setConversionProfileId( $new_profile->getId() );
			$flavor_params_conversion_profile->setFlavorParamsId( $new_flavor_params->getId() );
			$flavor_params_conversion_profile->setReadyBehavior( $new_flavor_params->getReadyBehavior() );
			
			$flavor_params_conversion_profile->save();
		} 

		
		// always add to the *source* flavotParams to the 1-to-many table
		$flavor_params_conversion_profile = new flavorParamsConversionProfile();
		$flavor_params_conversion_profile->setConversionProfileId( $new_profile->getId() );
		$flavor_params_conversion_profile->setFlavorParamsId( flavorParams::SOURCE_FLAVOR_ID );
		$flavor_params_conversion_profile->save();
		
		// point to the new profile and save the old one
		$old_conversion_profile->setConversionProfile2Id ( $new_profile->getId() ) ;
		$old_conversion_profile->save();
		
		return $new_profile;
	}

	
	// TODO - order please !! 	
	// what is the $conversion_profile_quality - id or type ??
	// for now it can be both
	public static function getConversionProfile ( $partner_id , $conversion_profile_quality )
	{
		if ( $conversion_profile_quality == ConversionProfile::CONVERSION_PROFILE_UNKNOWN ||
			 $conversion_profile_quality == null )
		{
			// in this case there is no explicit profile_id or profile_type - we need to select the best one from the partner
			return ConversionProfilePeer::retrieveByPartner ($partner_id  );
		}
		elseif ( is_numeric( $conversion_profile_quality ) )
		{
			return self::getConversionProfileById ( $partner_id , $conversion_profile_quality  );
		}
		else
		{
			return self::getConversionProfileByType ($partner_id , $conversion_profile_quality  );
		}		
 	}
	
	
	// return the conversion profile either if it belongs to  GLOBAL_PARTNER_PROFILE or to the curent partner_id
	public static function getConversionProfileByType ( $partner_id , $conversion_profile_type )
	{
		if ( $conversion_profile_type === null ) return null;
		
		return ConversionProfilePeer::retrieveByProfileType( $partner_id , $conversion_profile_type );
	}

	// return the conversion profile either if it belongs to  GLOBAL_PARTNER_PROFILE or to the curent partner_id
	public static function getConversionProfileById ( $partner_id , $conversion_profile_id )
	{
		if ( $conversion_profile_id === null ) return null;
		
		$conv_prof = ConversionProfilePeer::retrieveByPK( $conversion_profile_id );
		if ( $conv_prof )
		{
			if ( $conv_prof->getPartnerId() == $partner_id || $conv_prof->getPartnerId() == ConversionProfile::GLOBAL_PARTNER_PROFILE )
				return $conv_prof;
		}
		return null;
	}
	
	// TODO - attempt to create profile from $partner_convrsion_string and $partner_flv_conversion_string ??
	public static function getConversionProfileForPartner ( $partner_id , $partner_convrsion_string = null ,  $partner_flv_conversion_string = mull )
	{
		$conv_prof = ConversionProfilePeer::retrieveByPK( $conversion_profile_id );
	}
}

?>