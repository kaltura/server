<?php
/**
 * @package api
 * @subpackage ps2
 */
class addconversionprofileAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "addConversionProfile",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"conversionProfile" => array ("type" => "ConversionProfile", "desc" => "")
						),
					"optional" => array (
						)
					),
				"out" => array (
					"conversionProfile" => array ("type" => "ConversionProfile", "desc" => "")
					),
				"errors" => array (
				)
			); 
	}
 
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		// get the new properties for the ConversionProfile from the request
		$conv_profile = new ConversionProfile();
		
		$obj_wrapper = objectWrapperBase::getWrapperClass( $conv_profile , 0 );
		
		$fields_modified = baseObjectUtils::fillObjectFromMap ( $this->getInputParams() , $conv_profile , "conversionProfile_" , 
			$obj_wrapper->getUpdateableFields() , BasePeer::TYPE_PHPNAME , true  );
		// check that mandatory fields were set
		if ( count ( $fields_modified ) > 0 )
		{
			// search if partner already has a conversionProfile similar to the one just added - if exists - use it
			// if not - create it and return it. 
			$partner_conv_profile = ConversionProfilePeer::retrieveSimilar( $partner_id , $conv_profile );
			if ( ! $partner_conv_profile )
			{
				$conv_profile->setPartnerId( $partner_id );
				$conv_profile->setEnabled (1);
				$conv_profile->save();
				
				$partner_conv_profile = $conv_profile;
			}		

// TODO - remove - no need to playaround with the updatedAt for ordering (in the listconversionprofiles service 			
//			$partner_conv_profile->setUpdatedAt( time() );
//			$partner_conv_profile->save();
			
			$partner = $this->getPartner();
			$partner_current_conversion_profile = $partner->getCurrentConversionProfileType();

			if ( $partner_conv_profile->getId() != $partner_current_conversion_profile )
			{
				$partner->setCurrentConversionProfileType( $partner_conv_profile->getId() );
				$partner->save();
			}
			
			$this->addMsg ( "conversionProfile" , objectWrapperBase::getWrapperClass( $partner_conv_profile , objectWrapperBase::DETAIL_LEVEL_DETAILED) );
			$this->addDebug ( "added_fields" , $fields_modified );
			
		}
		else
		{
			$this->addError( APIErrors::NO_FIELDS_SET_FOR_CONVERSION_PROFILE );
		}
		

	}
}
?>