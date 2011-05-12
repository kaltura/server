<?php
/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
require_once ( "kalturaSystemAction.class.php" );

/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
class conversionProfileAction extends kalturaSystemAction
{
	public function execute()
	{
		$this->forceSystemAuthentication();
		
		myDbHelper::$use_alternative_con = null;
		$this->ok_to_save = $this->getP ("oktosave" );
		$conv_profile_id = $this->getP ( "convprofile_id" );
		if ( $conv_profile_id < 0 ) $conv_profile_id = "";
		
		$this->message = "";
		$this->display_disabled = $this->getP ( "display_disabled" );
		$command = $this->getP ( "command" );
		
		if ( $command == "removeCache" )
		{
		}
		elseif ( $command == "save"  )
		{
			$conv_profile = new ConversionProfile ();
			$wrapper = objectWrapperBase::getWrapperClass( $conv_profile , 0 );
			$extra_fields  = array ( "partnerId" ,"enabled" ); // add fields that cannot be updated using the API
			$allowed_params = array_merge ( $wrapper->getUpdateableFields() , $extra_fields );	

			$fields_modified = baseObjectUtils::fillObjectFromMap ( $_REQUEST , $conv_profile , "convprofile_" , $allowed_params , BasePeer::TYPE_PHPNAME , true );
			
			if ( $conv_profile_id ) // when exists $conv_profile_id - save
			{
				$conv_profile_from_db = ConversionProfilePeer::retrieveByPK( $conv_profile_id );
				if ( $conv_profile_from_db )
				{
					baseObjectUtils::fillObjectFromObject( $allowed_params , $conv_profile , $conv_profile_from_db , baseObjectUtils::CLONE_POLICY_PREFER_NEW , null , BasePeer::TYPE_PHPNAME , true );
				}
	
				$conv_profile_from_db->save();
			}
			else // when not exists $conv_profile_id - creaet new and return id
			{
				$conv_profile->save();
				$conv_profile_id = $conv_profile->getId();
			}
		}
		
		$this->conv_profile = ConversionProfilePeer::retrieveByPK( $conv_profile_id );
		$this->conv_profile_id= $conv_profile_id;
		if ( $this->conv_profile )
		{
			$this->conv_profile_type = $this->conv_profile->getProfileType();
			$this->fallback_mode = array();
			$this->conv_params_list = ConversionParamsPeer::retrieveByConversionProfile( $this->conv_profile , $this->fallback_mode , false  /*display_disable*/);
			// to see if there are any disabled params - call again with true 
			$tmp_fallback = array ();
			$tmp_conv_params_list = ConversionParamsPeer::retrieveByConversionProfile( $this->conv_profile , $tmp_fallback , true /*display_disable*/);
			if ( $tmp_fallback["mode"] == $this->fallback_mode["mode"] )
			{
				$this->fallback_mode = $tmp_fallback;
				$this->conv_params_list = $tmp_conv_params_list;
			}
			else
			{
				if ( $this->display_disabled )
				{
					$this->fallback_mode = $tmp_fallback;
					$this->conv_params_list = $tmp_conv_params_list;	
					$this->message = "This display is missleading due to [dispaly disabled=true]<br>It shows params that are disabled for this profile and WOULD NOT be used at run-time";				
				}
			}
		}
		else
		{
			$this->conv_profile_type = null;
			$this->conv_params_list = null;
		} 
		
	}
}
?>